<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('appointment_id')
                    ->relationship('appointment', 'id')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EGP'),
                Select::make('payment_method')
                    ->options(PaymentMethodEnum::class)
                    ->required(),
                Select::make('payment_status')
                    ->options(PaymentStatusEnum::class)
                    ->required(),
                TextInput::make('stripe_session_id')
                    ->default(null),
                TextInput::make('stripe_payment_intent_id')
                    ->default(null),
                DateTimePicker::make('paid_at'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
