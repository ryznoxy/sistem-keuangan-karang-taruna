<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sikat';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');
