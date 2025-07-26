<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'semua';

$query_pemasukan = "(SELECT
    p.tanggal_pemasukan as tanggal,
    'Pemasukan' as jenis,
    p.sumber_pemasukan as keterangan,
    p.jumlah,
    p.user_id,
    p.tanggal_catat,
    u.name as dibuat_oleh
FROM
    pemasukan p
JOIN
    users u ON p.user_id = u.user_id
WHERE
    DATE_FORMAT(p.tanggal_pemasukan, '%Y-%m') = '$bulan'
)";

$query_pengeluaran = "(SELECT
    pe.tanggal_pengeluaran as tanggal,
    'Pengeluaran' as jenis,
    pe.jenis_pengeluaran as keterangan,
    pe.jumlah,
    pe.user_id,
    pe.tanggal_catat,
    u.name as dibuat_oleh
FROM
    pengeluaran pe
JOIN
    users u ON pe.user_id = u.user_id
WHERE
    DATE_FORMAT(pe.tanggal_pengeluaran, '%Y-%m') = '$bulan'
)";

if ($tipe == 'pemasukan') {
  $query = $query_pemasukan;
} elseif ($tipe == 'pengeluaran') {
  $query = $query_pengeluaran;
} else {
  $query = "$query_pemasukan UNION ALL $query_pengeluaran";
}

$query .= " ORDER BY tanggal_catat DESC";
$result = mysqli_query($conn, $query);
?>

<div class="flex justify-between items-center mb-6">
  <h2 class="text-xl font-semibold">Riwayat Transaksi</h2>
  <div>
    <form method="get" class="flex items-center justify-center space-x-2">
      <input type="month" name="bulan" value="<?= $bulan ?>" class="px-3 py-1 border border-gray-300 rounded">
      <select name="tipe" class="px-3 py-1 border border-gray-300 rounded">
        <option value="semua" <?= $tipe == 'semua' ? 'selected' : '' ?>>Semua</option>
        <option value="pemasukan" <?= $tipe == 'pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
        <option value="pengeluaran" <?= $tipe == 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
      </select>
      <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded">Filter</button>
      <a href="riwayat.php" class="bg-gray-200 px-3 py-1 rounded">Reset</a>
    </form>
  </div>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-4">
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Dicatat</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dibuat oleh</th>
          buat biar bisa liat keterangan tambahan
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y H:i', strtotime($row['tanggal_catat'])) ?></td>

            <td class="py-2 px-4 border-b border-gray-200"><?= $row['jenis'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 capitalize"><?= $row['keterangan'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 font-semibold <?= $row['jenis'] === 'Pemasukan' ? 'text-green-600' : 'text-red-600' ?>">
              <?= formatRupiah($row['jumlah']) ?>
            </td>
            <td class="py-2 px-4 border-b border-gray-200"><?= $row['dibuat_oleh'] ?></td>
          </tr>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result) === 0): ?>
          <tr>
            <td colspan="4" class="py-4 px-4 text-center text-gray-500">Tidak ada data transaksi</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>