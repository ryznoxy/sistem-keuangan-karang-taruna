<?php
include '../includes/sidebar.php';
include '../includes/functions.php';

checkRole(['admin']);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_akun'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);

  $md5_password = md5($password);

  $query = "INSERT INTO users (email,name, password, role) VALUES ('$email', '$name', '$md5_password', '$role')";
  if (mysqli_query($conn, $query)) {
    $success = "Berhasil menambahkan akun";

    mysqli_query($conn, "INSERT INTO auditlog (user_id, aksi, deskripsi) VALUES ('$_SESSION[user_id]', 'tambah_akun', 'Menambahkan akun baru dengan email $email')");
  } else {
    $error = "Gagal menambahkan akun: " .  mysqli_error($conn);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_akun'])) {
  $id = mysqli_real_escape_string($conn, $_POST['user_id']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);

  if (!empty($password)) {
    $password = md5($password);
    $query = "UPDATE users SET email='$email', name='$name', password='$password', role='$role' WHERE user_id='$id'";
  } else {
    $query = "UPDATE users SET email='$email', name='$name', role='$role' WHERE user_id='$id'";
  }

  if (mysqli_query($conn, $query)) {
    $success = "Akun berhasil diperbarui";

    mysqli_query($conn, "INSERT INTO auditlog (user_id, aksi, deskripsi) VALUES ('$_SESSION[user_id]', 'ubah_akun', 'Memperbarui akun dengan ID $id')");
  } else {
    $error = "Gagal memperbarui akun: " . mysqli_error($conn);
  }
}

if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $query = "DELETE FROM users WHERE user_id = $id";

  if (mysqli_query($conn, $query)) {
    $success = "Akun berhasil dihapus";

    mysqli_query($conn, "INSERT INTO auditlog (user_id, aksi, deskripsi) VALUES ('$_SESSION[user_id]', 'hapus_akun', 'Menghapus akun dengan ID $id')");
  } else {
    $error = "Gagal menghapus akun: " . mysqli_error($conn);
  }
}

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
?>
<div class="h-[100dvh] w-[100dvw] fixed top-0 left-0 right-0 z-10 bg-gray-700 bg-opacity-50 <?= isset($_GET['edit'])  ? '' : 'hidden' ?> <?= isset($success) || isset($error) ? 'hidden' : '' ?>">
  <div class="flex justify-center items-center my-auto h-full">
    <div class="w-[440px] bg-white p-5 rounded-xl border">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Edit Akun</h2>
        <a href="user.php" class="text-xl font-bold">✕</a>
      </div>
      <div>
        <form method="post">
          <?php
          if (isset($_GET['edit'])) {
            $id = intval($_GET['edit']);
            $query = "SELECT * FROM users WHERE user_id = $id";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) > 0) {
              $user = mysqli_fetch_assoc($result);
            } else {
              $user = null;
            }
          }
          ?>

          <input type="hidden" name="user_id" id="user_id" required value="<?= is_array($user) ? $user['user_id'] : '' ?>">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Nama" value="<?= is_array($user) ? $user['name'] : '' ?>">
          </div>
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Email" value="<?= is_array($user) ? $user['email'] : '' ?>">
          </div>
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <input type="text" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Masukan password baru">
          </div>
          <div class="mb-6">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="" disabled>Pilih Role</option>
              <option value="admin" <?= is_array($user) && $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
              <option value="ketua" <?= is_array($user) && $user['role'] == 'ketua' ? 'selected' : '' ?>>Ketua</option>
              <option value="bendahara" <?= is_array($user) && $user['role'] == 'bendahara' ? 'selected' : '' ?>>Bendahara</option>
              <option value="anggota" <?= is_array($user) && $user['role'] == 'anggota' ? 'selected' : '' ?>>Anggota</option>
            </select>
          </div>

          <button type="submit" name="update_akun" class="w-full bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
            Simpan Perubahan
          </button>
        </form>
      </div>
    </div>
  </div>
</div>


<div class="flex justify-between items-center mb-6">
  <h2 class="text-xl font-semibold">Akun User</h2>
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
  <h3 class="text-lg font-semibold mb-4">Tambah Akun</h3>

  <form method="POST">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" id="name" name="name" required
          class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Nama lengkap">
      </div>

      <div>
        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded">
          <option value="">Pilih role</option>
          <option value="admin">Admin</option>
          <option value="ketua">Ketua</option>
          <option value="bendahara">Bendahara</option>
          <option value="anggota">Anggota</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" required
          class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Email">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password"
          class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Password">
      </div>
    </div>

    <button type="submit" name="tambah_akun"
      class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded ">
      Simpan Akun
    </button>
  </form>
</div>

<div class="bg-white p-6 rounded-lg shadow border mb-4">
  <h3 class="text-lg font-semibold mb-4">Daftar Akun</h3>

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
          <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = "SELECT * FROM users 
                          ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)):
        ?>
          <tr>
            <td class="py-2 px-4 border-b border-gray-200"><?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= ucfirst($row['name']) ?></td>
            <td class="py-2 px-4 border-b border-gray-200"><?= $row['email'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 font-semibold "><?= $row['role'] ?></td>
            <td class="py-2 px-4 border-b border-gray-200 space-x-2">
              <a href="?edit=<?= $row['user_id'] ?>"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
              <a href="?hapus=<?= $row['user_id'] ?>"
                onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')"
                class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($result) === 0): ?>
          <tr>
            <td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada data Akun</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>