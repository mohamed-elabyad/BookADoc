<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use App\Models\Appointment;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class PaymentStatusChart extends ChartWidget
{
    protected ?string $heading = 'Payment Status Chart';


    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $statuses = PaymentStatusEnum::values();

        $counts = Cache::tags(['payments', 'charts'])
            ->remember('payments-status-chart', 1800, function () use ($statuses) {
                $data = [];
                foreach ($statuses as $status) {
                    $data[$status] = Payment::where('payment_status', $status)->count();
                }
                return $data;
            });

        return [
            'datasets' => [
                [
                    'label' => 'Payment Status',
                    'backgroundColor' => ['#3B82F6', '#4ADE80', '#EF4444', '#FCD34D'],
                    'data' => array_values($counts),
                ],
            ],
            'labels' => $statuses,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
