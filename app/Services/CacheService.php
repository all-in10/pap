<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache key prefixes
     */
    const PREFIX_HOURSBANK = 'hoursbank:';
    const PREFIX_DESIGNATIONS = 'designations:';
    const PREFIX_DEPARTMENTS = 'departments:';
    const PREFIX_EMPLOYEES = 'employees:';
    const PREFIX_CONTRACTS = 'contracts:';

    /**
     * Cache durations (in minutes)
     */
    const DURATION_HOURSBANK = 60;         // 1 hour
    const DURATION_DESIGNATIONS = 1440;    // 24 hours
    const DURATION_DEPARTMENTS = 1440;     // 24 hours
    const DURATION_EMPLOYEES = 240;        // 4 hours
    const DURATION_CONTRACTS = 480;        // 8 hours

    /**
     * Get cached hoursbank by employee ID
     */
    public static function getHoursbank($employeeId)
    {
        return Cache::remember(
            self::PREFIX_HOURSBANK . $employeeId,
            now()->addMinutes(self::DURATION_HOURSBANK),
            function () use ($employeeId) {
                return \App\Models\Hoursbank::where('employee_id', $employeeId)->first();
            }
        );
    }

    /**
     * Invalidate hoursbank cache
     */
    public static function invalidateHoursbank($employeeId)
    {
        Cache::forget(self::PREFIX_HOURSBANK . $employeeId);
    }

    /**
     * Get all designations (cached)
     */
    public static function getDesignations()
    {
        return Cache::remember(
            self::PREFIX_DESIGNATIONS . 'all',
            now()->addMinutes(self::DURATION_DESIGNATIONS),
            function () {
                return \App\Models\Designation::select('id', 'name', 'base_salary')->get();
            }
        );
    }

    /**
     * Invalidate designations cache
     */
    public static function invalidateDesignations()
    {
        Cache::forget(self::PREFIX_DESIGNATIONS . 'all');
    }

    /**
     * Get all departments (cached)
     */
    public static function getDepartments()
    {
        return Cache::remember(
            self::PREFIX_DEPARTMENTS . 'all',
            now()->addMinutes(self::DURATION_DEPARTMENTS),
            function () {
                return \App\Models\Department::select('id', 'name', 'description')->get();
            }
        );
    }

    /**
     * Invalidate departments cache
     */
    public static function invalidateDepartments()
    {
        Cache::forget(self::PREFIX_DEPARTMENTS . 'all');
    }

    /**
     * Get employees by department (cached)
     */
    public static function getEmployeesByDepartment($departmentId)
    {
        return Cache::remember(
            self::PREFIX_EMPLOYEES . 'dept:' . $departmentId,
            now()->addMinutes(self::DURATION_EMPLOYEES),
            function () use ($departmentId) {
                return \App\Models\Employee::where('department_id', $departmentId)
                    ->select('id', 'name', 'designation_id', 'department_id')
                    ->get();
            }
        );
    }

    /**
     * Invalidate employees cache
     */
    public static function invalidateEmployeesByDepartment($departmentId)
    {
        Cache::forget(self::PREFIX_EMPLOYEES . 'dept:' . $departmentId);
    }

    /**
     * Get active contracts for employee (cached)
     */
    public static function getActiveContracts($employeeId)
    {
        return Cache::remember(
            self::PREFIX_CONTRACTS . 'active:' . $employeeId,
            now()->addMinutes(self::DURATION_CONTRACTS),
            function () use ($employeeId) {
                return \App\Models\Contract::where('employee_id', $employeeId)
                    ->where('status', 'active')
                    ->select('id', 'salary', 'start_date', 'end_date', 'status')
                    ->get();
            }
        );
    }

    /**
     * Invalidate contracts cache
     */
    public static function invalidateActiveContracts($employeeId)
    {
        Cache::forget(self::PREFIX_CONTRACTS . 'active:' . $employeeId);
    }

    /**
     * Clear all caches
     */
    public static function clearAll()
    {
        Cache::flush();
    }

    /**
     * Clear cache by prefix
     */
    public static function clearByPrefix($prefix)
    {
        // Note: Laravel Cache doesn't have a built-in prefix clear
        // This would require additional implementation with Redis
        // For now, we'll use individual cache invalidations
    }
}
