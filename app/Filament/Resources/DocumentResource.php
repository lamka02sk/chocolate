<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

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
                Forms\Components\DatePicker::make('date_issue')
                    ->label('Date of issue'),
                Forms\Components\DatePicker::make('date_expiry')
                    ->label('Date of expiry'),
                Forms\Components\RichEditor::make('notes')
                    ->columnSpanFull(),
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tags')
                    ->searchable()
                    ->badge()
                    ->limitList()
                    ->expandableLimitedList(),
                Tables\Columns\TextColumn::make('date_issue')
                    ->searchable()
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('date_expiry')
                    ->searchable()
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->searchable()
                    ->sortable()
                    ->dateTime()
            ])
            ->defaultSort('id', 'desc')
            ->defaultPaginationPageOption(50)
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
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
