<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Surat Perjanjian Kerja Sama Garap Lahan
|--------------------------------------------------------------------------
|
| UBAH DATA PERUSAHAAN & FORMAT NOMOR SURAT DI FILE INI.
|
| - company_name / company_address: identitas PIHAK KEDUA (perusahaan)
|   yang tercetak di surat perjanjian.
| - number_format: pola nomor surat. Placeholder yang tersedia:
|     {seq}         nomor urut per tahun, tanpa padding (1, 2, 3, ...)
|     {seq:3}       nomor urut dengan padding nol 3 digit (001, 002, ...)
|     {roman_month} bulan Romawi tanggal surat (I, II, ..., XII)
|     {month}       bulan angka (01-12)
|     {year}        tahun 4 digit
|   Contoh: "{seq:3}/SPK-GL/SAA/{roman_month}/{year}"
|           -> "001/SPK-GL/SAA/XI/2026"
|
*/

return [
    'company_name' => 'PT SARANA AKSES ADIKARYA',

    'company_address' => '362R+6MH, Jalan Pandansimo, Bakungan, Klurahan, Trimurti, Kec. Srandakan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55762',

    'number_format' => '{seq:3}/SPK-GL/SAA/{roman_month}/{year}',
];
