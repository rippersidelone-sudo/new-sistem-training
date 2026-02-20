<?php

/**
 * Helper Functions
 * File: app/helpers.php
 */

if (!function_exists('badgeRole')) {
    function badgeRole(?string $roleName): string
    {
        return match($roleName) {
            'HQ Admin'            => 'bg-blue-100 text-blue-700',
            'Training Coordinator'=> 'bg-green-100 text-green-700',
            'Trainer'             => 'bg-purple-100 text-purple-700',
            'Branch Coordinator'  => 'bg-orange-100 text-orange-700',
            'Participant'         => 'bg-gray-100 text-gray-700',
            default               => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('badgeRoleText')) {
    function badgeRoleText(?string $roleName): string
    {
        return match($roleName) {
            'HQ Admin'            => 'text-blue-700',
            'Training Coordinator'=> 'text-green-700',
            'Trainer'             => 'text-purple-700',
            'Branch Coordinator'  => 'text-orange-700',
            'Participant'         => 'text-gray-700',
            default               => 'text-gray-700',
        };
    }
}

if (!function_exists('badgeStatus')) {
    function badgeStatus(string $status): string
    {
        return match($status) {
            'Scheduled' => 'bg-blue-100 text-blue-700',
            'Ongoing'   => 'bg-green-100 text-green-700',
            'Completed' => 'bg-orange-100 text-orange-700',
            default     => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('badgeAction')) {
    function badgeAction(string $action): string
    {
        return match(strtolower($action)) {
            'create'   => 'bg-green-100 text-green-700',
            'update'   => 'bg-blue-100 text-blue-700',
            'delete'   => 'bg-red-100 text-red-700',
            'approve'  => 'bg-emerald-100 text-emerald-700',
            'reject'   => 'bg-rose-100 text-rose-700',
            'validate' => 'bg-indigo-100 text-indigo-700',
            'submit'   => 'bg-cyan-100 text-cyan-700',
            default    => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('formatBatchCode')) {
    function formatBatchCode(int $id, ?int $year = null): string
    {
        $year = $year ?? date('Y');
        return 'TRN-' . $year . '-' . str_pad($id, 3, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format date to Indonesian month abbreviation
     * Contoh: 20 Feb 2026, 03 Mei 2025
     */
    function formatDate($date, string $format = 'd M Y'): string
    {
        if (!$date) return '-';

        // Map: nomor bulan => singkatan Indonesia
        $indonesian = [
            1  => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
            5  => 'Mei', 6  => 'Jun', 7  => 'Jul', 8  => 'Agu',
            9  => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        // Map: nomor bulan => singkatan English yang dihasilkan Carbon
        $english = [
            1  => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
            5  => 'May', 6  => 'Jun', 7  => 'Jul', 8  => 'Aug',
            9  => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ];

        $date      = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        $formatted = $date->format($format);

        // ✅ Hanya replace bulan milik tanggal ini (bukan loop semua bulan)
        $monthNum  = (int) $date->format('n');
        $formatted = str_replace($english[$monthNum], $indonesian[$monthNum], $formatted);

        return $formatted;
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format datetime ke: "20 Feb 2026, 09:00"
     * Juga handle string TIME saja (HH:MM:SS dari kolom DB bertipe time)
     */
    function formatDateTime($datetime): string
    {
        if (!$datetime) return '-';

        // Jika hanya string waktu saja (HH:MM atau HH:MM:SS), kembalikan format jam saja
        if (is_string($datetime) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $datetime)) {
            return \Carbon\Carbon::parse($datetime)->format('H:i');
        }

        $datetime = is_string($datetime) ? \Carbon\Carbon::parse($datetime) : $datetime;

        return formatDate($datetime, 'd M Y') . ', ' . $datetime->format('H:i');
    }
}

if (!function_exists('formatTime')) {
    /**
     * Format waktu saja: "09:00"
     * Berguna untuk kolom DB bertipe time (string HH:MM:SS)
     */
    function formatTime($time): string
    {
        if (!$time) return '-';

        return \Carbon\Carbon::parse($time)->format('H:i');
    }
}

if (!function_exists('formatDateRange')) {
    /**
     * Format rentang tanggal
     * Contoh: "20 Feb - 22 Feb 2026" atau "20 Feb 2026" jika sama
     */
    function formatDateRange($startDate, $endDate): string
    {
        if (!$startDate || !$endDate) return '-';

        $start = is_string($startDate) ? \Carbon\Carbon::parse($startDate) : $startDate;
        $end   = is_string($endDate)   ? \Carbon\Carbon::parse($endDate)   : $endDate;

        if ($start->isSameDay($end)) {
            return formatDate($start, 'd M Y');
        }

        if ($start->isSameMonth($end) && $start->isSameYear($end)) {
            return $start->format('d') . ' - ' . formatDate($end, 'd M Y');
        }

        return formatDate($start, 'd M Y') . ' - ' . formatDate($end, 'd M Y');
    }
}

if (!function_exists('formatRelativeDate')) {
    /**
     * Format tanggal relatif: "Hari ini", "Kemarin", "3 hari lagi", "2 hari lalu"
     */
    function formatRelativeDate($date): string
    {
        if (!$date) return '-';

        $date  = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        $today = \Carbon\Carbon::today();
        $diff  = $today->diffInDays($date, false);

        if ($diff === 0)  return 'Hari ini';
        if ($diff === -1) return 'Kemarin';
        if ($diff === 1)  return 'Besok';
        if ($diff > 1)    return $diff . ' hari lagi';

        return abs($diff) . ' hari lalu';
    }
}