<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Enums\StatusEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->with([
                    'doctor.user',
                    'user',
                ])
            )
            ->columns([
                TextColumn::make('doctor.user.name')
                    ->label("Doctor's Name")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label("User's Name")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->date('m/d/Y')
                    ->sortable(),
                TextColumn::make('time')
                    ->time('g:i A')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('doctor.user.name')
                    ->relationship('doctor.user', 'name', function (Builder $query): Builder {
                        return $query->whereHas('doctor.appointments');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user.name')
                    ->relationship('user', 'name', function (Builder $query): Builder {
                        return $query->whereDoesntHave('doctor');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make("status")
                    ->label('Status')
                    ->preload()
                    ->searchable()
                    ->options(StatusEnum::toSelect()),

                Filter::make('time')
                    ->schema([
                        TimePicker::make("time_from")
                            ->label('Time From')
                            ->seconds(false),
                        TimePicker::make("time_to")
                            ->label('Time To')
                            ->seconds(false),
                    ])
                    ->query(
                        function (Builder $query, array $data) {
                            $query->when(
                                $data['time_from'],
                                fn(Builder $q, $time): Builder => $q->where('time', '>=', $time)
                            );
                            $query->when(
                                $data['time_to'],
                                fn(Builder $q, $time): Builder => $q->where('time', '<=', $time)
                            );
                        }
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicator = [];

                        if ($data['time_from']) {
                            $indicator[] = 'Time From: ' . $data['time_from'];
                        }
                        if ($data['time_to']) {
                            $indicator[] = 'Time To: ' . $data['time_to'];
                        }

                        return $indicator;
                    }),

                Filter::make('date')
                    ->schema([
                        DatePicker::make("date_from")
                            ->label('Date From')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make("date_to")
                            ->label('Date To')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                    ])
                    ->query(
                        function (Builder $query, array $data) {
                            $query->when(
                                $data['date_from'],
                                fn(Builder $q, $date): Builder => $q->whereDate('date', '>=', $date)
                            );
                            $query->when(
                                $data['date_to'],
                                fn(Builder $q, $date): Builder => $q->whereDate('date', '<=', $date)
                            );
                        }
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicator = [];

                        if ($data['date_from']) {
                            $indicator[] = 'Date From: ' . \Carbon\Carbon::parse($data['date_from'])->format('d/m/Y');
                        }
                        if ($data['date_to']) {
                            $indicator[] = 'Date To: ' . \Carbon\Carbon::parse($data['date_to'])->format('d/m/Y');
                        }

                        return $indicator;
                    }),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Created From')
                            ->native(false)
                            ->displayFormat('j/n/Y'),
                        DatePicker::make('created_until')
                            ->label('Created Until')
                            ->native(false)
                            ->displayFormat('j/n/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Created from: ' . Carbon::parse($data['created_from'])->format('j/n/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Created until: ' . Carbon::parse($data['created_until'])->format('j/n/Y');
                        }
                        return $indicators;
                    }),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
