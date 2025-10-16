<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Directory';
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $pluralModelLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('role')
                ->label('Role')
                ->options([
                    'admin'    => 'Admin',
                    'customer' => 'Customer',
                ])
                ->native(false)
                ->required()
                ->default('admin'),

            Forms\Components\TextInput::make('username')
                ->label('Username')
                ->required()
                ->maxLength(80)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(160)
                ->unique(ignoreRecord: true),

            Forms\Components\Fieldset::make('Credentials')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->rule('min:8')
                        // only send to backend if filled
                        ->dehydrated(fn ($state) => filled($state))
                        // with Laravel 12 casts ['password' => 'hashed'] it will be hashed automatically
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                        ->required(fn (string $operation) => $operation === 'create'),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->same('password')
                        ->dehydrated(false),
                ])->columns(2),

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
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'primary' => 'customer',
                        'success' => 'admin',
                    ])
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
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
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(['admin' => 'Admin', 'customer' => 'Customer']),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record) => $record->status !== 'active')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update(['status' => 'active'])),

                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn (User $record) => $record->status !== 'inactive')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update(['status' => 'inactive'])),
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
            // add relation managers here if needed, e.g. AccountsRelationManager
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
