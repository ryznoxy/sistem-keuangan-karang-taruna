<?php
include '../includes/functions.php';
include '../includes/config.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_$bulan.xls");
header("Pragma: no-cache");
header("Expires: 0");

$query_transaksi = "(SELECT tanggal_pemasukan as tanggal, 'Pemasukan' as jenis, sumber_pemasukan as keterangan, jumlah 
                   FROM pemasukan WHERE DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = '$bulan')
                   UNION ALL
                   (SELECT tanggal_pengeluaran as tanggal, 'Pengeluaran' as jenis, jenis_pengeluaran as keterangan, jumlah 
                   FROM pengeluaran WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = '$bulan')
                   ORDER BY tanggal";
$result = mysqli_query($conn, $query_transaksi);

$query_pemasukan = "SELECT SUM(jumlah) as total FROM pemasukan WHERE DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = '$bulan'";
$total_pemasukan = mysqli_fetch_assoc(mysqli_query($conn, $query_pemasukan))['total'] ?? 0;

$query_pengeluaran = "SELECT SUM(jumlah) as total FROM pengeluaran WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = '$bulan'";
$total_pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, $query_pengeluaran))['total'] ?? 0;

$total_semua = $total_pemasukan + $total_pengeluaran;
$saldo = $total_pemasukan - $total_pengeluaran;

echo "<table border='1'>";
echo "<tr style='background-color:#f2f2f2; font-weight:bold;'>
        <th>Tanggal</th>
        <th>Jenis</th>
        <th>Keterangan</th>
        <th>Jumlah</th>
      </tr>";

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
    echo "<td>" . $row['jenis'] . "</td>";
    echo "<td>" . $row['keterangan'] . "</td>";
    echo "<td>" . formatRupiah($row['jumlah']) . "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='4' align='center'>Tidak ada data</td></tr>";
}

// Tambahkan total di bawah
echo "<tr style='font-weight:bold; background-color:#e8f5e9;'>
        <td colspan='3' align='right'>Total Pemasukan:</td>
        <td>" . formatRupiah($total_pemasukan) . "</td>
      </tr>";
echo "<tr style='font-weight:bold; background-color:#ffebee;'>
        <td colspan='3' align='right'>Total Pengeluaran:</td>
        <td>" . formatRupiah($total_pengeluaran) . "</td>
      </tr>";
echo "<tr style='font-weight:bold; background-color:#fff9c4;'>
        <td colspan='3' align='right'>Total Semua Uang (Pemasukan + Pengeluaran):</td>
        <td>" . formatRupiah($total_semua) . "</td>
      </tr>";
echo "<tr style='font-weight:bold; background-color:#e3f2fd;'>
        <td colspan='3' align='right'>Saldo Akhir (Pemasukan - Pengeluaran):</td>
        <td>" . formatRupiah($saldo) . "</td>
      </tr>";

echo "</table>";
