<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KycProfileResource\Pages;
use App\Models\KycProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class KycProfileResource extends Resource
{
    protected static ?string $model = KycProfile::class;

    protected static ?string $navigationGroup  = 'Compliance & KYC';
    protected static ?string $navigationIcon   = 'heroicon-o-shield-check';
    protected static ?string $modelLabel       = 'KYC Profile';
    protected static ?string $pluralModelLabel = 'KYC Profiles';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Fieldset::make('Customer')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Customer')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'email',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('email')
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        // Only allow one KYC per user (ignore current record on edit)
                        ->rule(fn (Get $get, ?KycProfile $record) =>
                        Rule::unique('kyc_profiles', 'user_id')->ignore($record?->id)
                        )
                        // optional: inline create a customer
                        ->createOptionForm([
                            Forms\Components\TextInput::make('username')->required()->maxLength(80),
                            Forms\Components\TextInput::make('email')->email()->required()->maxLength(160),
                            Forms\Components\TextInput::make('password')->password()->required()->rule('min:8'),
                            Forms\Components\Hidden::make('role')->default('customer'),
                            Forms\Components\Hidden::make('status')->default('active'),
                        ])
                        ->createOptionUsing(fn (array $data) => User::create($data)->getKey()),
                ])->columns(1),

            Forms\Components\Fieldset::make('KYC Details')
                ->schema([
                    Forms\Components\Select::make('kyc_level')
                        ->label('KYC Level')
                        ->options([
                            'basic'    => 'Basic',
                            'standard' => 'Standard',
                            'enhanced' => 'Enhanced',
                        ])
                        ->native(false)
                        ->required()
                        ->default('basic'),

                    Forms\Components\Select::make('id_type')
                        ->label('ID Type')
                        ->options([
                            'passport'        => 'Passport',
                            'national_id'     => 'National ID',
                            'driver_license'  => 'Driver’s License',
                            'sss'             => 'SSS',
                            'umid'            => 'UMID',
                            'other'           => 'Other',
                        ])
                        ->native(false),

                    Forms\Components\TextInput::make('id_number')
                        ->label('ID Number')
                        ->maxLength(128),

                    Forms\Components\DatePicker::make('id_expiry')
                        ->label('ID Expiry')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->hint('Leave empty if no expiry'),
                ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('kyc_level')
                    ->label('Level')
                    ->colors([
                        'gray'    => 'basic',
                        'warning' => 'standard',
                        'success' => 'enhanced',
                    ])->sortable(),

                Tables\Columns\TextColumn::make('id_type')
                    ->label('ID Type')
                    ->sortable(),

                Tables\Columns\TextColumn::make('id_number')
                    ->label('ID Number')
                    ->limit(24)
                    ->searchable(),

                Tables\Columns\TextColumn::make('id_expiry')
                    ->label('Expiry')
                    ->date()
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
                Tables\Filters\SelectFilter::make('kyc_level')
                    ->options([
                        'basic'    => 'Basic',
                        'standard' => 'Standard',
                        'enhanced' => 'Enhanced',
                    ]),
                Tables\Filters\SelectFilter::make('id_type')
                    ->options([
                        'passport'        => 'Passport',
                        'national_id'     => 'National ID',
                        'driver_license'  => 'Driver’s License',
                        'sss'             => 'SSS',
                        'umid'            => 'UMID',
                        'other'           => 'Other',
                    ]),
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
            // Add relation managers here (e.g., attached documents/photos) if needed.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKycProfiles::route('/'),
            'create' => Pages\CreateKycProfile::route('/create'),
            'edit'   => Pages\EditKycProfile::route('/{record}/edit'),
        ];
    }
}
