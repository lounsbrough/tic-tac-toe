<?PHP

session_start();
$gameState = $_SESSION['tictactoe-game-state'] ?? null;

$gameState['game-board']['grid-values'] = $gameState['game-board']['grid-values'] ?? array_fill(0, 9, '');
$gameState['game-in-progress'] = $gameState['game-in-progress'] ?? false;
$gameState['game-difficulty'] = $gameState['game-difficulty'] ?? 'Normal';
$gameState['player-symbol'] = $gameState['player-symbol'] ?? 'X';
$gameState['player-start'] = $gameState['player-start'] ?? 'true';

$gridValues = $gameState['game-board']['grid-values'];
$gridDisabled = array_map(function($value) { return empty($value) ? '' : 'disabled'; }, $gridValues);
$gameInProgress = $gameState['game-in-progress'] == 'true';

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
<script src="js/tictactoe.js"></script>

<nav class="navbar navbar-dark bg-dark mb-5">
    <span class="navbar-brand mb-0 h1 mx-auto text-center">TicTacToe</span>
</nav>

<?php

if (!$gameInProgress)
{
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-5 text-sm-center control-row">
            <div class="row">

                <div class="col-sm-12 col-lg-12 text-sm-center">
                    <label class="game-settings-label">Difficulty:</label>
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

                <div class="col-sm-12">
                    <label class="game-settings-label">Play As:</label>
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

                <div class="col-sm-12">                    
                    <label class="game-settings-label">Who Starts?:</label>
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

                <div class="col-sm-12">
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

        <div class="col-md-12 mb-5 text-sm-center">

            <table id="game-board" class="mx-auto text-center">
                <tr>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[0] ?>><?= $gridValues[0] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[1] ?>><?= $gridValues[1] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[2] ?>><?= $gridValues[2] ?></button></td>
                </tr>
                <tr>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[3] ?>><?= $gridValues[3] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[4] ?>><?= $gridValues[4] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[5] ?>><?= $gridValues[5] ?></button></td>
                </tr>
                <tr>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[6] ?>><?= $gridValues[6] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[7] ?>><?= $gridValues[7] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d "<?= $gridDisabled[8] ?>><?= $gridValues[8] ?></button></td>
                </tr>
            </table>
            
        </div>

        <div class="col-md-12 mb-5 text-sm-center">
            <div class="row">

                <div class="col-sm-5 col-lg-12 text-sm-center">
                    <button id="start-over-button" class="btn btn-danger">Start Over</button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
}
?>

</body>
</html>
