<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoicesOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $currency = Setting::where('key', 'currency')->value('value') ?? '€';
        $totalAmountReceived = Invoice::select([])->where('type', 'issued')->withSum('items', 'price')->get()->sum('items_sum_price');
        $totalAmountPaid = Invoice::select([])->where('type', 'received')->withSum('items', 'price')->get()->sum('items_sum_price');

        return [
            Stat::make('Total invoices', Invoice::count()),
            Stat::make('Total issued invoices', Invoice::where('type', 'issued')->count()),
            Stat::make('Total received invoices', Invoice::where('type', 'received')->count()),
            Stat::make('Due invoices', Invoice::whereIsNull('date_paid')->count()),
            Stat::make('Total amount received', $currency . ' ' . $totalAmountReceived),
            Stat::make('Total amount paid', $currency . ' ' . $totalAmountPaid),
        ];
    }
}
