<?PHP
session_start();
$currentGameState = $_SESSION['tictactoe-game-state'] ?? null;

$gameDifficulty = $currentGameState['game-difficulty'];
$playerSymbol = $currentGameState['player-symbol'];
$grid = $currentGameState['game-board']['grid-values'];

require_once dirname(__FILE__).'/../classes/game-logic.php';
$gameLogic = new GameLogic();

$gameOver = $gameLogic->checkGameOver($grid, $playerSymbol);

echo json_encode($gameOver);

session_write_close();
?>