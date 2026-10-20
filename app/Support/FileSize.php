<?php

namespace App\Support;

/**
 * Mengubah jumlah byte menjadi teks yang terbaca manusia:
 *   850      => "850 B"
 *   2048     => "2 KB"
 *   1572864  => "1,5 MB"
 *   5242880  => "5 MB"
 *
 * Basis 1024 (sama seperti ukuran berkas di Windows/macOS/Linux) dan
 * pemisah desimal koma, sesuai penulisan bahasa Indonesia.
 *
 * Sengaja TIDAK memakai Illuminate\Support\Number::fileSize(): method itu
 * memerlukan ekstensi PHP `intl`, yang tidak selalu aktif (mis. di PHP
 * Windows atau shared hosting) dan akan melempar exception bila tidak ada.
 */
class FileSize
{
    private const UNITS = ['B', 'KB', 'MB', 'GB', 'TB'];

    /**
     * @return string|null null bila $bytes kosong atau tidak valid, supaya
     *                     tampilan bisa menyembunyikannya dengan @if.
     */
    public static function format(int|float|string|null $bytes, int $precision = 1): ?string
    {
        if ($bytes === null || $bytes === '' || ! is_numeric($bytes) || $bytes < 0) {
            return null;
        }

        $value = (float) $bytes;
        $unit  = 0;
        $last  = count(self::UNITS) - 1;

        while ($value >= 1024 && $unit < $last) {
            $value /= 1024;
            $unit++;
        }

        // Byte utuh tidak punya desimal.
        if ($unit === 0) {
            return number_format($value, 0, ',', '.') . ' B';
        }

        $number = number_format($value, $precision, ',', '.');

        // Buang nol di belakang koma: "5,0" => "5", "1,50" => "1,5".
        if ($precision > 0) {
            $number = rtrim(rtrim($number, '0'), ',');
        }

        return $number . ' ' . self::UNITS[$unit];
    }
}