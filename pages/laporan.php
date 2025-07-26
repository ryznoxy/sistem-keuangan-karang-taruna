<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

$query_pemasukan = "SELECT SUM(jumlah) as total FROM pemasukan 
                   WHERE DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = '$bulan'";
$result_pemasukan = mysqli_query($conn, $query_pemasukan);
$total_pemasukan = mysqli_fetch_assoc($result_pemasukan)['total'] ?? 0;

$query_pengeluaran = "SELECT SUM(jumlah) as total FROM pengeluaran 
                     WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = '$bulan'";
$result_pengeluaran = mysqli_query($conn, $query_pengeluaran);
$total_pengeluaran = mysqli_fetch_assoc($result_pengeluaran)['total'] ?? 0;

$saldo = $total_pemasukan - $total_pengeluaran;

$query_transaksi = "(SELECT tanggal_pemasukan as tanggal, 'Pemasukan' as jenis, sumber_pemasukan as keterangan, jumlah 
                   FROM pemasukan WHERE DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = '$bulan')
                   UNION ALL
                   (SELECT tanggal_pengeluaran as tanggal, 'Pengeluaran' as jenis, jenis_pengeluaran as keterangan, jumlah 
                   FROM pengeluaran WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = '$bulan')
                   ORDER BY tanggal";
$result_transaksi = mysqli_query($conn, $query_transaksi);
?>

<div class="flex justify-between items-center mb-6">
  <h2 class="text-xl font-semibold">Laporan Keuangan</h2>
  <div>
    <form method="get" class="flex items-center space-x-2">
      <input type="month" name="bulan" value="<?= $bulan ?>" class="px-3 py-1 border border-gray-300 rounded">
      <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded">Filter</button>
      <a href="laporan.php" class="bg-gray-200 px-3 py-1 rounded">Reset</a>
    </form>
  </div>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-6">
  <h3 class="text-lg font-semibold mb-4">Ringkasan Bulan <?= date('F Y', strtotime($bulan . '-01')) ?></h3>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="border border-gray-200 p-4 rounded-md">
      <h4 class="text-sm font-medium text-gray-500 mb-1">Total Pemasukan</h4>
      <p class="text-xl font-semibold text-green-600"><?= formatRupiah($total_pemasukan) ?></p>
    </div>

    <div class="border border-gray-200 p-4 rounded-md">
      <h4 class="text-sm font-medium text-gray-500 mb-1">Total Pengeluaran</h4>
      <p class="text-xl font-semibold text-red-600"><?= formatRupiah($total_pengeluaran) ?></p>
    </div>

    <div class="border border-gray-200 p-4 rounded-md">
      <h4 class="text-sm font-medium text-gray-500 mb-1">Saldo Akhir</h4>
      <p class="text-xl font-semibold !text-black <?= $saldo >= 0 ? 'text-green-600' : 'text-red-600' ?>">
        <?= formatRupiah($saldo) ?>
      </p>
    </div>
  </div>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-4  ">
  <h3 class="text-lg font-semibold mb-4">Detail Transaksi</h3>

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
          <tr>
            <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= $row['jenis'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= $row['keterangan'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 font-semibold <?= $row['jenis'] === 'Pemasukan' ? 'text-green-600' : 'text-red-600' ?>">
              <?= formatRupiah($row['jumlah']) ?>
            </td>
          </tr>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result_transaksi) === 0): ?>
          <tr>
            <td colspan="4" class="py-4 px-4 text-center text-gray-500">Tidak ada data transaksi</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>