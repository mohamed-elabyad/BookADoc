<?php

namespace App\Filament\Resources\Doctors\Tables;

use App\Enums\SpecialtyEnum;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DoctorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->with([
                    'user',
                ])
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
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->address),
                IconColumn::make('active')
                    ->sortable()
                    ->boolean(),
                TextColumn::make('work_from')
                    ->time('g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('work_to')
                    ->time('g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bio')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('license')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('degree')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ticket_price')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->label('Doctor Name')
                    ->relationship('user', 'name', fn($query) => $query->whereHas('doctor'))
                    ->searchable()
                    ->preload(),
                // فلتر الحالة النشطة (Active)
                TernaryFilter::make('active')
                    ->label('Active Status')
                    ->placeholder('All')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),

                // فلتر التخصص (Specialty)
                SelectFilter::make('specialty')
                    ->label('Specialty')
                    ->options(SpecialtyEnum::toSelect())
                    ->searchable()
                    ->multiple(),

                // فلتر أوقات العمل (Working Hours)
                // يجيب الدكاترة اللي شغالين في الوقت المطلوب
                Filter::make('working_hours')
                    ->schema([
                        TimePicker::make('time')
                            ->label('Available At Time')
                            ->seconds(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['time'],
                            fn(Builder $query, $time): Builder => $query
                                ->where('work_from', '<=', $time)
                                ->where('work_to', '>=', $time)
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['time'] ?? null) {
                            return ['Available at: ' . $data['time']];
                        }
                        return [];
                    }),

                // فلتر سعر التذكرة (Ticket Price)
                Filter::make('ticket_price')
                    ->schema([
                        TextInput::make('min')
                            ->label('Min Price')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('max')
                            ->label('Max Price')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min'],
                                fn(Builder $query, $price): Builder => $query->where('ticket_price', '>=', $price),
                            )
                            ->when(
                                $data['max'],
                                fn(Builder $query, $price): Builder => $query->where('ticket_price', '<=', $price),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min'] ?? null) {
                            $indicators[] = 'Min price: $' . $data['min'];
                        }
                        if ($data['max'] ?? null) {
                            $indicators[] = 'Max price: $' . $data['max'];
                        }
                        return $indicators;
                    }),

                // فلتر تاريخ الإنشاء (Created At)
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
