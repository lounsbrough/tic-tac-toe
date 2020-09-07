<?PHP
session_start();
$gameState = $_SESSION['tictactoe-game-state'] ?? null;

$gameState['game-in-progress'] = $gameState['game-in-progress'] ?? false;
$gameState['game-difficulty'] = $gameState['game-difficulty'] ?? 'Normal';
$gameState['player-symbol'] = $gameState['player-symbol'] ?? 'X';
$gameState['player-start'] = $gameState['player-start'] ?? true;
$gameState['game-grid-size'] = $gameState['game-grid-size'] ?? 3;
$gameState['win-result'] = $gameState['win-result'] ?? 0;
$gameState['winning-row'] = $gameState['winning-row'] ?? array();
$gameState['game-message'] = $gameState['game-message'] ?? '';
$gridCount = pow($gameState['game-grid-size'], 2);
$gameState['game-board']['grid-values'] = $gameState['game-board']['grid-values'] ?? array_fill(0, $gridCount, '');

$gridValues = $gameState['game-board']['grid-values'];

$winResult = $gameState['win-result'];
$winningRow = $gameState['winning-row'];
$gameMessage = $gameState['game-message'];

$gridDisabled = array_fill(0, $gridCount, 'disabled');
if ($winResult == 0)
{
    $gridDisabled = array_map(function($value) { return empty($value) ? '' : 'disabled'; }, $gridValues);
}

if ($winResult == 0)
{
    $gridClasses = array_map(function($value) {
        switch ($value) 
        {
            case 'X':
                return 'btn-primary';
            case 'O':
                return 'btn-warning';
            default:
                return 'btn-default';
        }
    }, $gridValues);
}
else
{
    $gridClasses = array_fill(0, $gridCount, '');
    foreach ($gridValues as $index => $gridValue)
    {
        $gridClasses[$index] = array_search($index, $winningRow) === false ? 'btn-default' : ($gridValue == 'X' ? 'btn-primary' : 'btn-warning');
    }
}

if (!empty($gameMessage)) 
{
    $gameMessageClass = 'game-message-alert-' . (empty($winningRow) ? 'default' : ($gridValues[$winningRow[0]] == 'X' ? 'primary' : 'warning'));
}
else
{
    $gameMessageClass = 'hide-message';
}

$gameInProgress = $gameState['game-in-progress'] == 'true';

session_write_close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<script>
    let currentGameState = <?= json_encode($gameState) ?>;
</script>

<script src="assets/bootstrap/jquery-3.3.1.min.js"></script>
<script src="assets/bootstrap/popper.min.js"></script>

<link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css">
<script src="assets/bootstrap/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/btn3d.css">

<link rel="stylesheet" href="css/tictactoe.css">
<script src="js/tictactoe.js"></script>

<?php
if (!$gameInProgress)
{
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12 mt-5 text-center control-row">

                <div class="row">
                    <div class="col-md-12">
                        <label class="game-settings-label">Difficulty</label>
                        <div class="btn-group">
                            <button id="difficulty-selected-button" type="button" class="btn btn-light difficulty-dropdown" disabled></button>
                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split difficulty-dropdown" disabled data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item difficulty-option" data-difficulty="Novice" href="#">Novice</a>
                                <a class="dropdown-item difficulty-option" data-difficulty="Normal" href="#">Normal</a>
                                <a class="dropdown-item difficulty-option" data-difficulty="Genius" href="#">Genius</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="game-settings-label">Play As</label>
                        <div class="btn-group">
                            <button id="symbol-selected-button" type="button" class="btn btn-light symbol-dropdown"></button>
                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split symbol-dropdown" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item symbol-option" data-symbol="X" href="#">X</a>
                                <a class="dropdown-item symbol-option" data-symbol="O" href="#">O</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="game-settings-label">Who Starts?</label>
                        <div class="btn-group">
                            <button id="player-start-selected-button" type="button" class="btn btn-light player-start-dropdown"></button>
                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split player-start-dropdown" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item player-start-option" data-player-start="true" href="#">Player</a>
                                <a class="dropdown-item player-start-option" data-player-start="false" href="#">Computer</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="game-settings-label">Grid Size</label>
                        <div class="btn-group">
                            <button id="grid-size-selected-button" type="button" class="btn btn-light grid-size-dropdown"></button>
                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split grid-size-dropdown" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item grid-size-option" data-grid-size="3" href="#">3</a>
                                <a class="dropdown-item grid-size-option" data-grid-size="4" href="#">4</a>
                                <a class="dropdown-item grid-size-option" data-grid-size="5" href="#">5</a>
                                <a class="dropdown-item grid-size-option" data-grid-size="6" href="#">6</a>
                                <a class="dropdown-item grid-size-option" data-grid-size="7" href="#">7</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button id="start-game-button" class="btn btn-primary">Start Game!</button>
                    </div>
                </div>
        </div>
    </div>
</div>

<?php
}
else
{
?>

<div class="container-fluid">
    <div class="row">

        <div id="game-message-alert" class="mx-auto mb-3 w-100 text-center game-message-alert <?= $gameMessageClass ?>" role="alert">
            <?= $gameMessage ?>
        </div>

        <div class="col-md-12 mb-4 text-center">

            <table id="game-board" class="mx-auto grid-size-<?php echo $gameState['game-grid-size']; ?>">

                <?php
                    for($row=0; $row<$gameState['game-grid-size']; $row++) {
                ?>
                    <tr>

                        <?php
                            for($column=0; $column<$gameState['game-grid-size']; $column++) {
                                $cellIndex = $row * $gameState['game-grid-size'] + $column;
                        ?>

                            <td><button type="button" class="btn <?= $gridClasses[$cellIndex] ?> btn-sq-lg btn3d "<?= $gridDisabled[$cellIndex] ?>><?= $gridValues[$cellIndex] ?></button></td>

                        <?php
                            }
                        ?>

                    </tr>
                <?php
                    }
                ?>

            </table>
            
        </div>

        <div class="col-md-12 mb-1 text-center">
            <button id="start-over-button" class="btn btn-danger">Start Over</button>
        </div>

    </div>
</div>

<?php
}
?>

</body>
</html>
