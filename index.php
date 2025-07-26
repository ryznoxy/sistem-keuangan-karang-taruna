<?php
require_once 'includes/auth.php';
if (isLoggedIn()) header('Location: pages/dashboard.php');
else header('Location: auth/login.php');
