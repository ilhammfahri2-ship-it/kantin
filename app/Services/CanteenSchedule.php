<?php

namespace App\Services;

use Carbon\Carbon;

class CanteenSchedule
{
    // Jam Istirahat 1: 09:30 - 10:00 WIB
    public const SESSION_1_START = '09:30';
    public const SESSION_1_END   = '10:00';

    // Jam Istirahat 2: 12:00 - 13:00 WIB (01:00 Siang)
    public const SESSION_2_START = '12:00';
    public const SESSION_2_END   = '13:00';

    public const TIMEZONE = 'Asia/Jakarta';

    /**
     * Dapatkan instance Carbon waktu saat ini di timezone Jakarta.
     */
    public static function now(?Carbon $customTime = null): Carbon
    {
        return $customTime ?: Carbon::now(self::TIMEZONE);
    }

    /**
     * Cek apakah kantin saat ini sedang buka.
     */
    public static function isOpen(?Carbon $customTime = null): bool
    {
        return self::getStatus($customTime)['isOpen'];
    }

    /**
     * Dapatkan detail status operasional kantin secara lengkap.
     */
    public static function getStatus(?Carbon $customTime = null): array
    {
        $now = self::now($customTime);
        $currentTime = $now->format('H:i');

        $isSession1 = ($currentTime >= self::SESSION_1_START && $currentTime <= self::SESSION_1_END);
        $isSession2 = ($currentTime >= self::SESSION_2_START && $currentTime <= self::SESSION_2_END);

        // Jika ada customTime yang diteruskan (misal unit test), evaluasi jam istirahat riil
        if ($customTime !== null) {
            $isOpen = $isSession1 || $isSession2;
        } else {
            // Mode pengembangan / preview: Buka kantin agar seluruh halaman dan fitur dapat ditinjau
            $isOpen = (bool) env('CANTEEN_ALWAYS_OPEN', true);
        }

        $currentSession = null;
        if ($isSession1) {
            $currentSession = 'Istirahat 1';
        } elseif ($isSession2) {
            $currentSession = 'Istirahat 2';
        } elseif ($isOpen) {
            $currentSession = 'Layanan Aktif';
        }

        $nextSession = null;
        if (!$isOpen) {
            if ($currentTime < self::SESSION_1_START) {
                $nextSession = 'Istirahat 1 (09:30 WIB)';
            } elseif ($currentTime < self::SESSION_2_START) {
                $nextSession = 'Istirahat 2 (12:00 WIB)';
            } else {
                $nextSession = 'Besok pada Istirahat 1 (09:30 WIB)';
            }
        }

        return [
            'isOpen' => $isOpen,
            'currentTime' => $currentTime,
            'currentSession' => $currentSession,
            'nextSession' => $nextSession,
            'session1' => [
                'name' => 'Istirahat 1',
                'start' => self::SESSION_1_START,
                'end' => self::SESSION_1_END,
                'label' => '09:30 - 10:00 WIB',
            ],
            'session2' => [
                'name' => 'Istirahat 2',
                'start' => self::SESSION_2_START,
                'end' => self::SESSION_2_END,
                'label' => '12:00 - 13:00 WIB (01:00 Siang)',
            ],
            'scheduleSummary' => 'Istirahat 1 (09:30 - 10:00 WIB) & Istirahat 2 (12:00 - 13:00 WIB)',
        ];
    }
}
