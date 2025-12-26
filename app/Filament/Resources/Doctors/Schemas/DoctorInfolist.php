<?php

namespace App\Filament\Resources\Doctors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class DoctorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("User's Info")
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
                Section::make("Doctor's Info")
                    ->schema([
                        ImageEntry::make('image')
                            ->getStateUsing(function ($record) {
                                if (!$record->image) return null;

                                if (filter_var($record->image, FILTER_VALIDATE_URL)) {
                                    return $record->image;
                                }

                                return url('storage/' . $record->image);
                            })
                            ->circular()
                            ->placeholder('No Image Found'),
                        TextEntry::make('bio')
                            ->placeholder('No bio Found'),
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('specialty')
                            ->badge(),
                        IconEntry::make('active')
                            ->boolean(),
                        TextEntry::make('phone')
                            ->placeholder('No Phone Found'),
                        TextEntry::make('address'),
                    ])->columns(2)
                    ->columnSpan(2),
                Section::make("Works Info")
                    ->schema([
                        TextEntry::make('work_from')
                            ->time('g:i A'),
                        TextEntry::make('work_to')
                            ->time('g:i A'),
                        TextEntry::make('ticket_price')
                            ->numeric(),
                        TextEntry::make('license')
                            ->url(fn($record) => $record->license ? Storage::url($record->license) : null)
                            ->openUrlInNewTab()
                            ->placeholder('No License Found'),
                        TextEntry::make('degree')
                            ->url(fn($record) => $record->degree ? Storage::url($record->degree) : null)
                            ->openUrlInNewTab()
                            ->placeholder('No Degree Found'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
                Section::make('Account Info')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])->columns(2)
                    ->columnSpan(2),
            ]);
    }
}
