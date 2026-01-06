<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            //
            \App\Filament\Widgets\TransactionOverview::class,
        ];
    }

    /* protected function getHeaderActions(): array
     {
         return [
             //
             \Filament\Actions\CreateAction::make(),
         ];
     }*/
}
