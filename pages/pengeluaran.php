<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

checkRole(['ketua', 'admin', 'bendahara']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pengeluaran'])) {
  $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
  $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
  $jumlah = str_replace(['.', ','], ['', '.'], $_POST['jumlah']);
  $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
  $user_id = $_SESSION['user_id'];

  $query = "INSERT INTO pengeluaran (user_id, tanggal_pengeluaran, jenis_pengeluaran, jumlah, keterangan) 
              VALUES ('$user_id', '$tanggal', '$jenis', '$jumlah', '$keterangan')";

  if (mysqli_query($conn, $query)) {
    $success = "Pengeluaran berhasil ditambahkan";
  } else {
    $error = "Gagal menambahkan pengeluaran: " . mysqli_error($conn);
  }
}

//buat edit

if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $query = "DELETE FROM pengeluaran WHERE id_pengeluaran = $id";

  if (mysqli_query($conn, $query)) {
    $success = "Pengeluaran berhasil dihapus";
  } else {
    $error = "Gagal menghapus pengeluaran: " . mysqli_error($conn);
  }
}

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
?>


//buat modal


<div class="flex justify-between items-center mb-6">
  <h2 class="text-xl font-semibold">Pengeluaran</h2>
  <div>
    <form method="get" class="flex items-center space-x-2">
      <input type="month" name="bulan" value="<?= $bulan ?>" class="px-3 py-1 border border-gray-300 rounded">
      <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded">Filter</button>
      <a href="pengeluaran.php" class="bg-gray-200 px-3 py-1 rounded">Reset</a>
    </form>
  </div>
</div>

<?php if (isset($success)): ?>
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <?= $success ?>
  </div>
<?php endif; ?>

<?php if (isset($error)): ?>
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <?= $error ?>
  </div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow border mb-6">
  <h3 class="text-lg font-semibold mb-4">Tambah Pengeluaran</h3>

  <form method="POST">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" required
          value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 border border-gray-300 rounded">
      </div>

      <div>
        <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengeluaran</label>
        <select id="jenis" name="jenis" required class="w-full px-3 py-2 border border-gray-300 rounded">
          <option value="">Pilih Jenis</option>
          <option value="operasional">Operasional</option>
          <option value="konsumsi">Konsumsi</option>
          <option value="kegiatan">Kegiatan</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
        <input type="text" id="jumlah" name="jumlah" required
          class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="0,00">
      </div>

      <div>
        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
        <input type="text" id="keterangan" name="keterangan"
          class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Keterangan tambahan">
      </div>
    </div>

    <button type="submit" name="tambah_pengeluaran"
      class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded ">
      Simpan Pengeluaran
    </button>
  </form>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-4">
  <h3 class="text-lg font-semibold mb-4">Daftar Pengeluaran</h3>

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = "SELECT * FROM pengeluaran 
                          WHERE DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = '$bulan'
                          ORDER BY tanggal_pengeluaran DESC";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)):
        ?>
          <tr>
            <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y', strtotime($row['tanggal_pengeluaran'])) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= ucfirst($row['jenis_pengeluaran']) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= $row['keterangan'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 font-semibold text-red-600"><?= formatRupiah($row['jumlah']) ?></td>
            <td class="py-2 px-4 border-b border-gray-200 space-x-2">
              <a href="?edit=<?= $row['id_pengeluaran'] ?>"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
              <a href="?hapus=<?= $row['id_pengeluaran'] ?>"
                onclick="return confirm('Apakah Anda yakin ingin menghapus pemasukan ini?')"
                class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result) === 0): ?>
          <tr>
            <td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada data pengeluaran</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>