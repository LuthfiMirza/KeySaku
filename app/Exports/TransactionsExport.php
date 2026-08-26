<?php

namespace App\Exports;

use App\Models\Household;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(private Household $household)
    {
    }

    public function collection(): Collection
    {
        return $this->household->transactions()
            ->with('category')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Kategori',
            'Nominal',
            'Catatan',
            'Sumber',
        ];
    }

    public function map($transaction): array
    {
        /** @var Transaction $transaction */
        return [
            $transaction->transaction_date->toDateString(),
            $transaction->type === Transaction::TYPE_INCOME ? 'Pemasukan' : 'Pengeluaran',
            $transaction->category?->name ?? 'Tanpa kategori',
            money($transaction->amount),
            $transaction->note,
            $transaction->source === Transaction::SOURCE_VOICE ? 'Suara' : 'Manual',
        ];
    }
}
