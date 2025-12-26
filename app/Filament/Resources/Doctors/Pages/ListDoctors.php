<?php

namespace App\Filament\Resources\Doctors\Pages;

use App\Filament\Resources\Doctors\DoctorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDoctors extends ListRecords
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'active' => Tab::make('Active')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('active', true)
                ),

            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('active', false)
                ),

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
