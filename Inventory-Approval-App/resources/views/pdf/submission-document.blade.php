<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Submission {{ $submission->proposal_id }}</title>
    <style>
        /* Mengatur ukuran kertas menjadi A4 dan margin */
        @page {
            size: A4;
            margin: 25mm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: left;
            margin-bottom: 35px;
        }

        .header h1 {
            color: #000;
            font-size: 35px;
            margin: 0;
        }
        
        /* Style untuk titik merah pada logo */
        .header h1 span {
            color: red;
        }

        .header p {
            margin: 0;
            font-size: 14px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .content-table th, .content-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .content-table th {
            background-color: #f2f2f2;
        }

        .footer {
            position: fixed;
            bottom: 25px;
            right: 0mm; /* Hanya atur posisi dari kanan */
            text-align: right;
        }

        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }
        
        /* Style untuk digital stamp */
        .digital-stamp {
            bottom: 30px; /* Posisi vertikal stamp */
            right: 0;   /* Posisi horizontal stamp */
            opacity: 0.2; /* Tingkat transparansi */
        }

        .signature-logo {
            font-size: 36px;
            font-weight: 900;
            color: #cccccc; /* Warna abu-abu muda */
            margin-bottom: 20px; /* Jarak antara logo dan nama */
        }

        .signature-logo span {
            color: #ff0000; /* Warna merah untuk titik */
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>WIT<span>.</span></h1>
        <p>The 360° Digital Transformation. Company</p>
    </div>

    <p>
        <strong>Nomor:</strong> {{ $document_number }}<br>
        <strong>Perihal:</strong> Persetujuan Pengajuan {{ $submission->type }} Barang
    </p>

    <p>Dengan Hormat,</p>
    <p>Berdasarkan pengajuan yang telah dikirimkan, dengan ini kami memberitahukan bahwa pengajuan {{ strtolower($submission->type) }} barang telah disetujui. Berikut adalah rincian dari pengajuan tersebut:</p>

    <table class="content-table">
        <tr>
            <th colspan="2">Detail Pengajuan</th>
        </tr>
        <tr>
            <td width="30%">ID Proposal</td>
            <td><strong>{{ $submission->proposal_id }}</strong></td>
        </tr>
        <tr>
            <td>Tipe Pengajuan</td>
            <td>{{ $submission->type }}</td>
        </tr>
         <tr>
            <td colspan="2" style="background-color:#f9f9f9;"><strong>Informasi Karyawan</strong></td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>{{ $submission->full_name }}</td>
        </tr>
        <tr>
            <td>ID Karyawan/NIP</td>
            <td>{{ $submission->employee_id }}</td>
        </tr>
         <tr>
            <td>Departemen</td>
            <td>{{ $submission->department }}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color:#f9f9f9;"><strong>Detail Barang & Tujuan</strong></td>
        </tr>
        <tr>
            <td>Nama Barang</td>
            <td>{{ $submission->item_name }}</td>
        </tr>
         <tr>
            <td>Jumlah</td>
            <td>{{ $submission->quantity }} Unit</td>
        </tr>
        <tr>
            <td>Tujuan</td>
            <td>{{ $submission->purpose_title }}</td>
        </tr>
        <tr>
            <td>Tanggal Penggunaan</td>
            <td>{{ \Carbon\Carbon::parse($submission->start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($submission->end_date)->format('d/m/Y') }}</td>
        </tr>
         <tr>
            <td>Deskripsi</td>
            <td>{{ $submission->type == 'Peminjaman' ? $submission->description : $submission->procurement_description }}</td>
        </tr>
    </table>

    <p>Demikian surat persetujuan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    <p>Atas perhatian dan kerjasamanya, kami ucapkan terimakasih.</p>


    {{-- Footer dipindahkan ke sini --}}
    <div class="footer">
        <p>Bandung, {{ date('d F Y') }}</p>
        <div>
            <div class="signature-logo">WIT<span>.</span></div>
            <p><u>"------------"</u><br>Chief Operating Officer</p>
        </div>
    </div>

</body>
</html>