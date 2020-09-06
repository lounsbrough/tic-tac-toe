<?PHP
session_start();
$currentGameState = $_SESSION['tictactoe-game-state'] ?? null;

$gameDifficulty = $currentGameState['game-difficulty'];
$playerSymbol = $currentGameState['player-symbol'];
$grid = $currentGameState['game-board']['grid-values'];

require_once dirname(__FILE__).'/../classes/game-logic-4x4.php';
$gameLogic = new GameLogic();

switch ($gameDifficulty)
{
    case 'Novice':
        $gameLogic->noviceMove($grid, $playerSymbol);
        break;
    case 'Normal':
        $gameLogic->normalMove($grid, $playerSymbol);
        break;
    case 'Genius':
        $gameLogic->geniusMove($grid, $playerSymbol);
        break;
}

$_SESSION['tictactoe-game-state']['game-board']['grid-values'] = $grid;

echo json_encode($_SESSION['tictactoe-game-state']['game-board']['grid-values']);

session_write_close();
?>
