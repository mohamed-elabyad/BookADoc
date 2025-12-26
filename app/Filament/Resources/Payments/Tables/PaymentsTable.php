<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->with([
                    'appointment.user',
                    'appointment.doctor.user',
                ])
            )
            ->columns([
                TextColumn::make('appointment.doctor.user.name')
                    ->label("Doctor's Name")
                    ->searchable(),
                TextColumn::make('appointment.user.name')
                    ->label("Patient's Name")
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stripe_session_id')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stripe_payment_intent_id')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->searchable()
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
                SelectFilter::make('appointment.doctor.user.name')
                    ->label("Doctor's Name")
                    ->relationship('appointment.doctor.user', 'name', function (Builder $query): Builder {
                        return $query->whereHas('doctor.appointments');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('appointment.user.name')
                    ->label("Patient's Name")
                    ->relationship('appointment.user', 'name', function (Builder $query): Builder {
                        return $query->whereDoesntHave('doctor');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make("payment_method")
                    ->label('Payment Method')
                    ->preload()
                    ->searchable()
                    ->options(PaymentMethodEnum::toSelect()),

                SelectFilter::make("payment_status")
                    ->label('Payment Status')
                    ->preload()
                    ->searchable()
                    ->options(PaymentStatusEnum::toSelect()),

                Filter::make('paid_at')
                    ->schema([
                        DatePicker::make('paid_from')
                            ->label('Paid From')
                            ->native(false)
                            ->displayFormat('j/n/Y'),
                        DatePicker::make('paid_until')
                            ->label('Paid Until')
                            ->native(false)
                            ->displayFormat('j/n/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['paid_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('paid_at', '>=', $date),
                            )
                            ->when(
                                $data['paid_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('paid_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['paid_from'] ?? null) {
                            $indicators[] = 'Paid from: ' . Carbon::parse($data['paid_from'])->format('j/n/Y');
                        }
                        if ($data['paid_until'] ?? null) {
                            $indicators[] = 'Paid until: ' . Carbon::parse($data['paid_until'])->format('j/n/Y');
                        }
                        return $indicators;
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
