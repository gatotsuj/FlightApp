<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Total Transactions', Transaction::query()->count())
                ->label('Total Transactions'),
            Stat::make('Total Amount Pending', 'Rp '.number_format(Transaction::where('payment_status', 'pending')->sum('grand_total'), 0, ',', '.'))
                ->label('Total Amount Pending'),
            Stat::make('Total Amount Completed', 'Rp '.number_format(Transaction::where('payment_status', 'paid')->sum('grand_total'), 0, ',', '.'))
                ->label('Total Amount Completed'),

        ];
    }
}
