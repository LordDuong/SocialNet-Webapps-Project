<?php
session_start();

// Destroy session
session_destroy();

// Redirect to signin page
header('Location: ./signin.html');
exit;
?>
