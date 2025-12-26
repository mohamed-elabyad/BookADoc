<?php

namespace App\Filament\Widgets;

use App\Models\Doctor;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestDoctors extends TableWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Doctors (Last 7 Days)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Doctor::query()
                    ->with(['user', 'appointments'])
                    ->where('created_at', '>=', now()->subWeek())
                    ->latest()
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label("Doctor's Name")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('specialty')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->address),
                IconColumn::make('active')
                    ->sortable()
                    ->boolean()
                    ->label('Status'),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(
                fn($record) => route('filament.admin.resources.doctors.view', $record)
            )
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->striped()
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => route('filament.admin.resources.doctors.view', $record)),
                EditAction::make()
                    ->url(fn($record) => route('filament.admin.resources.doctors.edit', $record)),
            ]);
    }
}
