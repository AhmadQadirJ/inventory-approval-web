<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Detail - {{ $submission->proposal_id }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.2;
        }
        .container {
            width: 100%;
        }
        h1 {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            margin-bottom: 25px;
        }
        h2 {
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            background-color: #f2f2f2;
            padding: 8px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 3px solid #e12d38;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .detail-table td:first-child {
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        .timeline-list {
            list-style: none;
            padding-left: 0;
            margin-top: 10px;
        }
        .timeline-list li {
            position: relative;
            padding-left: 25px;
            margin-bottom: 10px;
        }
        .timeline-list li::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #28a745;
        }
        .timeline-list .in-progress::before {
            background-color: #6c757d;
        }
        .timeline-status {
            font-weight: bold;
        }
        .timeline-meta {
            font-size: 8pt;
            color: #666;
        }
        .timeline-notes {
            font-size: 9pt;
            color: #444;
            border-left: 2px solid #ddd;
            padding-left: 10px;
            margin-top: 4px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submission Details</h1>

        {{-- DETAIL UTAMA --}}
        <h2>Detail Pengajuan</h2>
        <table class="detail-table">
            <tr>
                <td>ID Proposal</td>
                <td>{{ $submission->proposal_id }}</td>
            </tr>
            <tr>
                <td>Tipe Pengajuan</td>
                <td>{{ $submission->type }}</td>
            </tr>
             <tr>
                <td>Status Saat Ini</td>
                <td>{{ $submission->status }}</td>
            </tr>
        </table>

        {{-- INFORMASI KARYAWAN --}}
        <h2>Informasi Karyawan</h2>
        <table class="detail-table">
            <tr>
                <td>Nama Lengkap</td>
                <td>{{ $submission->full_name }}</td>
            </tr>
            <tr>
                <td>ID Karyawan/NIP</td>
                <td>{{ $submission->employee_id }}</td>
            </tr>
            <tr>
                <td>Branch</td>
                <td>{{ $submission->branch }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>{{ $submission->department }}</td>
            </tr>
        </table>

        <h2>Detail Barang</h2>
        <table class="detail-table">
            @if ($submission->type == 'Peminjaman')
                <tr>
                    <td>Nama Properti</td>
                    <td>{{ $submission->inventory->nama }}</td>
                </tr>
                <tr>
                    <td>Kode Properti</td>
                    <td>{{ $submission->inventory->kode }}</td>
                </tr>
                @if ($submission->inventory->kategori !== 'Ruangan')
                    <tr>
                        <td>Brand Properti</td>
                        <td>{{ $submission->inventory->brand ?? '-' }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Jumlah Dipinjam</td>
                    <td>{{ $submission->quantity }} Unit</td>
                </tr>
            @else {{-- Pengadaan --}}
                <tr>
                    <td>Nama Barang</td>
                    <td>{{ $submission->item_name }}</td>
                </tr>
                <tr>
                    <td>Jumlah</td>
                    <td>{{ $submission->quantity }} Unit</td>
                </tr>
                <tr>
                    <td>Estimasi Harga</td>
                    <td>Rp {{ number_format($submission->estimated_price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Link Referensi</td>
                    <td>{{ $submission->reference_link }}</td>
                </tr>
                <tr>
                    <td>Deskripsi Barang</td>
                    <td>{{ $submission->item_description }}</td>
                </tr>
            @endif
        </table>

        <h2>Detail Tujuan Pengajuan</h2>
        <table class="detail-table">
            <tr>
                <td>Judul Tujuan</td>
                <td>{{ $submission->purpose_title }}</td>
            </tr>
            <tr>
                <td>Tanggal Penggunaan</td>
                <td>{{ \Carbon\Carbon::parse($submission->start_date)->format('d F Y') }} - {{ \Carbon\Carbon::parse($submission->end_date)->format('d F Y') }}</td>
            </tr>

            {{-- Baris baru yang hanya muncul untuk proposal Peminjaman --}}
            @if ($submission->type == 'Peminjaman')
                <tr>
                    <td>Jam Penggunaan</td>
                    <td>{{ \Carbon\Carbon::parse($submission->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($submission->end_time)->format('H:i') }}</td>
                </tr>
            @endif

            <tr>
                <td>Deskripsi Pengajuan</td>
                <td>{{ $submission->type == 'Peminjaman' ? $submission->description : $submission->procurement_description }}</td>
            </tr>
        </table>

        {{-- TIMELINE STATUS --}}
        <h2>Timeline Status</h2>
        <ul class="timeline-list">
            @forelse($submission->timelines->sortBy('created_at') as $timeline)
            <li>
                <div class="timeline-status">{{ $timeline->status }}</div>
                <div class="timeline-meta">{{ $timeline->created_at->format('d F Y, H:i') }} WIB by {{ $timeline->user->name }} ({{$timeline->user->role}})</div>
                @if($timeline->notes && !Str::contains(strtolower($timeline->notes), 'created by user'))
                    <div class="timeline-notes">"{{ $timeline->notes }}"</div>
                @endif
            </li>
            @empty
            <li>Tidak ada riwayat status yang tercatat.</li>
            @endforelse

            {{-- Menampilkan status saat ini jika belum selesai --}}
           @if(!Str::startsWith($submission->status, 'Accepted') && !Str::startsWith($submission->status, 'Rejected'))
                <li class="in-progress">
                    <div class="timeline-status">{{ $submission->status }}</div>
                    <div class="timeline-meta">On Progress</div>
                </li>
            @endif
        </ul>

    </div>
</body>
</html>