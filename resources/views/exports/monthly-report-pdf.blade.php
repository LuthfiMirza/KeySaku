<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan — {{ $monthLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitle { color: #64748b; margin-bottom: 18px; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary td { padding: 8px 12px; border: 1px solid #e2e8f0; }
        .summary td.label { color: #64748b; width: 33%; }
        .summary td.value { font-weight: bold; text-align: right; }
        .income { color: #059669; }
        .expense { color: #e11d48; }
        h2 { font-size: 14px; margin-top: 24px; margin-bottom: 8px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { padding: 6px 10px; border: 1px solid #e2e8f0; text-align: left; }
        table.data th { background: #f8fafc; font-size: 11px; text-transform: uppercase; color: #64748b; }
        table.data td.amount { text-align: right; }
        .footer { margin-top: 24px; color: #94a3b8; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Bulanan KeySaku</h1>
    <p class="subtitle">{{ $monthLabel }} — {{ $household->name ?: 'Keluarga' }}</p>

    <table class="summary">
        <tr>
            <td class="label">Total Pemasukan</td>
            <td class="value income">{{ money($totalIncome) }}</td>
        </tr>
        <tr>
            <td class="label">Total Pengeluaran</td>
            <td class="value expense">{{ money($totalExpense) }}</td>
        </tr>
        <tr>
            <td class="label">Saldo Bulan Ini</td>
            <td class="value">{{ money($totalIncome - $totalExpense) }}</td>
        </tr>
    </table>

    <h2>Pengeluaran per Kategori</h2>
    @if ($expenseByCategory->isEmpty())
        <p>Belum ada pengeluaran bulan ini.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenseByCategory as $row)
                    <tr>
                        <td>{{ $row['category']?->name ?? 'Tanpa kategori' }}</td>
                        <td class="amount">{{ money($row['total']) }}</td>
                        <td class="amount">{{ $row['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Daftar Transaksi</h2>
    @if ($transactions->isEmpty())
        <p>Belum ada transaksi bulan ini.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                        <td>{{ $transaction->type === \App\Models\Transaction::TYPE_INCOME ? 'Pemasukan' : 'Pengeluaran' }}</td>
                        <td>{{ $transaction->category?->name ?? 'Tanpa kategori' }}</td>
                        <td class="amount">{{ money($transaction->amount) }}</td>
                        <td>{{ $transaction->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="footer">Dibuat lewat KeySaku pada {{ now()->translatedFormat('d M Y H:i') }}</p>
</body>
</html>
