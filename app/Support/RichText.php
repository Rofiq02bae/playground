<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Sanitasi HTML rich text dari block editor (heading, paragraf, kutipan).
 * Hanya formatting inline dasar yang diizinkan: bold, italic, underline,
 * kode inline, link http/https/mailto, dan line break. Semua tag lain,
 * atribut event, dan skema berbahaya dibuang.
 */
class RichText
{
    public static function sanitize(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $clean = strip_tags($html, '<strong><b><em><i><u><a><code><br>');

        // Buang semua atribut kecuali href pada <a> yang aman.
        $clean = preg_replace_callback('/<a\s+([^>]*)>/i', function (array $m) {
            if (preg_match('/href\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $m[1], $hrefMatch)) {
                $href = $hrefMatch[2] !== '' ? $hrefMatch[2] : ($hrefMatch[3] ?? '');
                if (Str::startsWith($href, ['http://', 'https://', 'mailto:'])) {
                    return '<a href="'.e($href).'" target="_blank" rel="noopener noreferrer">';
                }
            }

            return '<a>';
        }, $clean) ?? $clean;

        // Tag inline lain tidak boleh membawa atribut apa pun.
        $clean = preg_replace('/<(strong|b|em|i|u|code)\s+[^>]*>/i', '<$1>', $clean) ?? $clean;
        $clean = preg_replace('/<br\s*\/?>/i', '<br>', $clean) ?? $clean;

        return trim($clean);
    }

    /** Versi teks polos (untuk kolom content, pencarian, meta). */
    public static function plain(?string $html): string
    {
        $text = str_replace(['<br>', '<br/>', '<br />'], "\n", (string) $html);

        return trim(html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8'));
    }
}
