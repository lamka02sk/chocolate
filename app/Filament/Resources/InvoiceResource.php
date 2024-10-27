<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->columnSpan(2)
                    ->minLength(3)
                    ->maxLength(256)
                    ->required(),
                Forms\Components\TextInput::make('partner')
                    ->columnSpan(2)
                    ->minLength(1)
                    ->maxLength(256)
                    ->required(),
                Forms\Components\Select::make('type')
                    ->columnSpan(2)
                    ->options(['received' => 'Received', 'issued' => 'Issued'])
                    ->default('received')
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\DatePicker::make('date_issue')
                    ->label('Date of issue'),
                Forms\Components\DatePicker::make('date_paid')
                    ->label('Date of payment'),
                Forms\Components\RichEditor::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('items')
                    ->columns(12)
                    ->columnSpanFull()
                    ->relationship()
                    ->addActionLabel('Add item')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpan(7)
                            ->minLength(3)
                            ->maxLength(256)
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->columnSpan(2)
                            ->numeric()
                            ->inputMode('decimal')
                            ->default(1.0)
                            ->required(),
                        Forms\Components\TextInput::make('unit')
                            ->columnSpan(1)
                            ->default(Setting::where('key', 'unit')->value('value') ?? ''),
                        Forms\Components\TextInput::make('price')
                            ->columnSpan(2)
                            ->label('Total price')
                            ->prefix(Setting::where('key', 'currency')->value('value') ?? '€')
                            ->numeric()
                            ->inputMode('decimal')
                            ->default(0.0)
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                    ])
                    ->orderColumn(),
                Forms\Components\TagsInput::make('tags')
                    ->columnSpan(2)
                    ->nestedRecursiveRules([
                        'min:2',
                        'max:32',
                    ])
                    ->splitKeys([',', ' ', 'Tab', 'Enter']),
                Forms\Components\FileUpload::make('attachments')
                    ->columnSpan(2)
                    ->multiple()
                    ->downloadable()
                    ->openable()
                    ->reorderable()
                    ->appendFiles()
                    ->moveFiles()
                    ->maxFiles(100)
                    ->storeFileNamesIn('attachments_file_names')
            ]);
    }

    public static function table(Table $table): Table
    {
        $currency = Setting::where('key', 'currency')->value('value') ?? '€';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(function (Invoice $invoice) {
                        return [
                            'received' => 'info',
                            'issued' => 'success',
                        ][$invoice->type];
                    })
                    ->state(function (Invoice $invoice) {
                        return [
                            'received' => 'Received',
                            'issued' => 'Issued',
                        ][$invoice->type];
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->state(function (Invoice $invoice) use ($currency) {
                        return $currency . ' ' . $invoice->items->sum('price');
                    }),
                Tables\Columns\TextColumn::make('date_paid')
                    ->searchable()
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->searchable()
                    ->sortable()
                    ->dateTime()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
