<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoicesOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $date = now()->subMonths(12);
        $date = Carbon::createFromDate($date->year, $date->month);

        $currency = Setting::where('key', 'currency')->value('value') ?? '€';
        $totalAmountReceived = Invoice::select([])->where('type', 'issued')->withSum('items', 'price')->get()->sum('items_sum_price');
        $totalAmountPaid = Invoice::select([])->where('type', 'received')->withSum('items', 'price')->get()->sum('items_sum_price');

        return [
            Stat::make('Total invoices', Invoice::where('date_issue', '>=', $date)->count())->description('over the last 12 months'),
            Stat::make('Total issued invoices', Invoice::where('type', 'issued')->where('date_issue', '>=', $date)->count())->description('over the last 12 months'),
            Stat::make('Total received invoices', Invoice::where('type', 'received')->where('date_issue', '>=', $date)->count())->description('over the last 12 months'),
            Stat::make('Total amount received', $currency . ' ' . $totalAmountReceived)->description('over the last 12 months'),
            Stat::make('Total amount paid', $currency . ' ' . $totalAmountPaid)->description('over the last 12 months'),
            Stat::make('Margin', round(100 - ($totalAmountPaid / $totalAmountReceived * 100), 2) . ' %')->description('over the last 12 months'),
        ];
    }
}
