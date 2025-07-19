<?php

namespace RMS\Helper\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait ModelTrait
{
    /**
     * Scope to filter active records
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    /**
     * Scope to get count and sum of a column
     *
     * @param Builder $query
     * @param string $column
     * @return Builder
     */
    public function scopeCountAndSum(Builder $query, string $column = 'amount'): Builder
    {
        return $query->select([
            DB::raw('count(*) as total_count'),
            DB::raw("sum({$column}) as total_sum"),
        ]);
    }

    /**
     * Scope to filter records created today
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->where('created_at', '>=', Carbon::today());
    }

    /**
     * Scope to filter records created yesterday
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeYesterday(Builder $query): Builder
    {
        return $query->where('created_at', '>=', Carbon::today()->subDay())
            ->where('created_at', '<', Carbon::today());
    }
}
