<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
        }
        .date {
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="date">Dibuat pada: {{ $date }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Pegawai</th>
                <th>Dinas</th>
                <th>Status</th>
                <th>Tanggal Pengajuan</th>
                <th>Tanggal Verifikasi Dinas</th>
                <th>Tanggal Verifikasi Kabupaten</th>
                <th>Tanggal Approve</th>
                <th>Tanggal Selesai</th>
                <th>Jenis Pengajuan</th>
                <th>No SK</th>
                <th>TMT KGB Baru</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengajuanData as $index => $pengajuan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pengajuan->pegawai?->nip ?? '' }}</td>
                <td>{{ $pengajuan->pegawai?->nama ?? '' }}</td>
                <td>{{ $pengajuan->tenant?->nama ?? '' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}</td>
                <td>{{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d/m/Y') : '' }}</td>
                <td>{{ $pengajuan->tanggal_verifikasi_dinas ? $pengajuan->tanggal_verifikasi_dinas->format('d/m/Y') : '' }}</td>
                <td>{{ $pengajuan->tanggal_verifikasi_kabupaten ? $pengajuan->tanggal_verifikasi_kabupaten->format('d/m/Y') : '' }}</td>
                <td>{{ $pengajuan->tanggal_approve ? $pengajuan->tanggal_approve->format('d/m/Y') : '' }}</td>
                <td>{{ $pengajuan->tanggal_selesai ? $pengajuan->tanggal_selesai->format('d/m/Y') : '' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $pengajuan->jenis_pengajuan)) }}</td>
                <td>{{ $pengajuan->no_sk ?? '' }}</td>
                <td>{{ $pengajuan->tmt_kgb_baru ? $pengajuan->tmt_kgb_baru->format('d/m/Y') : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        Laporan dibuat melalui Sistem Pengajuan KGB PNS
    </div>
</body>
</html>