<?php

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    header("Location: /kalcer_login/login/index.php");
    exit;
}
