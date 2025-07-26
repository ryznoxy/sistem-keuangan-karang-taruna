<?php
include 'config.php';

function formatRupiah($angka)
{
  return 'Rp ' . number_format($angka, 2, ',', '.');
}

function getSaldoKas()
{
  global $conn;

  $query_pemasukan = "SELECT SUM(jumlah) as total_pemasukan FROM pemasukan";
  $result_pemasukan = mysqli_query($conn, $query_pemasukan);
  $row_pemasukan = mysqli_fetch_assoc($result_pemasukan);
  $total_pemasukan = $row_pemasukan['total_pemasukan'] ?? 0;

  $query_pengeluaran = "SELECT SUM(jumlah) as total_pengeluaran FROM pengeluaran";
  $result_pengeluaran = mysqli_query($conn, $query_pengeluaran);
  $row_pengeluaran = mysqli_fetch_assoc($result_pengeluaran);
  $total_pengeluaran = $row_pengeluaran['total_pengeluaran'] ?? 0;

  return $total_pemasukan - $total_pengeluaran;
}

function getPemasukanBulanIni()
{
  global $conn;
  $bulan_ini = date('Y-m');
  $query = "SELECT SUM(jumlah) as total FROM pemasukan WHERE DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = '$bulan_ini'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  return $row['total'] ?? 0;
}

function getPengeluaranBulanIni()
{
  global $conn;
  $bulan_ini = date('Y-m');
  $query = "SELECT SUM(jumlah) as total FROM pengeluaran WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = '$bulan_ini'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  return $row['total'] ?? 0;
}
