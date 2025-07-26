<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

$saldo = getSaldoKas();
$pemasukan_bulan_ini = getPemasukanBulanIni();
$pengeluaran_bulan_ini = getPengeluaranBulanIni();
?>

<h2 class="text-xl font-semibold mb-5">Dashboard</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <div class="bg-white p-6 rounded-lg shadow border">
    <h3 class="text-lg font-semibold mb-2 text-gray-700">Saldo Kas</h3>
    <p class="text-2xl font-bold"><?= formatRupiah($saldo) ?></p>
  </div>

  <div class="bg-white p-6 rounded-lg shadow border">
    <h3 class="text-lg font-semibold mb-2 text-gray-700">Pemasukan Bulan Ini</h3>
    <p class="text-2xl font-bold text-green-600"><?= formatRupiah($pemasukan_bulan_ini) ?></p>
  </div>

  <div class="bg-white p-6 rounded-lg shadow border">
    <h3 class="text-lg font-semibold mb-2 text-gray-700">Pengeluaran Bulan Ini</h3>
    <p class="text-2xl font-bold text-red-600"><?= formatRupiah($pengeluaran_bulan_ini) ?></p>
  </div>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-6">
  <h2 class="text-xl font-semibold mb-4">Riwayat Transaksi Terakhir</h2>

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
        <?php
        $query = "(SELECT tanggal_pemasukan as tanggal, 'Pemasukan' as jenis, sumber_pemasukan as keterangan, jumlah 
                          FROM pemasukan ORDER BY tanggal_pemasukan DESC LIMIT 5)
                          UNION ALL
                          (SELECT tanggal_pengeluaran as tanggal, 'Pengeluaran' as jenis, jenis_pengeluaran as keterangan, jumlah 
                          FROM pengeluaran ORDER BY tanggal_pengeluaran DESC LIMIT 5)
                          ORDER BY tanggal DESC LIMIT 10";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)):
        ?>
          <tr>
            <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= $row['jenis'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 capitalize"><?= $row['keterangan'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 font-semibold <?= $row['jenis'] === 'Pemasukan' ? 'text-green-600' : 'text-red-600' ?>">
              <?= formatRupiah($row['jumlah']) ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>