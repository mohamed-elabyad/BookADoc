<?php

namespace App\Filament\Widgets;

use App\Enums\StatusEnum;
use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class AppointmentsStatusChart extends ChartWidget
{
    protected ?string $heading = 'Appointments Status Chart';


    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $statuses = StatusEnum::values();

        $counts = Cache::tags(['appointments', 'chart'])
            ->remember('appointments-status-chart', 1800, function () use ($statuses) {
                $data = [];
                foreach ($statuses as $status) {
                    $data[$status] = Appointment::where('status', $status)->count();
                }
                return $data;
            });

        return [
            'datasets' => [
                [
                    'label' => 'Appointments Status',
                    'backgroundColor' => ['#FCD34D', '#4ADE80', '#3B82F6'],
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
