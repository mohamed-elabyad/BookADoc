<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Predis\Response\Status;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Appointment Details')
                    ->schema([
                        TextEntry::make('appointment.id')
                            ->label('View Appointment')
                            ->formatStateUsing(fn($record) => "View Appointment #{$record->appointment_id}")
                            ->url(fn($record) => route('filament.admin.resources.appointments.view', $record->appointment_id))
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->openUrlInNewTab(),
                        TextEntry::make('appointment.doctor.name')
                            ->label("Doctor's Name")
                            ->icon('heroicon-o-user'),
                        TextEntry::make('appointment.user.name')
                            ->label("Patient's Name")
                            ->icon('heroicon-o-user'),
                        TextEntry::make('appointment.date')
                            ->label('Appointment Date')
                            ->date('j/n/Y')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('appointment.time')
                            ->label('Appointment Time')
                            ->time('g:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('appointment.status')
                            ->label('Appointment Status')
                            ->badge()
                            ->color(fn($state): string => match ($state?->value) {
                                StatusEnum::Pending->value => 'warning',
                                StatusEnum::Confirmed->value => 'success',
                                StatusEnum::Confirmed->value => 'info',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3)
                    ->columnSpan(2),

                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('amount')
                            ->money(fn($record) => $record->currency ?? 'EGP')
                            ->label('Amount Paid')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->badge()
                            ->color(fn($state): string => match ($state?->value) {
                                PaymentMethodEnum::Online->value => 'success',
                                PaymentMethodEnum::Cash->value => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state): string => ucfirst($state->value)),
                        TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->badge()
                            ->color(fn($state): string => match ($state?->value) {
                                PaymentStatusEnum::Confirmed->value => 'success',
                                PaymentStatusEnum::Pending->value => 'warning',
                                PaymentStatusEnum::Failed->value => 'danger',
                                PaymentStatusEnum::Cancelled->value => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state): string => ucfirst($state->value)),
                        TextEntry::make('paid_at')
                            ->label('Paid At')
                            ->dateTime('j/n/Y, g:i A')
                            ->placeholder('Not paid yet')
                            ->icon('heroicon-o-check-circle'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Section::make('Stripe Details')
                    ->schema([
                        TextEntry::make('stripe_session_id')
                            ->label('Stripe Session ID')
                            ->placeholder('-')
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('stripe_payment_intent_id')
                            ->label('Stripe Payment Intent ID')
                            ->placeholder('-')
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->visible(fn($record) => $record?->payment_method->value === 'online')
                    ->columnSpan(2),

                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('j/n/Y, g:i A')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('j/n/Y, g:i A')
                            ->icon('heroicon-o-calendar')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->columnSpan(2),
            ]);
    }
}
