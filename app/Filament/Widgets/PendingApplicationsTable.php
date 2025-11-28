<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CustomerApplicationResource;
use App\Models\CustomerApplication;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PendingApplicationsTable extends BaseWidget
{
    protected static ?string $heading = 'Applications Awaiting Action';

    /** Layout: half width on large screens */
    protected int|string|array $columnSpan = "full";

    /** Optional live refresh */
    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        $query = CustomerApplication::query()
            ->whereIn('status', ['submitted', 'in_review'])
            ->with(['applicant', 'assignedAdmin'])
            ->latest('submitted_at');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('App#')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('applicant.email')
                    ->label('Customer')
                    ->searchable()
                    ->copyable()
                    ->limit(28),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'submitted',
                        'info'    => 'in_review',
                    ])
                    ->icons([
                        'heroicon-m-inbox'  => 'submitted',
                        'heroicon-m-eye'    => 'in_review',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedAdmin.email')
                    ->label('Assigned')
                    ->placeholder('—')
                    ->toggleable()
                    ->limit(24),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'in_review' => 'In Review',
                    ]),
                Tables\Filters\Filter::make('mine')
                    ->label('Assigned to me')
                    ->query(fn (Builder $q) => $q->where('assigned_admin_id', Auth::id())),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->recordUrl(fn (CustomerApplication $record) =>
            CustomerApplicationResource::getUrl('edit', ['record' => $record])
            )
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (CustomerApplication $record) =>
                    CustomerApplicationResource::getUrl('edit', ['record' => $record])
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('assignToMe')
                    ->label('Assign to me')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (CustomerApplication $record) => ! $record->assigned_admin_id)
                    ->requiresConfirmation()
                    ->action(fn (CustomerApplication $record) =>
                    $record->update(['assigned_admin_id' => Auth::id(), 'status' => 'in_review'])
                    ),

                Tables\Actions\Action::make('markInReview')
                    ->label('Mark In Review')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (CustomerApplication $record) => $record->status === 'submitted')
                    ->action(fn (CustomerApplication $record) =>
                    $record->update(['status' => 'in_review'])
                    ),
            ])
            ->paginated([5, 10, 25])
            ->striped()
            ->emptyStateHeading('No applications need action')
            ->emptyStateDescription('New or in-review applications will appear here.')
            ->emptyStateIcon('heroicon-o-inbox');
    }
}
