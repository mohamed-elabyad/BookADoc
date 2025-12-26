<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatusEnum;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $patients_query = User::query()->whereDoesntHave('doctor');

        return [
            Stat::make('Users', User::count())
                ->description($this->getGrowthDescription(User::class))
                ->descriptionIcon($this->getGrowthIcon(User::class))
                ->color($this->getGrowthColor(User::class))
                ->chart($this->getChartData(User::class)),

            Stat::make('Doctors', Doctor::count())
                ->description($this->getGrowthDescription(Doctor::class))
                ->descriptionIcon($this->getGrowthIcon(Doctor::class))
                ->color($this->getGrowthColor(Doctor::class))
                ->chart($this->getChartData(Doctor::class)),

            Stat::make('Appointments', Appointment::count())
                ->description($this->getGrowthDescription(Appointment::class))
                ->descriptionIcon($this->getGrowthIcon(Appointment::class))
                ->color($this->getGrowthColor(Appointment::class))
                ->chart($this->getChartData(Appointment::class)),

            Stat::make('Patients', $patients_query->count())
                ->description($this->getGrowthDescription($patients_query))
                ->descriptionIcon($this->getGrowthIcon($patients_query))
                ->color($this->getGrowthColor($patients_query))
                ->chart($this->getChartData($patients_query)),

            Stat::make('Total Revenue', $this->formatMoney($this->getYearlyRevenue()))
                ->description('Revenue this year')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart($this->getRevenueChartData()),

            Stat::make('Monthly Revenue', $this->formatMoney($this->getCurrentMonthRevenue()))
                ->description($this->getMonthlyRevenueGrowthDescription())
                ->descriptionIcon($this->getMonthlyRevenueGrowthIcon())
                ->color($this->getMonthlyRevenueGrowthColor())
                ->chart($this->getRevenueChartData()),
        ];
    }


    protected function getYearlyRevenue(): float
    {
        return Cache::tags(['revenue'])->remember('yearly-revenue', 1800, function () {
            return Payment::where('payment_status', PaymentStatusEnum::Confirmed)
                ->whereYear('paid_at', now()->year)
                ->sum('amount');
        });
    }


    protected function getCurrentMonthRevenue(): float
    {
        $data = $this->getMonthlyRevenueGrowthData();
        return $data['current'];
    }


    protected function getMonthlyRevenueGrowthData(): array
    {
        return Cache::tags(['revenue'])->remember('monthly-revenue-growth', 1800, function () {
            $current = Payment::where('payment_status', PaymentStatusEnum::Confirmed)
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount');

            $lastMonth = now()->subMonth();
            $last = Payment::where('payment_status', PaymentStatusEnum::Confirmed)
                ->whereMonth('paid_at', $lastMonth->month)
                ->whereYear('paid_at', $lastMonth->year)
                ->sum('amount');

            return [
                'current' => $current,
                'last' => $last,
            ];
        });
    }


    protected function getMonthlyRevenueGrowthDescription(): string
    {
        $data = $this->getMonthlyRevenueGrowthData();

        if ($data['last'] == 0) {
            return $this->formatMoney($data['current']) . ' this month';
        }

        $percentage_change = (($data['current'] - $data['last']) / $data['last']) * 100;
        $percentage_change = round(abs($percentage_change), 1);

        if ($data['current'] > $data['last']) {
            return $percentage_change . '% increase this month';
        } elseif ($data['current'] < $data['last']) {
            return $percentage_change . '% decrease this month';
        }

        return 'No change';
    }


    protected function getMonthlyRevenueGrowthIcon(): string
    {
        $data = $this->getMonthlyRevenueGrowthData();
        return $data['current'] >= $data['last']
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
    }


    protected function getMonthlyRevenueGrowthColor(): string
    {
        $data = $this->getMonthlyRevenueGrowthData();
        return $data['current'] >= $data['last'] ? 'success' : 'danger';
    }


    protected function getRevenueChartData(): array
    {
        return Cache::tags(['revenue'])->remember('revenue-chart', 1800, function () {
            $data = [];
            $current_year = now()->year;

            for ($month = 1; $month <= 12; $month++) {
                $revenue = Payment::where('payment_status', PaymentStatusEnum::Confirmed)
                    ->whereMonth('paid_at', $month)
                    ->whereYear('paid_at', $current_year)
                    ->sum('amount');

                $data[] = (int) $revenue;
            }

            return $data;
        });
    }


    protected function formatMoney(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }


    protected function getGrowthData(string|Builder $model_or_query): array
    {
        $cache_key = $this->getSimpleCacheKey($model_or_query, 'growth');
        $tags = $this->getCacheTags($model_or_query);

        return Cache::tags($tags)->remember($cache_key, 1800, function () use ($model_or_query) {
            $query = $this->resolveQuery($model_or_query);

            $current = (clone $query)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $lastMonth = now()->subMonth();
            $last = (clone $query)
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count();

            return [
                'current' => $current,
                'last' => $last,
            ];
        });
    }


    protected function getGrowthDescription(string|Builder $model_or_query): string
    {
        $data = $this->getGrowthData($model_or_query);

        if ($data['last'] == 0) {
            return $data['current'] . ' new this month';
        }

        $percentage_change = (($data['current'] - $data['last']) / $data['last']) * 100;
        $percentage_change = round(abs($percentage_change), 1);

        if ($data['current'] > $data['last']) {
            return $percentage_change . '% increase this month';
        } elseif ($data['current'] < $data['last']) {
            return $percentage_change . '% decrease this month';
        }

        return 'No change';
    }


    protected function getGrowthIcon(string|Builder $model_or_query): string
    {
        $data = $this->getGrowthData($model_or_query);
        return $data['current'] >= $data['last']
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
    }


    protected function getGrowthColor(string|Builder $model_or_query): string
    {
        $data = $this->getGrowthData($model_or_query);
        return $data['current'] >= $data['last'] ? 'success' : 'danger';
    }


    protected function getChartData(string|Builder $model_or_query): array
    {
        $cache_key = $this->getSimpleCacheKey($model_or_query, 'chart');
        $tags = $this->getCacheTags($model_or_query);

        return Cache::tags($tags)->remember($cache_key, 1800, function () use ($model_or_query) {
            $query = $this->resolveQuery($model_or_query);
            $data = [];
            $current_year = now()->year;

            for ($month = 1; $month <= 12; $month++) {
                $count = (clone $query)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $current_year)
                    ->count();

                $data[] = $count;
            }

            return $data;
        });
    }


    protected function resolveQuery(string|Builder $model_or_query): Builder
    {
        if (is_string($model_or_query)) {
            return $model_or_query::query();
        }

        return $model_or_query;
    }


    protected function getSimpleCacheKey(string|Builder $model_or_query, string $suffix): string
    {
        if (is_string($model_or_query)) {
            $name = strtolower(class_basename($model_or_query));
        } else {
            $modelClass = get_class($model_or_query->getModel());
            $name = strtolower(class_basename($modelClass));

            // لو في where conditions، أضف identifier بسيط
            if ($model_or_query->getQuery()->wheres) {
                $name .= '-filtered';
            }
        }

        return "{$name}-{$suffix}";
    }

    
    protected function getCacheTags(string|Builder $model_or_query): array
    {
        if (is_string($model_or_query)) {
            $name = strtolower(class_basename($model_or_query));
        } else {
            $modelClass = get_class($model_or_query->getModel());
            $name = strtolower(class_basename($modelClass));
        }

        return ['stats', $name];
    }
}
