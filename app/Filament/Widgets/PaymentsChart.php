<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Cache;

class PaymentsChart extends ChartWidget
{
    protected ?string $heading = 'Confirmed Payments (This Year)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Cache::tags(['payments', 'charts'])->remember(
            'payments-chart-' . now()->year,
            1800,
            function () {
                return Trend::query(
                    Payment::query()
                        ->where('payment_status', PaymentStatusEnum::Confirmed)
                )
                    ->between(
                        start: now()->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perMonth()
                    ->sum('amount');
            }
        );

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(
                fn(TrendValue $value) =>
                Carbon::parse($value->date)->format('M')
            ),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
