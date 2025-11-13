<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

checkRole(['ketua', 'admin', 'bendahara']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pemasukan'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $sumber = mysqli_real_escape_string($conn, $_POST['sumber']);
    $jumlah = str_replace(['.', ','], ['', '.'], $_POST['jumlah']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO pemasukan (user_id, tanggal_pemasukan, sumber_pemasukan, jumlah, keterangan) 
              VALUES ('$user_id', '$tanggal', '$sumber', '$jumlah', '$keterangan')";

    if (mysqli_query($conn, $query)) {
        $success = "Pemasukan berhasil ditambahkan";

        mysqli_query($conn, "INSERT INTO auditlog (user_id, aksi, deskripsi) VALUES ('$user_id', 'tambah_pemasukan', 'Menambahkan pemasukan sebesar Rp " . number_format($jumlah, 2, ',', '.') . " dari sumber $sumber pada tanggal $tanggal')");
    } else {
        $error = "Gagal menambahkan pemasukan: " . mysqli_error($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pemasukan'])) {
    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $sumber = mysqli_real_escape_string($conn, $_POST["sumber"]);
    $tanggal = mysqli_real_escape_string($conn, $_POST["tanggal"]);
    $jumlah = mysqli_real_escape_string($conn, $_POST["jumlah"]);
    $keterangan = mysqli_real_escape_string($conn, $_POST["keterangan"] ?? "");

    $query = "UPDATE pemasukan SET sumber_pemasukan='$sumber', tanggal_pemasukan='$tanggal', jumlah='$jumlah', keterangan='$keterangan' WHERE id_pemasukan='$id'";

    if (mysqli_query($conn, $query)) {
        $success = "Pemasukan berhasil diubah";

        mysqli_query($conn, "INSERT INTO auditlog (user_id, aksi, deskripsi) VALUES ('$user_id', 'ubah_pemasukan', 'Mengubah pemasukan ID $id menjadi sumber: $sumber, tanggal: $tanggal, jumlah: Rp " . number_format($jumlah, 2, ',', '.') . ", keterangan: $keterangan')");
    } else {
        $error = "Gagal mengubah pemasukan: " . mysqli_error($conn);
    }
}


if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM pemasukan WHERE id_pemasukan = $id";

    if (mysqli_query($conn, $query)) {
        $success = "Pemasukan berhasil dihapus";

        mysqli_query($conn, "INSERT INTO auditlog (user_id, aksi, deskripsi) VALUES ('$user_id', 'hapus_pemasukan', 'Menghapus pemasukan dengan ID $id')");
    } else {
        $error = "Gagal menghapus pemasukan: " . mysqli_error($conn);
    }
}

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
?>

<div class="h-[100dvh] w-[100dvw] fixed top-0 left-0 right-0 z-10 bg-gray-700 bg-opacity-50 <?= isset($_GET['edit'])  ? '' : 'hidden' ?> <?= isset($success) || isset($error) ? 'hidden' : '' ?>">
    <div class="flex justify-center items-center my-auto h-full">
        <div class="w-[440px] bg-white p-5 rounded-xl border">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Edit Pemasukan</h2>
                <a href="pemasukan.php" class="text-xl font-bold">✕</a>
            </div>
            <div>
                <form method="post">
                    <?php
                    if (isset($_GET['edit'])) {
                        $id = intval($_GET['edit']);
                        $query = "SELECT * FROM pemasukan WHERE id_pemasukan = $id";
                        $result = mysqli_query($conn, $query);
                        $pemasukan = mysqli_fetch_assoc($result);
                    }
                    ?>

                    <input type="hidden" name="id" id="id" required value="<?= isset($pemasukan) ? $pemasukan['id_pemasukan'] : '' ?>">
                    <div class="mb-4">
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" required class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?= isset($pemasukan) ? date('Y-m-d', strtotime($pemasukan['tanggal_pemasukan'])) : date('Y-m-d') ?>">
                    </div>
                    <div class="mb-4">
                        <label for="sumber" class="block text-sm font-medium text-gray-700 mb-1">Sumber Pemasukan</label>
                        <select id="sumber" name="sumber" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Pilih Sumber</option>
                            <option value="iuran" <?= isset($pemasukan) && $pemasukan['sumber_pemasukan'] == 'iuran' ? 'selected' : '' ?>>Iuran</option>
                            <option value="donatur" <?= isset($pemasukan) && $pemasukan['sumber_pemasukan'] == 'donatur' ? 'selected' : '' ?>>Donatur</option>
                            <option value="sponsor" <?= isset($pemasukan) && $pemasukan['sumber_pemasukan'] == 'sponsor' ? 'selected' : '' ?>>Sponsor</option>
                            <option value="hasil kegiatan" <?= isset($pemasukan) && $pemasukan['sumber_pemasukan'] == 'hasil kegiatan' ? 'selected' : '' ?>>Hasil Kegiatan</option>
                            <option value="lainnya" <?= isset($pemasukan) && $pemasukan['sumber_pemasukan'] == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                        <input type="text" id="jumlah" name="jumlah" required class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="0,00" value="<?= isset($pemasukan) ? $pemasukan['jumlah'] : '' ?>">
                    </div>
                    <div class="mb-6">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <input type="text" id="keterangan" name="keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Keterangan tambahan" value="<?= isset($pemasukan) ? $pemasukan['keterangan'] : '' ?>">
                    </div>

                    <button type="submit" name="update_pemasukan" class="w-full bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-semibold">Pemasukan</h2>
    <div>
        <form method="get" class="flex items-center space-x-2">
            <input type="month" name="bulan" value="<?= $bulan ?>" class="px-3 py-1 border border-gray-300 rounded">
            <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded">Filter</button>
            <a href="pemasukan.php" class="bg-gray-200 px-3 py-1 rounded">Reset</a>
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
    <h3 class="text-lg font-semibold mb-4">Tambah Pemasukan</h3>

    <form method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" required
                    value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 border border-gray-300 rounded">
            </div>

            <div>
                <label for="sumber" class="block text-sm font-medium text-gray-700 mb-1">Sumber Pemasukan</label>
                <select id="sumber" name="sumber" required class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">Pilih Sumber</option>
                    <option value="iuran">Iuran</option>
                    <option value="donatur">Donatur</option>
                    <option value="sponsor">Sponsor</option>
                    <option value="hasil kegiatan">Hasil Kegiatan</option>
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

        <button type="submit" name="tambah_pemasukan"
            class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded ">
            Simpan Pemasukan
        </button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-4">
    <h3 class="text-lg font-semibold mb-4">Daftar Pemasukan</h3>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sumber</th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah</th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM pemasukan 
                          WHERE DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = '$bulan'
                          ORDER BY tanggal_pemasukan DESC";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)):
                ?>
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y', strtotime($row['tanggal_pemasukan'])) ?></td>
                        <td class="py-2 px-4 border-b border-gray-200"><?= ucfirst($row['sumber_pemasukan']) ?></td>
                        <td class="py-2 px-4 border-b border-gray-200"><?= $row['keterangan'] ?></td>
                        <td class="py-2 px-4 border-b border-gray-200 font-semibold text-green-600"><?= formatRupiah($row['jumlah']) ?></td>
                        <td class="py-2 px-4 border-b border-gray-200 space-x-2">
                            <a href="?edit=<?= $row['id_pemasukan'] ?>"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                            <a href="?hapus=<?= $row['id_pemasukan'] ?>"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus pemasukan ini?')"
                                class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada data pemasukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>