<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    @page { size: A4; margin: 2.5cm 2.5cm 2.5cm 2.5cm; }
    body { font-family: 'Times New Roman', 'Times', serif; font-size: 12pt; line-height: 1.5; text-align: justify; color: #000; }
    .title { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0 0 2pt 0; }
    .number { text-align: center; margin: 0 0 14pt 0; }
    p { margin: 0 0 8pt 0; }
    .party { margin: 0 0 4pt 0; }
    .party table { width: 100%; border-collapse: collapse; }
    .party td { vertical-align: top; padding: 0; text-align: left; }
    .party td.k { width: 90pt; }
    .pasal { font-weight: bold; margin: 12pt 0 4pt 0; text-align: left; }
    ol { margin: 0 0 8pt 0; padding-left: 18pt; }
    li { margin-bottom: 4pt; }
    ul { margin: 0 0 8pt 0; padding-left: 18pt; }
    .signatures { width: 100%; border-collapse: collapse; margin-top: 18pt; page-break-inside: avoid; }
    .signatures td { width: 50%; vertical-align: top; text-align: center; padding: 0 6pt; }
    .sign-space { height: 70pt; }
    .sign-name { font-weight: bold; text-decoration: underline; }
    .witnesses { margin-top: 16pt; text-align: left; }
    .witnesses p { margin-bottom: 14pt; }
</style>
</head>
<body>
@php
    $fmtDate = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale('id')->translatedFormat('d F Y') : '';
    $dayName = \Carbon\Carbon::parse($agreement_date)->locale('id')->translatedFormat('l');
    $dateLong = \Carbon\Carbon::parse($agreement_date)->locale('id')->translatedFormat('d F Y');
@endphp

<p class="title">Surat Perjanjian Kerja Sama Garap Lahan Pertanian</p>
<p class="number">Nomor: {{ $number }}</p>

<p>Pada hari ini, {{ $dayName }}, tanggal {{ $dateLong }}, bertempat di {{ $signing_place }}, kami yang bertanda tangan di bawah ini:</p>

<div class="party">
    <table>
        <tr><td class="k">1. Nama</td><td>: <strong>{{ $owner_name }}</strong></td></tr>
        @if (filled($owner_nik))
            <tr><td class="k">NIK</td><td>: {{ $owner_nik }}</td></tr>
        @endif
        @if (filled($owner_occupation))
            <tr><td class="k">Pekerjaan</td><td>: {{ $owner_occupation }}</td></tr>
        @endif
        <tr><td class="k">Alamat</td><td>: {{ $owner_address }}</td></tr>
    </table>
    <p>Dalam hal ini bertindak sebagai pemilik sah atas bidang tanah pertanian, yang selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Pemilik Lahan)</strong>.</p>
</div>

<div class="party">
    <table>
        <tr><td class="k">2. Nama</td><td>: <strong>{{ $company_name }}</strong></td></tr>
        <tr><td class="k">Alamat</td><td>: {{ $company_address }}</td></tr>
    </table>
    <p>Dalam hal ini bertindak sebagai pengelola, yang selanjutnya disebut sebagai <strong>PIHAK KEDUA (Perusahaan)</strong>.</p>
</div>

<p><strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> (secara bersama-sama disebut <strong>PARA PIHAK</strong>) sepakat untuk mengikatkan diri dalam perjanjian kerja sama pengelolaan lahan pertanian dengan ketentuan sebagai berikut:</p>

<p class="pasal">Pasal 1: Objek Lahan Pertanian</p>
<p>PIHAK PERTAMA menyerahkan hak pengelolaan lahan pertanian kepada PIHAK KEDUA dengan rincian sebagai berikut:</p>
<ul>
    <li><strong>Lokasi Lahan</strong>: {{ $location_line }}</li>
    <li><strong>Luas Lahan</strong>: {{ $area_text }}</li>
</ul>

<p class="pasal">Pasal 2: Jenis Tanaman dan Jangka Waktu</p>
<ol>
    <li>Lahan tersebut akan ditanami komoditas {{ $crop_line }}.</li>
    <li>Perjanjian ini berlaku untuk jangka waktu terhitung sejak tanggal {{ $fmtDate($start_date) }} sampai dengan {{ $fmtDate($end_date) }}.</li>
    <li>Perpanjangan masa garap wajib disepakati secara tertulis oleh PARA PIHAK selambat-lambatnya 14 (empat belas) hari sebelum masa berlaku habis.</li>
</ol>

<p class="pasal">Pasal 3: Sistem Bagi Hasil Panen</p>
<ol>
    <li>Pembagian hasil dilakukan setiap kali masa panen tiba, setelah dikurangi biaya-biaya bersama yang disepakati (jika ada).</li>
    <li>Persentase pembagian hasil panen ditetapkan sebesar <strong>{{ $owner_percent }}% untuk PIHAK PERTAMA</strong> dan <strong>{{ $company_percent }}% untuk PIHAK KEDUA</strong>.</li>
    <li>Pembagian hasil diserahkan dalam bentuk uang selambat-lambatnya 3 hari setelah proses penjualan selesai.</li>
</ol>

<p class="pasal">Pasal 4: Risiko Gagal Panen (<em>Force Majeure</em>)</p>
<p>Apabila terjadi gagal panen akibat bencana alam, serangan hama berat, kekeringan ekstrem, atau banjir di luar kendali manusia, maka kerugian ditanggung bersama oleh PARA PIHAK sesuai porsi modal/tenaga yang telah dikeluarkan, dan tidak ada tuntutan ganti rugi di antara PARA PIHAK.</p>

<p class="pasal">Pasal 5: Penyelesaian Perselisihan</p>
<p>Apabila timbul perselisihan dalam pelaksanaan perjanjian ini, PARA PIHAK sepakat menyelesaikannya secara musyawarah mufakat dengan melibatkan perangkat desa/kelurahan setempat sebagai mediator.</p>

<p class="pasal">Pasal 6: Penutup</p>
<p>Surat perjanjian ini dibuat dalam rangkap 2 (dua) bermeterai cukup, ditandatangani oleh PARA PIHAK dan saksi-saksi dalam keadaan sadar tanpa paksaan, serta memiliki kekuatan hukum yang sama.</p>

<table class="signatures">
    <tr>
        <td><strong>PIHAK PERTAMA (Pemilik Lahan)</strong></td>
        <td><strong>PIHAK KEDUA (Perusahaan)</strong></td>
    </tr>
    <tr>
        <td><em>(Meterai Rp10.000 &amp; Tanda Tangan)</em></td>
        <td><em>(Tanda Tangan)</em></td>
    </tr>
    <tr><td class="sign-space"></td><td class="sign-space"></td></tr>
    <tr>
        <td><span class="sign-name">{{ $owner_name }}</span></td>
        <td><span class="sign-name">{{ $company_name }}</span></td>
    </tr>
</table>

<div class="witnesses">
    <p><strong>Saksi-Saksi:</strong></p>
    <p>1. <strong>{{ $witness1_name }}</strong> (Perangkat Desa / Ketua Kelompok Tani) : ......................................</p>
    <p>2. <strong>{{ $witness2_name }}</strong> (Keluarga / Tetangga Batas Lahan) : ......................................</p>
</div>

</body>
</html>
