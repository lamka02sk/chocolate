<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DocumentsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total documents', Document::count())->color('success'),
            Stat::make('Total active documents', Document::whereIsNull('date_expiry')->orWhere('date_expiry', '>', Carbon::now())->count())->color('success')
        ];
    }
}
