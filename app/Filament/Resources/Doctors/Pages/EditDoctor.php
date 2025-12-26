<?php

namespace App\Filament\Resources\Doctors\Pages;

use App\Filament\Resources\Doctors\DoctorResource;
use App\Notifications\DoctorActivatedNotification;
use App\Notifications\DoctorDeactivatedNotification;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDoctor extends EditRecord
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

     protected function afterSave(): void
    {
        $doctor = $this->record;

        if ($doctor->wasChanged('active')) {
            if ($doctor->active) {

                $doctor->user->notify(
                    new DoctorActivatedNotification($doctor)
                );
            } else {
                $doctor->user->notify(
                    new DoctorDeactivatedNotification($doctor)
            );
            }
        }
    }
}
