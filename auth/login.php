<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];

  $query = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (md5($password) === $user['password']) {
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['name'] = $user['name'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['email'] = $user['email'];

      header("Location: ../pages/dashboard.php");
      exit();
    }
  }

  $error = "Email atau password salah";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - SIKAT</title>
  <link href="../styles.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white">
  <div class="min-h-screen flex items-center justify-center">
    <div class="bg-white border p-8 rounded-xl shadow-md w-full max-w-md">
      <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Login SIKAT</h1>

      <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-4">
          <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
          <input type="email" id="email" name="email" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600">
        </div>

        <div class="mb-6">
          <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
          <input type="password" id="password" name="password" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600">
        </div>

        <button type="submit"
          class="w-full bg-gray-800 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline">
          Login
        </button>
      </form>

      <p class="text-sm text-gray-600 mt-4 text-center">Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar sekarang!</a></p>

    </div>
  </div>
</body>

</html>