<?php
include 'config.php';

function isLoggedIn()
{
  return isset($_SESSION['user_id']);
}

function checkLogin()
{
  if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
  }
}

function getUserRole()
{
  return $_SESSION['role'] ?? null;
}

function checkRole($allowedRoles)
{
  if (!in_array(getUserRole(), $allowedRoles)) {
    header("Location: ../pages/dashboard.php");
    exit();
  }
}
