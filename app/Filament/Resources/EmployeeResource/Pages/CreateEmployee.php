<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getCreatedNotificationTitle(): ?string //for customize the notification title
    {
        return 'Employee Created';
    }

    protected function getCreatedNotification(): ?Notification //for customize the notification title and body
    {
//      TODO:if you want to hide the notification just return null
        return Notification::make()
            ->success()
            ->title('Employee Created')
            ->body('The employee has been created.');
    }

}
