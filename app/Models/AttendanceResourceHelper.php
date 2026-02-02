<?php

namespace App\Models;

use Carbon\Carbon;

class AttendanceResourceHelper
{
    public static function calculateDurationFormatted($date, $enter_time, $exit_time, $break_minutes): string
    {
        if (!$date || !$enter_time || !$exit_time) {
            return '-';
        }
        try {
            $dateStr = is_string($date) ? $date : (method_exists($date, 'format') ? $date->format('Y-m-d') : (string) $date);
            $enterStr = is_string($enter_time) ? $enter_time : (method_exists($enter_time, 'format') ? $enter_time->format('H:i:s') : (string) $enter_time);
            $exitStr = is_string($exit_time) ? $exit_time : (method_exists($exit_time, 'format') ? $exit_time->format('H:i:s') : (string) $exit_time);
            $enter = Carbon::parse($dateStr . ' ' . $enterStr);
            $exit = Carbon::parse($dateStr . ' ' . $exitStr);
            if ($exit->lt($enter)) {
                $exit->addDay();
            }
            $break = (int) ($break_minutes ?? 60);
            if ($break < 0) { $break = 0; }
            $totalMinutes = (int) $exit->diffInMinutes($enter) - $break;
            if ($totalMinutes < 0) {
                $totalMinutes = 0;
            }
            return Attendance::formatMinutesToHuman($totalMinutes);
        } catch (\Exception $e) {
            return '-';
        }
    }

    public static function calculateOvertimeFormatted($date, $enter_time, $exit_time, $break_minutes): string
    {
        if (!$date || !$enter_time || !$exit_time) {
            return '-';
        }
        try {
            $dateStr = is_string($date) ? $date : (method_exists($date, 'format') ? $date->format('Y-m-d') : (string) $date);
            $enterStr = is_string($enter_time) ? $enter_time : (method_exists($enter_time, 'format') ? $enter_time->format('H:i:s') : (string) $enter_time);
            $exitStr = is_string($exit_time) ? $exit_time : (method_exists($exit_time, 'format') ? $exit_time->format('H:i:s') : (string) $exit_time);
            $enter = Carbon::parse($dateStr . ' ' . $enterStr);
            $exit = Carbon::parse($dateStr . ' ' . $exitStr);
            if ($exit->lt($enter)) {
                $exit->addDay();
            }
            $break = (int) ($break_minutes ?? 60);
            if ($break < 0) { $break = 0; }
            $totalMinutes = (int) $exit->diffInMinutes($enter) - $break;
            if ($totalMinutes < 0) {
                $totalMinutes = 0;
            }
            $standardMinutes = 8 * 60;
            $overtime = max(0, $totalMinutes - $standardMinutes);
            return Attendance::formatMinutesToHuman($overtime);
        } catch (\Exception $e) {
            return '-';
        }
    }
}