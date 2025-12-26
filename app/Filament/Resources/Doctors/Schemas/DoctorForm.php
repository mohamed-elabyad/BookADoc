<?php

namespace App\Filament\Resources\Doctors\Schemas;

use App\Enums\SpecialtyEnum;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Doctor's Info")
                    ->schema([
                        Select::make('user_id')
                            ->relationship(
                                'user',
                                'name',
                                modifyQueryUsing: function ($query, $operation, $record) {
                                    if ($operation === 'create') {
                                        return $query->whereDoesntHave('doctor');
                                    }

                                    return $query->where(function ($q) use ($record) {
                                            $q->whereDoesntHave('doctor')
                                                ->orWhere('id', $record?->user_id);
                                        });
                                }
                            )
                            ->label('Name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('specialty')
                            ->options(SpecialtyEnum::class)
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('phone')
                            ->tel()
                            ->default(null),
                        TextInput::make('address')
                            ->required(),
                        TextInput::make('bio')
                            ->default(null),
                        Toggle::make('active')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('danger'),
                        FileUpload::make('image')
                            ->directory('doctors/images')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->circleCropper()
                            ->image(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
                Section::make('Work Info')
                    ->schema([
                        TimePicker::make('work_from')
                            ->seconds(false)
                            ->required(),
                        TimePicker::make('work_to')
                            ->seconds(false)
                            ->required(),
                        TextInput::make('ticket_price')
                            ->required()
                            ->numeric(),
                        FileUpload::make('license')
                            ->directory('doctors/licenses')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        FileUpload::make('degree')
                            ->directory('doctors/degrees')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ]);
    }
}
