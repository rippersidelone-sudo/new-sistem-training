<?php

/**
 * Helper Functions for Badge Colors and Utilities
 * File: app/helpers.php or bootstrap/helpers.php
 */

if (!function_exists('badgeRole')) {
    /**
     * Get badge color class based on role name (for badges)
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

if (!function_exists('badgeRoleText')) {
    /**
     * Get text color class based on role name (for text only)
     */
    function badgeRoleText(?string $roleName): string
    {
        return match($roleName) {
            'HQ Admin' => 'text-blue-700',
            'Training Coordinator' => 'text-green-700',
            'Trainer' => 'text-purple-700',
            'Branch Coordinator' => 'text-orange-700',
            'Participant' => 'text-gray-700',
            default => 'text-gray-700',
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

if (!function_exists('badgeAction')) {
    /**
     * Get badge color class based on action type
     */
    function badgeAction(string $action): string
    {
        return match(strtolower($action)) {
            'create' => 'bg-green-100 text-green-700',
            'update' => 'bg-blue-100 text-blue-700',
            'delete' => 'bg-red-100 text-red-700',
            'approve' => 'bg-emerald-100 text-emerald-700',
            'reject' => 'bg-rose-100 text-rose-700',
            'validate' => 'bg-indigo-100 text-indigo-700',
            'submit' => 'bg-cyan-100 text-cyan-700',
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