<?php
session_start();
$_SESSION['tictactoe-game-state'] = $_POST;
session_write_close();
?>