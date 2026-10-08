<?php

namespace Tests\Unit;

use App\Services\CanteenSchedule;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CanteenScheduleTest extends TestCase
{
    public function test_canteen_is_open_during_first_break(): void
    {
        // 09:30 - Tepat mulai istirahat 1
        $time1 = Carbon::parse('2026-10-03 09:30:00', 'Asia/Jakarta');
        $status1 = CanteenSchedule::getStatus($time1);
        $this->assertTrue($status1['isOpen']);
        $this->assertEquals('Istirahat 1', $status1['currentSession']);

        // 09:45 - Di tengah istirahat 1
        $time2 = Carbon::parse('2026-10-03 09:45:00', 'Asia/Jakarta');
        $this->assertTrue(CanteenSchedule::isOpen($time2));

        // 10:00 - Akhir istirahat 1
        $time3 = Carbon::parse('2026-10-03 10:00:00', 'Asia/Jakarta');
        $this->assertTrue(CanteenSchedule::isOpen($time3));
    }

    public function test_canteen_is_open_during_second_break(): void
    {
        // 12:00 - Mulai istirahat 2 (siang)
        $time1 = Carbon::parse('2026-10-03 12:00:00', 'Asia/Jakarta');
        $status1 = CanteenSchedule::getStatus($time1);
        $this->assertTrue($status1['isOpen']);
        $this->assertEquals('Istirahat 2', $status1['currentSession']);

        // 12:30 - Di tengah istirahat 2
        $time2 = Carbon::parse('2026-10-03 12:30:00', 'Asia/Jakarta');
        $this->assertTrue(CanteenSchedule::isOpen($time2));

        // 13:00 - Akhir istirahat 2 (jam 01:00 siang)
        $time3 = Carbon::parse('2026-10-03 13:00:00', 'Asia/Jakarta');
        $this->assertTrue(CanteenSchedule::isOpen($time3));
    }

    public function test_canteen_is_closed_outside_breaks(): void
    {
        // 08:00 - Sebelum istirahat 1
        $time1 = Carbon::parse('2026-10-03 08:00:00', 'Asia/Jakarta');
        $status1 = CanteenSchedule::getStatus($time1);
        $this->assertFalse($status1['isOpen']);
        $this->assertStringContainsString('Istirahat 1', $status1['nextSession']);

        // 10:15 - Antara istirahat 1 dan 2
        $time2 = Carbon::parse('2026-10-03 10:15:00', 'Asia/Jakarta');
        $status2 = CanteenSchedule::getStatus($time2);
        $this->assertFalse($status2['isOpen']);
        $this->assertStringContainsString('Istirahat 2', $status2['nextSession']);

        // 15:00 - Sore hari setelah istirahat 2
        $time3 = Carbon::parse('2026-10-03 15:00:00', 'Asia/Jakarta');
        $status3 = CanteenSchedule::getStatus($time3);
        $this->assertFalse($status3['isOpen']);
        $this->assertStringContainsString('Besok', $status3['nextSession']);
    }
}
