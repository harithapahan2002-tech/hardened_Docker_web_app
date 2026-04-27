<?php
require_once 'auth.php';

startSecureSession();
logoutUser();

header('Location: /login.php?logout=1');
exit();
?>