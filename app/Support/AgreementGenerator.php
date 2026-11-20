<?php

namespace App\Support;

use App\Models\Agreement;
use App\Models\AgriculturalProject;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Generate Surat Perjanjian Kerja Sama Garap Lahan (Pemilik <-> Perusahaan).
 *
 * Data perusahaan & pola nomor surat diatur di config/perjanjian.php.
 * NIK & pekerjaan pemilik bersifat opsional: barisnya hanya dicetak
 * di surat bila terisi di data pemilik.
 */
class AgreementGenerator
{
    /** Nomor surat berikutnya untuk tanggal tertentu, mengikuti pola di config. */
    public static function nextNumber(?Carbon $date = null): string
    {
        $date ??= now();

        $sequence = Agreement::whereYear('agreement_date', $date->year)->count() + 1;

        return self::formatNumber($sequence, $date);
    }

    public static function formatNumber(int $sequence, Carbon $date): string
    {
        $romans = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        return preg_replace_callback('/\{seq(?::(\d+))?\}|\{roman_month\}|\{month\}|\{year\}/', function ($m) use ($sequence, $date, $romans) {
            return match (true) {
                str_starts_with($m[0], '{seq') => isset($m[1]) && $m[1] !== ''
                    ? str_pad((string) $sequence, (int) $m[1], '0', STR_PAD_LEFT)
                    : (string) $sequence,
                $m[0] === '{roman_month}' => $romans[$date->month],
                $m[0] === '{month}' => $date->format('m'),
                default => $date->format('Y'),
            };
        }, (string) config('perjanjian.number_format'));
    }

    /**
     * Data surat dari proyek + daftar cek kelengkapannya.
     * Setiap item checklist: label, value (tampilan), filled (bool).
     */
    public static function letterData(AgriculturalProject $project): array
    {
        $project->loadMissing(['owner.province', 'owner.regency', 'owner.district', 'owner.village']);
        $owner = $project->owner;

        $place = collect([
            $owner?->village?->nama,
            $owner?->district ? 'Kec. '.$owner->district->nama : null,
            $owner?->regency?->nama,
        ])->filter()->implode(', ');

        $areaM2 = (float) $project->area_size * 10000;
        $areaText = '± '.number_format($areaM2, fmod($areaM2, 1) === 0.0 ? 0 : 2, ',', '.').' m²';

        $locationLine = $project->location.($project->coordinates ? ' (koordinat: '.$project->coordinates.')' : '');

        $cropLine = $project->crop_type.($project->crop_variety ? ' ('.$project->crop_variety.')' : '');

        $data = [
            'company_name' => (string) config('perjanjian.company_name'),
            'company_address' => (string) config('perjanjian.company_address'),
            'signing_place' => $place !== '' ? $place : ($owner?->address ?? ''),
            'owner_name' => $owner?->name ?? '',
            'owner_nik' => $owner?->nik,
            'owner_occupation' => $owner?->occupation,
            'owner_address' => $owner?->address ?? '',
            'location_line' => $locationLine,
            'area_text' => $areaText,
            'crop_line' => $cropLine,
            'start_date' => $project->start_date,
            'end_date' => $project->estimated_harvest_date,
            'company_percent' => (int) $project->profit_share_company_percent,
            'owner_percent' => 100 - (int) $project->profit_share_company_percent,
        ];

        $checklist = [
            ['label' => 'Nama Pemilik Lahan', 'value' => $data['owner_name'], 'filled' => filled($data['owner_name'])],
            ['label' => 'NIK Pemilik (opsional — kosong = tidak tampil di surat)', 'value' => $data['owner_nik'] ?: '— tidak diisi —', 'filled' => true, 'optional' => true, 'present' => filled($data['owner_nik'])],
            ['label' => 'Pekerjaan Pemilik (opsional — kosong = tidak tampil di surat)', 'value' => $data['owner_occupation'] ?: '— tidak diisi —', 'filled' => true, 'optional' => true, 'present' => filled($data['owner_occupation'])],
            ['label' => 'Alamat Lengkap Pemilik', 'value' => $data['owner_address'], 'filled' => filled($data['owner_address'])],
            ['label' => 'Tempat Penandatanganan (dari wilayah alamat pemilik)', 'value' => $data['signing_place'], 'filled' => filled($data['signing_place'])],
            ['label' => 'Lokasi Lahan & Koordinat', 'value' => $data['location_line'], 'filled' => filled($project->location), 'optional' => ! filled($project->coordinates), 'present' => filled($project->coordinates)],
            ['label' => 'Luas Lahan (ditampilkan dalam m²)', 'value' => $areaText, 'filled' => (float) $project->area_size > 0],
            ['label' => 'Komoditas', 'value' => $cropLine, 'filled' => filled($project->crop_type)],
            ['label' => 'Tanggal Mulai', 'value' => $project->start_date?->format('d-m-Y') ?? '', 'filled' => filled($project->start_date)],
            ['label' => 'Tanggal Akhir (estimasi panen)', 'value' => $project->estimated_harvest_date?->format('d-m-Y') ?? '', 'filled' => filled($project->estimated_harvest_date)],
            ['label' => 'Bagi Hasil — Pemilik / Perusahaan', 'value' => $data['owner_percent'].'% / '.$data['company_percent'].'%', 'filled' => true],
        ];

        $data['checklist'] = $checklist;
        $data['complete'] = collect($checklist)->every(fn ($item) => $item['filled']);

        return $data;
    }

    /**
     * Generate nomor + PDF, simpan file & catat ke tabel agreements.
     * Satu proyek hanya punya satu surat (nomor tidak berubah).
     */
    public static function generate(AgriculturalProject $project, string $witness1, string $witness2, User $actor, ?Carbon $date = null): Agreement
    {
        $date ??= now();

        $number = self::nextNumber($date);
        $data = self::letterData($project);
        $data['number'] = $number;
        $data['agreement_date'] = $date;
        $data['witness1_name'] = $witness1;
        $data['witness2_name'] = $witness2;

        $pdf = Pdf::loadView('pdf.agreement', $data)
            ->setPaper('a4')
            ->setOption(['defaultFont' => 'times', 'isRemoteEnabled' => false]);

        $path = 'agreements/surat-'.$project->id.'-'.preg_replace('/[^A-Za-z0-9]+/', '-', $number).'.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        return Agreement::create([
            'project_id' => $project->id,
            'number' => $number,
            'witness1_name' => $witness1,
            'witness2_name' => $witness2,
            'agreement_date' => $date->toDateString(),
            'pdf_path' => $path,
            'created_by' => $actor->id,
        ]);
    }
}
