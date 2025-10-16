<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationDocumentResource\Pages;
use App\Models\ApplicationDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicationDocumentResource extends Resource
{
    protected static ?string $model = ApplicationDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('application_id')
                    ->relationship('application', 'id')
                    ->required(),
                Forms\Components\TextInput::make('doc_type')
                    ->required(),
                Forms\Components\TextInput::make('file_url')
                    ->required()
                    ->maxLength(512),
                Forms\Components\TextInput::make('verified_status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('doc_type'),
                Tables\Columns\TextColumn::make('file_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('verified_status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListApplicationDocuments::route('/'),
            'create' => Pages\CreateApplicationDocument::route('/create'),
            'edit' => Pages\EditApplicationDocument::route('/{record}/edit'),
        ];
    }
}
