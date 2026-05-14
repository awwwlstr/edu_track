<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>

body {
    font-family: Arial, sans-serif;
    font-size: 11px;
    color: #000;
    margin: 20px 30px;
}

/* ================= KOP ================= */
.kop {
    border-bottom: 3px double #000;
    padding-bottom: 8px;
    margin-bottom: 12px;
}

.kop table {
    width: 100%;
    border-collapse: collapse;
}

.logo {
    width: 70px;
    vertical-align: top;
}

.logo img {
    width: 60px;
}

.kop-text {
    text-align: center;
}

.instansi {
    font-size: 10px;
    letter-spacing: 1px;
}

.nama {
    font-size: 16px;
    font-weight: bold;
    margin: 2px 0;
    letter-spacing: 0.5px;
}

.alamat {
    font-size: 10px;
    line-height: 1.3;
}

/* ================= JUDUL ================= */
.judul {
    text-align: center;
    margin: 8px 0 12px;
}

.judul h2 {
    font-size: 13px;
    font-weight: bold;
    margin: 0;
}

.judul p {
    font-size: 10px;
    margin-top: 2px;
}

/* ================= INFO ================= */
.info {
    width: 100%;
    margin-bottom: 10px;
}

.info td {
    font-size: 10px;
    vertical-align: top;
}

/* ================= TABEL ================= */
table.data {
    width: 100%;
    border-collapse: collapse;
}

table.data th {
    background: #2c3e50;
    color: #fff;
    padding: 6px;
    font-size: 10px;
}

table.data td {
    border: 1px solid #444;
    padding: 5px;
    font-size: 10px;
}

table.data tr:nth-child(even) {
    background: #f2f2f2;
}

/* ================= TTD ================= */
.ttd {
    margin-top: 25px;
}

/* ================= FOOTER ================= */
.footer {
    margin-top: 15px;
    font-size: 9px;
    text-align: center;
    color: #555;
}

</style>
</head>

<body>

<!-- ================= KOP ================= -->
<div class="kop">
    <table>
        <tr>
            <!-- KIRI (LOGO) -->
            <td class="logo">
                @if(file_exists(public_path('images/logo-sekolah.png')))
                    <img src="{{ public_path('images/logo-sekolah.png') }}">
                @endif
            </td>

            <!-- TENGAH (TEXT) -->
            <td class="kop-text">
                <div class="instansi">YAYASAN HIDAYATULLAH</div>
                <div class="nama">SD ISLAM TERPADU HIDAYATULLAH</div>
                <div class="alamat">
                    Jl. Sirayu RT 04 RW 02, Kel. Jatirejo, Kec. Gunungpati<br>
                    Kota Semarang
                </div>
            </td>

            <!-- KANAN (SPACER BIAR CENTER) -->
            <td style="width:70px;"></td>
        </tr>
    </table>
</div>

<!-- ================= JUDUL ================= -->
<div class="judul">
    <h2>REKAP ABSENSI {{ strtoupper($judul ?? 'PEGAWAI') }}</h2>
    <p>Periode: {{ $bulanLabel ?? '-' }}</p>
</div>

<!-- ================= INFO ================= -->
<table class="info">
<tr>
    <td width="50%">
        Tahun Pelajaran : {{ $tahunPelajaran ?? '-' }}<br>
        Semester : {{ $semester ?? '-' }}<br>
        Total Data : {{ count($absensi) }}
    </td>

    <td width="50%" align="center">
        Dicetak oleh : {{ auth()->user()->nama ?? '-' }}<br>
        Tanggal : {{ now()->translatedFormat('d F Y') }}<br>
        Pukul : {{ now()->format('H:i') }} WIB
    </td>
</tr>
</table>

<!-- ================= TABEL ================= -->
<table class="data">
<thead>
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Nama Guru</th>
    <th>NIP</th>
    <th>Masuk</th>
    <th>Keluar</th>
    <th>Status</th>
    <th>Keterangan</th>
</tr>
</thead>
<tbody>
@forelse($absensi as $key => $item)
<tr>
    <td>{{ $key+1 }}</td>
    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
    <td>{{ $item->user->nama ?? '-' }}</td>
    <td>{{ $item->user->nip ?? '-' }}</td>
    <td>{{ $item->jam_masuk ?? '-' }}</td>
    <td>{{ $item->jam_keluar ?? '-' }}</td>
    <td>{{ ucfirst($item->status) }}</td>
    <td>{{ $item->keterangan ?? '-' }}</td>
</tr>
@empty
<tr>
    <td colspan="8" align="center">Tidak ada data</td>
</tr>
@endforelse
</tbody>
</table>

<!-- ================= TTD ================= -->
<table width="100%" class="ttd">
<tr>
    <td width="60%"></td>
    <td width="40%" align="center">
        Semarang, {{ now()->translatedFormat('d F Y') }}<br>
        Kepala Sekolah<br><br><br>

        <b>{{ config('sekolah.kepala_sekolah','...') }}</b><br>
        NIP. {{ config('sekolah.nip_kepala','...') }}
    </td>
</tr>
</table>

<!-- ================= FOOTER ================= -->
<div class="footer">
    Dicetak otomatis oleh Sistem Absensi {{ config('sekolah.nama','') }} · {{ now()->format('d/m/Y H:i:s') }}
</div>

</body>
</html>