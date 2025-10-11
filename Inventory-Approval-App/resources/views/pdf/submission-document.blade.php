<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Submission {{ $submission->proposal_id }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.2;
        }
        .header { text-align: left; margin-bottom: 30px; }
        .header h1 { color: #000; font-size: 36px; margin: 0; }
        .header h1 span { color: red; }
        .header p { margin: 0; font-size: 14px; }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .content-table th, .content-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10pt; /* Ukuran font di dalam tabel diperkecil */
        }
        .content-table th {
            background-color: #f2f2f2;
            font-size: 11pt;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 50px;
            right: 25mm;
            text-align: right;
        }
        .signature-logo {
            font-size: 36px;
            font-weight: 900;
            color: #cccccc;
            margin-bottom: 15px;
        }
        .signature-logo span {
            color: #ff0000;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>WIT<span>.</span></h1>
        <p>The 360° Digital Transformation. Company</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Nomor:</strong> {{ $document_number }}<br>
                <strong>Perihal:</strong> Persetujuan Pengajuan {{ $submission->type }} Barang
            </td>
            <td width="50%" style="text-align: right;">
                Bandung, {{ date('d F Y') }}
            </td>
        </tr>
    </table>
    
    <p style="margin-top: 20px;">Dengan Hormat,</p>
    <p>Berdasarkan pengajuan yang telah dikirimkan, dengan ini kami memberitahukan bahwa pengajuan {{ strtolower($submission->type) }} barang telah disetujui. Berikut adalah rincian dari pengajuan tersebut:</p>

    {{-- Informasi Ringkas Pengajuan & Karyawan --}}
    <table class="content-table">
        <tr>
            <td width="25%"><strong>ID Proposal</strong></td>
            <td width="25%">{{ $submission->proposal_id }}</td>
            <td width="25%"><strong>Nama Pemohon</strong></td>
            <td width="25%">{{ $submission->full_name }}</td>
        </tr>
        <tr>
            <td><strong>Tipe Pengajuan</strong></td>
            <td>{{ $submission->type }}</td>
            <td><strong>Departemen</strong></td>
            <td>{{ $submission->department }}</td>
        </tr>
    </table>

    {{-- Detail Barang (Dinamis) --}}
    <table class="content-table">
        <tr>
            <th colspan="2">Detail Barang</th>
        </tr>
        @if ($submission->type == 'Peminjaman')
            <tr>
                <td width="30%">Nama Properti</td>
                <td>{{ $submission->inventory->nama }}</td>
            </tr>
            <tr>
                <td>Kode Properti</td>
                <td>{{ $submission->inventory->kode }}</td>
            </tr>
            @if ($submission->inventory->kategori !== 'Ruangan')
                <tr>
                    <td>Brand</td>
                    <td>{{ $submission->inventory->brand ?? '-' }}</td>
                </tr>
            @endif
            <tr>
                <td>Branch</td>
                <td>{{ $submission->inventory->branch }}</td>
            </tr>
        @else {{-- Untuk Pengadaan --}}
            <tr>
                <td width="30%">Nama Barang</td>
                <td>{{ $submission->item_name }}</td>
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

    {{-- Detail Tujuan Pengajuan --}}
    <table class="content-table">
        <tr>
            <th colspan="2">Detail Tujuan Pengajuan</th>
        </tr>
        <tr>
            <td width="30%">Judul Tujuan</td>
            <td>{{ $submission->purpose_title }}</td>
        </tr>
        <tr>
            <td>Jumlah</td>
            <td>{{ $submission->quantity }} Unit</td>
        </tr>
        <tr>
            <td>Tanggal Penggunaan</td>
            <td>{{ \Carbon\Carbon::parse($submission->start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($submission->end_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Deskripsi Pengajuan</td>
            <td>{{ $submission->type == 'Peminjaman' ? $submission->description : $submission->procurement_description }}</td>
        </tr>
    </table>

    <p>Demikian surat persetujuan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    <p>Atas perhatian dan kerjasamanya, kami ucapkan terimakasih.</p>

    <div class="footer">
        <div style="margin-top: 30px;">
            <div class="signature-logo">WIT<span>.</span></div>
            <p><u>"------------"</u><br>Chief Operating Officer</p>
        </div>
    </div>

</body>
</html>