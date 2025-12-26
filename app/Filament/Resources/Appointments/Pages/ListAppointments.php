<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])
                ),

            'this_month' => Tab::make('This Month')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereMonth('created_at', now()->month)
                ),

            'this_year' => Tab::make('This Year')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereYear('created_at', now()->year)
                ),
        ];
    }
}
