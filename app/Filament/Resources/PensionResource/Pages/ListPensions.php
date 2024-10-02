<?php

namespace App\Filament\Resources\PensionResource\Pages;

use App\Filament\Resources\PensionResource;
use App\Models\Attribute;
use App\Models\Employee;
use App\Models\Payroll;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPensions extends ListRecords
{
    protected static string $resource = PensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('pensions')
                ->label('Pension')
                ->form([
                    Select::make('rate')
                        ->label('Pension Rate')
                        ->options(Attribute::all()->pluck('name', 'value'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data):void{
                    $employee = Employee::query()->where('status', 0)->where('rsa_status', '=', 1)->get();
                    $rep = false;
                    $amount = 0;
                    foreach($employee as $emp):
                        $amount = $data['rate'] * $emp->gross;
                        $existingRecord = Payroll::query()->where('employee_id', $emp->id)->whereMonth('created_at', '=', date('m'))->first();
                        if($existingRecord):
                            if($existingRecord->update([
                                'pension' => $amount
                            ])):
                                $rep = true;
                            endif;
                        else:
                            if(Payroll::create([
                                'employee_id' => $emp->id,
                                'pension' => $amount,
                            ])):
                                $rep = true;
                            endif;
                        endif;
                    endforeach;
                    if($rep):
                        Notification::make()
                            ->title('Pension generated successfully.')
                            ->success()
                            ->send();
                    else:
                        Notification::make()
                            ->title('Pension generated failed.')
                            ->success()
                            ->send();
                    endif;
                }),
        ];
    }
}
