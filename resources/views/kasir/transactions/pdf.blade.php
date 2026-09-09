<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        @page {
            margin: 25px;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 4px 0;
            font-size: 16px;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .info-table td {
            padding: 2px 0;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
        }
        table.data th {
            background-color: #f2f2f2;
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        table.data td {
            border: 1px solid #ccc;
            padding: 6px;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-box {
            margin-top: 15px;
            width: 250px;
            float: right;
        }
        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 6px;
            border: 1px solid #ccc;
            font-size: 11px;
        }
        .summary-box .label {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #999;
            text-align: center;
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Riwayat Transaksi</h1>
        <p>Kasir & Warehouse System</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Periode</strong></td>
            <td width="2%">:</td>
            <td>
                @if ($dateFrom && $dateTo)
                    {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                @else
                    Semua periode
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Dicetak pada</strong></td>
            <td>:</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Total Transaksi</strong></td>
            <td>:</td>
            <td>{{ $transactions->count() }} transaksi</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Invoice</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Kasir</th>
                <th width="12%">Metode</th>
                <th width="16%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->invoice_number }}</td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->user->name ?? '-' }}</td>
                    <td>{{ strtoupper($transaction->payment_method) }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Total Omzet</td>
                <td class="text-right">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh sistem Kasir & Warehouse.
    </div>

</body>
</html>
