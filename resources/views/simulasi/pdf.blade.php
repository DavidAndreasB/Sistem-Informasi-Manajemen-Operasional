<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Perhitungan Biaya</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20pt;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header .subtitle {
            font-size: 10pt;
            color: #666;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            vertical-align: top;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 10pt;
        }

        table td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tfoot tr {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        table tfoot td {
            padding: 12px 8px;
            font-size: 12pt;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-success {
            color: #28a745;
        }

        .bold {
            font-weight: bold;
        }

        .small-text {
            font-size: 9pt;
            color: #666;
        }

        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>Simulasi Perhitungan Biaya</h1>
        <div class="subtitle">Penawaran Harga - Venus Tekindo</div>
    </div>

    {{-- Informasi Tanggal --}}
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Tanggal Cetak</div>
            <div class="info-value">: {{ $tanggal->translatedFormat('d F Y, H:i') }} WIB</div>
        </div>
        <div class="info-row">
            <div class="info-label">Keterangan</div>
            <div class="info-value">: Dokumen ini adalah simulasi perhitungan biaya untuk keperluan penawaran</div>
        </div>
    </div>

    {{-- Tabel Rincian --}}
    <div>
        <h3 style="margin-bottom: 10px;">Rincian Estimasi Biaya:</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">No</th>
                    <th style="width: 40%;">Uraian Pekerjaan</th>
                    <th style="width: 20%;" class="text-center">Tarif / Jam</th>
                    <th style="width: 15%;" class="text-center">Durasi (Jam)</th>
                    <th style="width: 20%;" class="text-right">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="bold">{{ $item['uraian'] }}</td>
                        <td class="text-center">Rp {{ number_format($item['tarif'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item['durasi'] }}</td>
                        <td class="text-right bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data simulasi.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right text-uppercase">Total Estimasi Biaya:</td>
                    <td class="text-right text-success">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #17a2b8;">
            <div class="small-text">
                <strong>Catatan:</strong><br>
                - Harga dapat berubah sewaktu-waktu tanpa pemberitahuan terlebih dahulu<br>
                - Simulasi ini bersifat estimasi dan tidak mengikat secara hukum<br>
                - Untuk penawaran resmi, silakan hubungi bagian penjualan
            </div>
        </div>

        <div class="small-text text-center" style="margin-top: 30px;">
            Dicetak pada: {{ $tanggal->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>
</body>

</html>