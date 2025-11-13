<?php
include 'auth.php';
checkLogin();

$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
$path = end($uri_parts);

$role = getUserRole();
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Informasi Keuangan Karang Taruna</title>
  <link href="../styles.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col">
  <div class="ml-64 border-b border-gray-300">
    <h1 class="text-2xl font-bold px-4 py-4">Selamat datang, <?= $role ?></h1>
  </div>
  <div class="flex flex-row">
    <header class="fixed left-0 top-0 min-h-screen h-full w-64 bg-white border-r border-gray-300">
      <div class="flex flex-col h-full justify-between ">
        <div>
          <h1 class="text-2xl font-bold px-4 pt-4">SIKAT</h1>
          <nav>
            <ul class="flex flex-col space-y-4 p-4">
              <?php if ($role == 'admin' || $role == 'bendahara' || $role == 'ketua'): ?>
                <li><a href="dashboard.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'dashboard.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Dashboard</a></li>
                <li><a href="pemasukan.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'pemasukan.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Pemasukan</a></li>
                <li><a href="pengeluaran.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'pengeluaran.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Pengeluaran</a></li>
                <li><a href="laporan.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'laporan.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Laporan</a></li>
                <li><a href="riwayat.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'riwayat.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Riwayat</a></li>
                <?php if ($role == 'admin'): ?>
                  <li><a href="user.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'user.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Akun User</a></li>
                  <li><a href="auditlog.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'auditlog.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Audit Log</a></li>
                <?php endif; ?>
              <?php else: ?>
                <li><a href="dashboard.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'dashboard.p hp') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Dashboard</a></li>
                <li><a href="laporan.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'laporan.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Laporan</a></li>
                <li><a href="riwayat.php" class="block py-3 px-2 border rounded-md text-sm hover:bg-gray-200 <?php if (strpos($path, 'riwayat.php') !== false) echo 'bg-gray-800 text-white hover:bg-gray-900'; ?> transition-all ease-in-out">Riwayat</a></li>
              <?php endif; ?>
            </ul>
          </nav>
        </div>
        <div class="flex flex-col items-start px-4 pb-4 space-y-2">
          <p class="text-sm  inline-flex flex-col">Masuk sebagai : <span><?= $_SESSION['name'] ?> (<?= ucfirst($_SESSION['role']) ?>)</span></p>
          <a href="../auth/logout.php" class="border px-3 py-2 rounded-md text-sm w-full text-center bg-gray-800 hover:bg-gray-700 text-white">Logout</a>
        </div>
      </div>
  </div>
  </header>
  <div class="fixed left-72 top-20 w-full max-w-[calc(100%-18rem)] pr-4">