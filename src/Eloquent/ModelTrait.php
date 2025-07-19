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

    /**
     * Scope to search records with LIKE
     *
     * @param Builder $query
     * @param string $column
     * @param string $value
     * @return Builder
     */
    public function scopeWhereLike(Builder $query, string $column, string $value): Builder
    {
        return $query->where($column, 'LIKE', '%' . $value . '%');
    }

    /**
     * Scope to order records by latest created_at
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrderByLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to filter records within a date range
     *
     * @param Builder $query
     * @param Carbon|string $start
     * @param Carbon|string $end
     * @param string $column
     * @return Builder
     */
    public function scopeWhereInDateRange(Builder $query, $start, $end, string $column = 'created_at'): Builder
    {
        return $query->whereBetween($column, [
            $start instanceof Carbon ? $start : Carbon::parse($start),
            $end instanceof Carbon ? $end : Carbon::parse($end),
        ]);
    }

    /**
     * Scope to include soft-deleted records
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithTrashed(Builder $query): Builder
    {
        return $query->withTrashed();
    }

    /**
     * Scope to filter records by status
     *
     * @param Builder $query
     * @param string|int $status
     * @param string $column
     * @return Builder
     */
    public function scopeWhereStatus(Builder $query, $status, string $column = 'status'): Builder
    {
        return $query->where($column, $status);
    }
}
