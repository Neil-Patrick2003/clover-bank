<?php
// app/Filament/Widgets/Concerns/InteractsWithDateRange.php

namespace App\Filament\Widgets\Concerns;

use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

trait InteractsWithDateRange
{
    /** Separate name to avoid collisions with InteractsWithForms::getFormSchema */
    protected function getDateRangeFormSchema(): array
    {
        return [
            DatePicker::make('from')
                ->label('From')
                ->default(now()->subDays(29))
                ->native(false)
                ->closeOnDateSelection(),
            DatePicker::make('to')
                ->label('To')
                ->default(now())
                ->native(false)
                ->closeOnDateSelection(),
        ];
    }

    protected function drFrom(): Carbon
    {
        $state = $this->form->getState();
        return Carbon::parse($state['from'] ?? now()->subDays(29))->startOfDay();
    }

    protected function drTo(): Carbon
    {
        $state = $this->form->getState();
        return Carbon::parse($state['to'] ?? now())->endOfDay();
    }

    protected function dateLabels(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $labels = [];
        $d = Carbon::parse($from)->copy();
        while ($d->lte($to)) {
            $labels[] = $d->format('M d');
            $d->addDay();
        }
        return $labels;
    }
}
