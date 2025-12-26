<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Cache;

class AppointmentsChart extends ChartWidget
{
    protected ?string $heading = 'All Appointments (This Year)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Cache::tags(['appointments', 'charts'])->remember(
            'appointments-chart-' . now()->year,
            1800,
            function () {
                return Trend::model(Appointment::class)
                    ->between(
                        start: now()->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perMonth()
                    ->count();
            }
        );

        return [
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(
                fn (TrendValue $value) =>
                    Carbon::parse($value->date)->format('M')
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
