<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $navigationGroup   = 'Payments';
    protected static ?string $navigationIcon    = 'heroicon-o-building-office-2';
    protected static ?string $modelLabel        = 'Biller';
    protected static ?string $pluralModelLabel  = 'Billers';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('biller_code')
                ->label('Biller Code')
                ->required()
                ->maxLength(40)
                ->unique(ignoreRecord: true)
                ->helperText('Must be unique (e.g. MERALCO, GLOBE).'),

            Forms\Components\TextInput::make('biller_name')
                ->label('Biller Name')
                ->required()
                ->maxLength(160),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active'   => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->native(false)
                ->required()
                ->default('active'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('biller_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('biller_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'gray'    => 'inactive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Bill $record) => $record->status !== 'active')
                    ->requiresConfirmation()
                    ->action(fn (Bill $record) => $record->update(['status' => 'active'])),

                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn (Bill $record) => $record->status !== 'inactive')
                    ->requiresConfirmation()
                    ->action(fn (Bill $record) => $record->update(['status' => 'inactive'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Action::make('bulkActivate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'active'])),
                    Action::make('bulkDeactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'inactive'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers here (e.g., BillPayments) if you want inline viewing.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit'   => Pages\EditBill::route('/{record}/edit'),
        ];
    }
}
