<?php

namespace App\Filament\Resources\Doctors\Pages;

use App\Filament\Resources\Doctors\DoctorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record->user;

        if ($user) {
            $user->update([
                'role' => 'doctor',
            ]);
        }
    }
}
