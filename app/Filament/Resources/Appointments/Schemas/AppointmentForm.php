<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Enums\StatusEnum;
use App\Models\Doctor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('doctor_id')
                    ->relationship('doctor', 'id', modifyQueryUsing: function ($query, $search) {
                        $query->with('user');
                        if ($search) {
                            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
                        }
                        return $query;
                    })
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name ?? 'N/A')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('date')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                TimePicker::make('time')
                    ->seconds(false)
                    ->required(),
                Select::make('status')
                    ->options(StatusEnum::class)
                    ->default(StatusEnum::Pending->value)
                    ->required(),
            ]);
    }
}
