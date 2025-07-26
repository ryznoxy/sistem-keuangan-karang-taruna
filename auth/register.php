<?php
include '../includes/config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);

  $md5_password = md5($password);


  $query = "INSERT INTO users (email,name, password, role) VALUES ('$email', '$name', '$md5_password', '$role')";
  if (mysqli_query($conn, $query)) {
    header("Location: login.php?success=Registrasi berhasil");
    exit();
  } else {
    header("Location: register.php?error= " . mysqli_error($conn));
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="../styles.css" rel="stylesheet">
</head>

<body class="bg-white">
  <div class="min-h-screen flex flex-col items-center justify-center">

    <?php if (isset($_GET['error'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php if (strpos($_GET['error'], 'Duplicate entry') !== false): ?>
          Email sudah terdaftar. Silakan gunakan email lain.
        <?php else: ?>
          <?= htmlspecialchars($_GET['error']) ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="bg-white border p-8 rounded-xl shadow-md w-full max-w-md">
      <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Register SIKAT</h1>
      <form method="POST">
        <div class="mb-4">
          <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
          <input type="name" id="name" name="name" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600">
        </div>
        <div class="mb-4">
          <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
          <input type="email" id="email" name="email" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600">
        </div>

        <div class="mb-4">
          <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
          <input type="password" id="password" name="password" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600">
        </div>

        <div class="mb-6">
          <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
          <input type="text" id="role" name="role" required readonly
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600 capitalize text-sm" value="anggota">
        </div>

        <button type="submit"
          class="w-full bg-gray-800 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline">
          Register
        </button>
      </form>
      <h2 class="text-center text-gray-600 text-sm mt-4">Sudah memiliki akun? <a href="login.php" class="text-blue-600 hover:underline">Login sekarang!</a></h2>
    </div>  
  </div>
</body>