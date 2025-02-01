<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IncomeExpensesChart extends ChartWidget
{
    protected static ?string $heading = 'Príjmy / Výdavky';

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        /** @var Carbon[] $months */
        $months = [];

        for($i = 12; $i >= 0; --$i) {
            $months[] = now()->subMonths($i);
        }

        $expenses = Invoice::select(DB::raw('strftime(\'%Y-%m\', date_paid) as month, SUM(invoice_items.price * invoice_items.quantity) as total'))
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('type', 'received')
            ->where('date_paid', '>=', Carbon::createFromDate($months[0]->year, $months[0]->month))
            ->groupBy('month')
            ->pluck('total', 'month');

        $expenses = collect($months)->keyBy(fn ($month) => $month->format('Y-m'))->map(fn ($month) => $expenses->get($month->format('Y-m')) ?? 0);

        $incomes = Invoice::select(DB::raw('strftime(\'%Y-%m\', date_issue) as month, SUM(invoice_items.price * invoice_items.quantity) as total'))
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('type', 'issued')
            ->where('date_issue', '>=', Carbon::createFromDate($months[0]->year, $months[0]->month))
            ->groupBy('month')
            ->pluck('total', 'month');

        $incomes = collect($months)->keyBy(fn ($month) => $month->format('Y-m'))->map(fn ($month) => $incomes->get($month->format('Y-m'))?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Príjmy',
                    'data' => $incomes,
                    'backgroundColor' => '#22A95D',
                    'borderWidth' => 0
                ],
                [
                    'label' => 'Výdavky',
                    'data' => $expenses,
                    'backgroundColor' => '#B7192F',
                    'borderWidth' => 0
                ]
            ],
            'labels' => collect($months)->map(fn($month) => $month->format('Y-m'))
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => '€' + value
                        }
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
