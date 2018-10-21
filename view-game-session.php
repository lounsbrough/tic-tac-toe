<?PHP
session_start();
echo '<pre>';
print_r($_SESSION['tictactoe-game-state']);
echo '</pre>';
?>