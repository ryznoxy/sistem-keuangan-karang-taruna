<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'semua';

$query_pemasukan = "(SELECT
    p.id_pemasukan as id,
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
    pe.id_pengeluaran as id,
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
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ket. Lengkap</th>
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
            <td class="py-2 px-4 border-b border-gray-200">
              <a href="riwayat.php?bulan=<?= $bulan ?>&tipe=<?= $tipe ?>&lihat_lengkap=1&id=<?= $row['id'] ?>&jenis=<?= strtolower($row['jenis']) ?>" class="px-2 py-1 border rounded">
                👁️
              </a>
            </td>
          </tr>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result) === 0): ?>
          <tr>
            <td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada data transaksi</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
if (isset($_GET['lihat_lengkap'])):
  $id = $_GET['id'] ?? '';
  $jenis = $_GET['jenis'] ?? '';

  $queryPemasukan = "SELECT
        p.id_pemasukan AS id,
        p.tanggal_pemasukan AS tanggal,
        'Pemasukan' AS jenis,
        p.sumber_pemasukan AS keterangan,
        p.jumlah,
        p.user_id,
        p.tanggal_catat,
        p.keterangan AS keterangan_tambahan,
        u.name AS dibuat_oleh
    FROM pemasukan p
    JOIN users u ON p.user_id = u.user_id
    WHERE p.id_pemasukan = '$id'";

  $queryPengeluaran = "SELECT
        pe.id_pengeluaran AS id,
        pe.tanggal_pengeluaran AS tanggal,
        'Pengeluaran' AS jenis,
        pe.jenis_pengeluaran AS keterangan,
        pe.jumlah,
        pe.user_id,
        pe.tanggal_catat,
        pe.keterangan AS keterangan_tambahan,
        u.name AS dibuat_oleh
    FROM pengeluaran pe
    JOIN users u ON pe.user_id = u.user_id
    WHERE pe.id_pengeluaran = '$id'";

  if ($jenis === 'pemasukan') {
    $resultDetail = mysqli_query($conn, $queryPemasukan);
  } elseif ($jenis === 'pengeluaran') {
    $resultDetail = mysqli_query($conn, $queryPengeluaran);
  }
  $data = mysqli_fetch_assoc($resultDetail);
?>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
      <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-4">
        <h2 class="text-lg font-semibold">Keterangan Lengkap</h2>
        <a href="riwayat.php?bulan=<?= $bulan ?>&tipe=<?= $tipe ?>" class="text-xl font-bold">✕</a>
      </div>
      <div class="space-y-2">
        <p>ID: <span class="font-medium"><?= $data['id'] ?> </span> </p>
        <p>Tanggal: <span class="font-medium"><?= date('d/m/Y', strtotime($data['tanggal'])) ?></span></p>
        <p>Tanggal Catat: <span class="font-medium"><?= date('d/m/Y', strtotime($data['tanggal_catat'])) ?></span></p>
        <p>Jenis: <span class="font-medium"><?= $data['jenis'] ?></span></p>
        <p>Keterangan: <span class="font-medium"><?= $data['keterangan'] ?></span></p>
        <p>Jumlah: <span class="font-medium"><?= formatRupiah($data['jumlah']) ?></span></p>
        <p>Dibuat Oleh: <span class="font-medium"><?= $data['dibuat_oleh'] ?> ( <?= $data['user_id'] ?> )</span></p>
        <p>Ket. Tambahan: <span class="font-medium"><?= $data['keterangan_tambahan'] ? $data['keterangan_tambahan'] : '-' ?></span></p>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>