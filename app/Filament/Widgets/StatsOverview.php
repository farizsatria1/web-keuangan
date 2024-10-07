<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected function formatRupiah($value)
    {
        return 'Rp. ' . number_format($value, 0, ',', '.');
    }

    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $pemasukan = Transaction::incomes()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $pengeluaran = Transaction::expenses()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $sisa = $pemasukan - $pengeluaran;
        return [
            Stat::make('Total Pemasukan', $this->formatRupiah($pemasukan)),
            Stat::make('Total Pengeluaran', $this->formatRupiah($pengeluaran)),
            Stat::make('Sisa', $this->formatRupiah($sisa)),
        ];
    }
}
