<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'semua';

// Query dasar
$query = "SELECT 
            a.id_audit, 
            a.user_id, 
            u.name AS nama_user, 
            a.aksi, 
            a.deskripsi, 
            a.waktu_aksi
          FROM auditlog a
          JOIN users u ON a.user_id = u.user_id
          WHERE DATE_FORMAT(a.waktu_aksi, '%Y-%m') = '$bulan'";

// Filter berdasarkan aksi
if ($aksi !== 'semua') {
  $query .= " AND a.aksi = '$aksi'";
}

$query .= " ORDER BY a.waktu_aksi DESC";

$result = mysqli_query($conn, $query);
?>

<div class="flex justify-between items-center mb-6">
  <h2 class="text-xl font-semibold">Audit Log Aktivitas</h2>
  <div>
    <form method="get" class="flex items-center justify-center space-x-2">
      <input type="month" name="bulan" value="<?= $bulan ?>" class="px-3 py-1 border border-gray-300 rounded">
      <select name="aksi" class="px-3 py-1 border border-gray-300 rounded">
        <option value="semua" <?= $aksi == 'semua' ? 'selected' : '' ?>>Semua Aksi</option>
        <option value="tambah_pemasukan" <?= $aksi == 'tambah_pemasukan' ? 'selected' : '' ?>>Tambah Pemasukan</option>
        <option value="hapus_pemasukan" <?= $aksi == 'hapus_pemasukan' ? 'selected' : '' ?>>Hapus Pemasukan</option>
        <option value="tambah_pengeluaran" <?= $aksi == 'tambah_pengeluaran' ? 'selected' : '' ?>>Tambah Pengeluaran</option>
        <option value="hapus_pengeluaran" <?= $aksi == 'hapus_pengeluaran' ? 'selected' : '' ?>>Hapus Pengeluaran</option>
        <option value="login" <?= $aksi == 'login' ? 'selected' : '' ?>>Login</option>
        <option value="logout" <?= $aksi == 'logout' ? 'selected' : '' ?>>Logout</option>
      </select>
      <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded">Filter</button>
      <a href="auditlog.php" class="bg-gray-200 px-3 py-1 rounded">Reset</a>
    </form>
  </div>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-4">
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Tanggal & Waktu</th>
          <th class="py-2 px-4 border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Nama Pengguna</th>
          <th class="py-2 px-4 border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Aksi</th>
          <th class="py-2 px-4 border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Deskripsi</th>
          <th class="py-2 px-4 border-b bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider text-left">Detail</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y H:i', strtotime($row['waktu_aksi'])) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= htmlspecialchars($row['nama_user']) ?></td>
            <td class="py-2 px-4 border-b border-gray-200 capitalize"><?= str_replace('_', ' ', $row['aksi']) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= $row['deskripsi'] ? htmlspecialchars($row['deskripsi']) : '-' ?></td>
            <td class="py-2 px-4 border-b border-gray-200">
              <a href="auditlog.php?bulan=<?= $bulan ?>&aksi=<?= $aksi ?>&lihat=1&id=<?= $row['id_audit'] ?>" class="px-2 py-1 border rounded">
                👁️
              </a>
            </td>
          </tr>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result) === 0): ?>
          <tr>
            <td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada aktivitas yang tercatat</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
if (isset($_GET['lihat'])):
  $id = $_GET['id'] ?? '';
  $detailQuery = "SELECT a.*, u.name FROM auditlog a JOIN users u ON a.user_id = u.user_id WHERE a.id_audit = '$id'";
  $detailResult = mysqli_query($conn, $detailQuery);
  $data = mysqli_fetch_assoc($detailResult);
?>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
      <div class="flex justify-between items-center mb-4 border-b border-gray-300 pb-4">
        <h2 class="text-lg font-semibold">Detail Aktivitas</h2>
        <a href="auditlog.php?bulan=<?= $bulan ?>&aksi=<?= $aksi ?>" class="text-xl font-bold">✕</a>
      </div>
      <div class="space-y-2">
        <p>ID Log: <span class="font-medium"><?= $data['id_audit'] ?></span></p>
        <p>Tanggal & Waktu: <span class="font-medium"><?= date('d/m/Y H:i', strtotime($data['waktu_aksi'])) ?></span></p>
        <p>Nama Pengguna: <span class="font-medium"><?= $data['name'] ?> (<?= $data['user_id'] ?>)</span></p>
        <p>Aksi: <span class="font-medium capitalize"><?= str_replace('_', ' ', $data['aksi']) ?></span></p>
        <p>Deskripsi: <span class="font-medium"><?= $data['deskripsi'] ? $data['deskripsi'] : '-' ?></span></p>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>