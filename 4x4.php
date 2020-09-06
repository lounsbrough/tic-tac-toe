<?PHP
session_start();
$gameState = $_SESSION['tictactoe-game-state'] ?? null;

$gameState['game-board']['grid-values'] = $gameState['game-board']['grid-values'] ?? array_fill(0, 16, '');
$gameState['game-in-progress'] = $gameState['game-in-progress'] ?? false;
$gameState['game-difficulty'] = $gameState['game-difficulty'] ?? 'Normal';
$gameState['player-symbol'] = $gameState['player-symbol'] ?? 'X';
$gameState['player-start'] = $gameState['player-start'] ?? true;
$gameState['win-result'] = $gameState['win-result'] ?? 0;
$gameState['winning-row'] = $gameState['winning-row'] ?? array();
$gameState['game-message'] = $gameState['game-message'] ?? '';
$gridValues = $gameState['game-board']['grid-values'];

$winResult = $gameState['win-result'];
$winningRow = $gameState['winning-row'];
$gameMessage = $gameState['game-message'];

$gridDisabled = array_fill(0, 16, 'disabled');
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
    $gridClasses = array_fill(0, 16, '');
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
<script src="js/tictactoe-4x4.js"></script>

<nav class="navbar navbar-dark bg-dark">
    <span class="navbar-brand mb-0 h1 mx-auto text-center">TicTacToe</span>
</nav>

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

        <div class="col-md-12 mb-5 text-center">

            <table id="game-board" class="mx-auto">
                <tr>
                    <td><button type="button" class="btn <?= $gridClasses[0] ?> btn-sq-lg btn3d "<?= $gridDisabled[0] ?>><?= $gridValues[0] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[1] ?> btn-sq-lg btn3d "<?= $gridDisabled[1] ?>><?= $gridValues[1] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[2] ?> btn-sq-lg btn3d "<?= $gridDisabled[2] ?>><?= $gridValues[2] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[3] ?> btn-sq-lg btn3d "<?= $gridDisabled[3] ?>><?= $gridValues[3] ?></button></td>
                </tr>
                <tr>
                    <td><button type="button" class="btn <?= $gridClasses[4] ?> btn-sq-lg btn3d "<?= $gridDisabled[4] ?>><?= $gridValues[4] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[5] ?> btn-sq-lg btn3d "<?= $gridDisabled[5] ?>><?= $gridValues[5] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[6] ?> btn-sq-lg btn3d "<?= $gridDisabled[6] ?>><?= $gridValues[6] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[7] ?> btn-sq-lg btn3d "<?= $gridDisabled[7] ?>><?= $gridValues[7] ?></button></td>
                </tr>
                <tr>
                    <td><button type="button" class="btn <?= $gridClasses[8] ?> btn-sq-lg btn3d "<?= $gridDisabled[8] ?>><?= $gridValues[8] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[9] ?> btn-sq-lg btn3d "<?= $gridDisabled[9] ?>><?= $gridValues[9] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[10] ?> btn-sq-lg btn3d "<?= $gridDisabled[10] ?>><?= $gridValues[10] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[11] ?> btn-sq-lg btn3d "<?= $gridDisabled[11] ?>><?= $gridValues[11] ?></button></td>
                </tr>
                <tr>
                    <td><button type="button" class="btn <?= $gridClasses[12] ?> btn-sq-lg btn3d "<?= $gridDisabled[12] ?>><?= $gridValues[12] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[13] ?> btn-sq-lg btn3d "<?= $gridDisabled[13] ?>><?= $gridValues[13] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[14] ?> btn-sq-lg btn3d "<?= $gridDisabled[14] ?>><?= $gridValues[14] ?></button></td>
                    <td><button type="button" class="btn <?= $gridClasses[15] ?> btn-sq-lg btn3d "<?= $gridDisabled[15] ?>><?= $gridValues[15] ?></button></td>
                </tr>
            </table>

        </div>

        <div class="col-md-12 mb-5 text-center">
            <button id="start-over-button" class="btn btn-danger">Start Over</button>
        </div>

    </div>
</div>

<?php
}
?>

</body>
</html>
