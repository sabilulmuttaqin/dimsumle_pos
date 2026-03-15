<?php

namespace App\Helpers;

class DateHelper
{
    public static function getMySQLDayName(int $dayNumber): string
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return $days[$dayNumber] ?? '';
    }

    public static function getDayName(int $dayNumber): string
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$dayNumber] ?? '';
    }

    public static function getMonthName(int $monthNumber): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari',  3 => 'Maret',     4 => 'April',
            5 => 'Mei',     6 => 'Juni',       7 => 'Juli',      8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $months[$monthNumber] ?? '';
    }
}
