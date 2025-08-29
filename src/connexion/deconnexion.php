<?php
$_SESSION = [];
session_destroy();
header('Location: ../connexion.php');
exit;
?>
