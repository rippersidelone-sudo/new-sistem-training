<?php

/**
 * Helper Functions for Badge Colors and Utilities
 * Add this file to composer.json autoload.files section
 */

if (!function_exists('badgeRole')) {
    /**
     * Get badge color class based on role name
     */
    function badgeRole(?string $roleName): string
    {
        return match($roleName) {
            'HQ Admin' => 'bg-blue-100 text-blue-700',
            'Training Coordinator' => 'bg-green-100 text-green-700',
            'Trainer' => 'bg-purple-100 text-purple-700',
            'Branch Coordinator' => 'bg-orange-100 text-orange-700',
            'Participant' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('badgeStatus')) {
    /**
     * Get badge color class based on batch status
     */
    function badgeStatus(string $status): string
    {
        return match($status) {
            'Scheduled' => 'bg-blue-100 text-blue-700',
            'Ongoing' => 'bg-green-100 text-green-700',
            'Completed' => 'bg-orange-100 text-orange-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}

if (!function_exists('formatBatchCode')) {
    /**
     * Format batch code (TRN-YYYY-XXX)
     */
    function formatBatchCode(int $id, ?int $year = null): string
    {
        $year = $year ?? date('Y');
        return 'TRN-' . $year . '-' . str_pad($id, 3, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format date to Indonesian format
     */
    function formatDate($date, string $format = 'd M Y'): string
    {
        if (!$date) return '-';
        
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        
        $date = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        $formatted = $date->format($format);
        
        // Replace month names
        foreach ($months as $num => $name) {
            $formatted = str_replace($date->format('M'), $name, $formatted);
        }
        
        return $formatted;
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format datetime to Indonesian format
     */
    function formatDateTime($datetime): string
    {
        if (!$datetime) return '-';
        
        $datetime = is_string($datetime) ? \Carbon\Carbon::parse($datetime) : $datetime;
        return formatDate($datetime, 'd M Y') . ', ' . $datetime->format('H.i');
    }
}