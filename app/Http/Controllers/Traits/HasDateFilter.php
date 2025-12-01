<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasDateFilter
{
    /**
     * Apply date filter to query based on day_filter or selectedDate
     * Sama seperti logika di DashboardController untuk konsistensi
     * 
     * @param Builder $query
     * @param string|null $dayFilter
     * @param string|null $selectedDate
     * @param Carbon|null $defaultDate Default date jika tidak ada filter (null = no filter)
     * @return Builder
     */
    protected function applyDateFilter(Builder $query, ?string $dayFilter = null, ?string $selectedDate = null, ?Carbon $defaultDate = null): Builder
    {
        $today = today();
        
        // Apply date filter berdasarkan day_filter atau selectedDate
        // Sama seperti DokterController: filter berdasarkan tgl_reg
        if ($dayFilter && !$selectedDate) {
            switch ($dayFilter) {
                case 'today':
                    $query->whereDate('tgl_reg', $today->toDateString());
                    break;
                case 'yesterday':
                    $yesterday = $today->copy()->subDay();
                    $query->whereDate('tgl_reg', $yesterday->toDateString());
                    break;
                case 'this_week':
                    $startOfWeek = $today->copy()->startOfWeek();
                    $endOfWeek = $today->copy()->endOfWeek();
                    $query->whereBetween('tgl_reg', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
                    break;
                case 'last_week':
                    $startOfLastWeek = $today->copy()->subWeek()->startOfWeek();
                    $endOfLastWeek = $today->copy()->subWeek()->endOfWeek();
                    $query->whereBetween('tgl_reg', [$startOfLastWeek->toDateString(), $endOfLastWeek->toDateString()]);
                    break;
                case 'this_month':
                    $startOfMonth = $today->copy()->startOfMonth();
                    $endOfMonth = $today->copy()->endOfMonth();
                    $query->whereBetween('tgl_reg', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
                    break;
                case 'last_month':
                    $startOfLastMonth = $today->copy()->subMonth()->startOfMonth();
                    $endOfLastMonth = $today->copy()->subMonth()->endOfMonth();
                    $query->whereBetween('tgl_reg', [$startOfLastMonth->toDateString(), $endOfLastMonth->toDateString()]);
                    break;
                case 'this_year':
                    $startOfYear = $today->copy()->startOfYear();
                    $endOfYear = $today->copy()->endOfYear();
                    $query->whereBetween('tgl_reg', [$startOfYear->toDateString(), $endOfYear->toDateString()]);
                    break;
            }
        } elseif ($selectedDate && $selectedDate !== 'all' && $selectedDate !== '') {
            // Filter berdasarkan tanggal spesifik - SAMA seperti DokterController
            try {
                $queryDate = Carbon::parse($selectedDate);
                $query->whereDate('tgl_reg', $queryDate->toDateString());
            } catch (\Exception $e) {
                // Invalid date, skip filter
            }
        } elseif ($defaultDate !== null) {
            // Default: filter berdasarkan defaultDate jika diberikan
            $query->whereDate('tgl_reg', $defaultDate->toDateString());
        }
        // Jika tidak ada filter dan defaultDate null, tidak ada filter tanggal (show all)
        
        return $query;
    }
}

