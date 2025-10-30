<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 20px;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .header img {
            float: left;
            height: 60px;
            margin-right: 10px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 13pt;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
        }

        .badge {
            background-color: #6c5ce7;
            color: white;
            font-size: 8pt;
            padding: 2px 5px;
            border-radius: 3px;
        }

        .small {
            font-size: 8pt;
            color: #555;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <img src="{{ public_path('img/Logo.png') }}" alt="Logo">
        <div>
            <div>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
            <div style="font-size: 13pt; font-weight: bold;">POLITEKNIK NEGERI MALANG</div>
            <div>Jl. Soekarno-Hatta No. 9 Malang 65141</div>
            <div>Telp (0341) 404424 Pes. 101-105, Fax. (0341) 404420</div>
            <div>Laman: www.polinema.ac.id</div>
        </div>
    </div>

    <div class="title">DAFTAR LAMARAN MAHASISWA</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Prodi</th>
                <th>No. Telp</th>
                <th>Lowongan</th>
                <th>Perusahaan</th>
                <th>Alamat Perusahaan</th>
                <th>Tanggal Lamaran</th>
                <th>Status</th>
                <th>Dosen Pembimbing</th>
                <th>Email Dosen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lamaran as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->mahasiswa->mhs_nim }}</td>
                <td>{{ $item->mahasiswa->full_name }}</td>
                <td>{{ $item->mahasiswa->prodi->nama_prodi ?? '-' }}</td>
                <td>{{ $item->mahasiswa->telp }}</td>
                <td>{{ $item->lowongan->judul ?? '-' }}</td>
                <td>{{ $item->lowongan->perusahaan->nama ?? '-' }}</td>
                <td>{{ $item->lowongan->perusahaan->alamat ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_lamaran)->format('d-m-Y') }}</td>
                <td><span class="badge">{{ ucfirst($item->status) }}</span></td>
                <td>{{ $item->dosen->nama ?? '-' }}</td>
                <td>{{ $item->dosen->email ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
