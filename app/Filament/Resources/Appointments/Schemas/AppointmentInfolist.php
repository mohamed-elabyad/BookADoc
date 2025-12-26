<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AppointmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Info')
                    ->schema([
                        TextEntry::make('doctor.name')
                            ->label("Doctor's Name"),
                        TextEntry::make('user.name')
                            ->label("User's Name"),
                        TextEntry::make('date')
                            ->date('m/d/Y'),
                        TextEntry::make('time')
                            ->time('g:i A'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ]);
    }
}
