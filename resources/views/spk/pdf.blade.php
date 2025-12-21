<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - {{ $spk->no_spk }}</title>
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

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10pt;
        }

        .status-draft {
            background-color: #e0e0e0;
            color: #333;
        }

        .status-diproses {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-selesai {
            background-color: #d4edda;
            color: #155724;
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

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 50px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            display: inline-block;
            width: 200px;
        }

        .no-border {
            border: none !important;
        }

        .bold {
            font-weight: bold;
        }

        .small-text {
            font-size: 9pt;
            color: #666;
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
        <h1>Surat Perintah Kerja</h1>
        <div class="subtitle">Venus Tekindo</div>
    </div>

    {{-- Informasi SPK --}}
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">No. SPK</div>
            <div class="info-value">: <strong>{{ $spk->no_spk }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal</div>
            <div class="info-value">: {{ \Carbon\Carbon::parse($spk->tanggal)->translatedFormat('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Pemesan</div>
            <div class="info-value">: {{ $spk->nama_pemesan }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Judul Proyek</div>
            <div class="info-value">: <strong>{{ $spk->judul_proyek }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Status</div>
            <div class="info-value">:
                <span class="status-badge 
                    @if($spk->status == 'Selesai') status-selesai 
                    @elseif($spk->status == 'Diproses') status-diproses 
                    @else status-draft 
                    @endif">
                    {{ $spk->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tabel Item --}}
    <div>
        <h3 style="margin-bottom: 10px;">Daftar Rincian Pekerjaan:</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">No</th>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 55%;">Rincian Spesifikasi</th>
                    <th style="width: 15%;" class="text-center">Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spk->items as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="bold">{{ $item->nama_barang }}</td>
                        <td>{!! nl2br(e($item->rincian)) !!}</td>
                        <td class="text-center bold">{{ $item->quantity }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada item rincian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer & Tanda Tangan --}}
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <div>Mengetahui,</div>
                <div class="signature-line"></div>
                <div style="margin-top: 5px;">Pemesan</div>
            </div>
            <div class="signature-box">
                <div>Dibuat Oleh,</div>
                <div class="signature-line"></div>
                <div style="margin-top: 5px;">{{ $spk->creator->username ?? 'Admin' }}</div>
            </div>
        </div>

        <div class="small-text text-center" style="margin-top: 30px;">
            Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>
</body>

</html>