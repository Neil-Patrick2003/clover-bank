<?php

namespace App\Filament\Resources\CustomerApplicationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Table;
use Filament\Forms\Form;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'KYC Documents';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('doc_type')
                ->options(['valid_id'=>'Valid ID','proof_of_address'=>'Proof of Address','other'=>'Other'])
                ->required(),
            Forms\Components\TextInput::make('file_url')->required(),
            Forms\Components\Select::make('verified_status')
                ->options(['pending'=>'Pending','verified'=>'Verified','rejected'=>'Rejected'])
                ->default('pending'),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doc_type'),
                Tables\Columns\TextColumn::make('file_url')->limit(40)->toggleable(),
                Tables\Columns\BadgeColumn::make('verified_status')
                    ->colors(['gray'=>'pending','success'=>'verified','danger'=>'rejected']),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->headerActions([ Tables\Actions\CreateAction::make(), ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
