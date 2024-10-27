<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    use InteractsWithForms;

    public ?array $data = [
        'unit' => 'pcs',
        'currency' => '€'
    ];

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected static ?int $navigationSort = 99;

    public function mount(): void
    {
        $settings = Setting::all(['key', 'value'])->pluck('value', 'key');
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('unit')
                    ->label('Default unit')
                    ->required(),
                TextInput::make('currency')
                    ->label('Default currency')
                    ->required()
            ])
            ->statePath('data');
    }

    // Save settings
    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('Settings updated successfully')
            ->color('success')
            ->send();
    }
}
