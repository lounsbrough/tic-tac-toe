<!DOCTYPE html>
<html>
<body>

<script src="assets/bootstrap/jquery-3.3.1.slim.min.js"></script>
<script src="assets/bootstrap/popper.min.js"></script>

<link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css">
<script src="assets/bootstrap/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/btn3d.css">

<link rel="stylesheet" href="css/tictactoe.css">
<script src="js/tictactoe.js"></script>

<!-- PHP Code -->

<?PHP

$new = array_fill(0, 9, 'X');
$gridValues = array_fill(0, 9, 'X');

require_once dirname(__FILE__).'/classes/game-logic.php';
$gameLogic = new GameLogic();

if (!isset($_GET['go']))
{
    $disabled = '';
    $difficulty = $_GET['difficulty'] ?? 2;
    $symbol = $_GET['symbolhidden'] ?? 'checked' == 'checked' ? 'X' : 'O';
    $start = $_GET['start'] ?? 'checked';
    $turn = 1;
    $old = array_fill(0, 9, '');
    $new = array_fill(0, 9, '');
} else {
    $disabled = 'disabled';
    $difficulty = $_GET['difficulty'];
    $turn = $_GET['turn'];
    if (isset($_GET['symboldsp']))
    {
        $symbol = $_GET['symboldsp'];
        $start = isset($_GET['startdsp']) ? 'checked' : '';
    }
    else
    {
        $symbol = $_GET['symbolhidden'] == 'checked' ? 'X' : 'O';
        $start = $_GET['start'];
    }
}

$difficultyCheck = array_fill(0, 3, '');
$difficultyCheck[$difficulty] = 'checked';

$symbolcheck = array_fill(0, 2, '');
$symbol == 'X' ? $symbolcheck[0] = 'checked' : $symbolcheck[1] = 'checked';

$startcheck = $start == 'checked' ? 'checked' : '';

if (isset($_GET['go']))
{
    for ($i = 0; $i <= 8; $i++)
    {
        $old[$i] = $_GET['old' . ($i + 1)];
        $new[$i] = $_GET['new' . ($i + 1)];
        $new[$i] = empty(trim($new[$i])) ? '' : strtoupper($new[$i]);
    }
}

$win = (!isset($_GET['win'])) ? 0 : $_GET['win'];

if (isset($_GET['go']))
{
    if (!preg_match("/[^XO]/i", implode($new)))
    {
        $changedCount = 0;
        for ($i = 0; $i <= 8; $i++)
        {
            if ($old[$i] != $new[$i])
            {
                if ($new[$i] != $symbol && $win != 1)
                {
                    $errmsg = "Please Mark '$symbol'";
                }
                $changedCount++;
            }
        }
        if ($changedCount != 1 && $win != 1)
        {
            $errmsg = 'Mark Exactly One Box';
        }
    }
    else
    {
        $errmsg = "Please Mark '$symbol'";
    }
}

$win = 0;

(isset($errmsg)) ? $new = $old : $errmsg = '';

if (empty($start) && $turn == 1 && isset($_GET['go']))
{
    $new = array_fill(0, 9, '');
    $errmsg = '';
    switch ($difficulty)
    {
        case 0:
            $gameLogic->noviceMove($new, $symbol);
            break;
        case 1:
            $gameLogic->normalMove($new, $symbol);
            break;
        case 2:
            $gameLogic->geniusMove($new, $turn, $start, $symbol);
            break;
    }
    $turn++;
    $old = $new;
    $disabled = 'disabled';
} 
elseif ($new != $old)
{
    $turn++;
    $checkWin = $gameLogic->checkWin($new, $symbol);
    $win = $checkWin[0];
    $winningRow = $checkWin[1];
    if ($win == 1)
    {
        $errmsg = 'You Win!';
    } 
    elseif ($win == 2)
    {
        $win = 1;
        $errmsg = 'Computer Wins!';
    } 
    elseif ($win == 3)
    {
        $win = 1;
        $errmsg = 'Cat\'s Game.';
    } 
    elseif (isset($_GET['go']) && $turn <= 9)
    {
        switch ($difficulty)
        {
            case 0:
                $gameLogic->noviceMove($new, $symbol);
                break;
            case 1:
                $gameLogic->normalMove($new, $symbol);
                break;
            case 2:
                $gameLogic->geniusMove($new, $turn, $start, $symbol);
                break;
        }
        $checkWin = $gameLogic->checkWin($new, $symbol);
        $win = $checkWin[0];
        $winningRow = $checkWin[1];
        $turn++;
        if ($win == 1)
        {
            $errmsg = 'You Win!';
        } 
        elseif ($win == 2)
        {
            $win = 1;
            $errmsg = 'Computer Wins!';
        } 
        elseif ($win == 3)
        {
            $win = 1;
            $errmsg = 'Cat\'s Game.';
        } 
        else 
        {
            $old = $new;
        }
    }
}

if (empty($winningRow))
{
    $winningRow = array_fill(0, 3, 9);
}

for ($i = 0; $i <= 8; $i++)
{
    (!empty($new[$i]) || $win == 1) ? $readonly[$i] = "readonly" : $readonly[$i] = "";
    ($winningRow[0] == $i || $winningRow[1] == $i || $winningRow[2] == $i) ? $locked[$i] = "color:#C00000" : $locked[$i] = "color:#8A4B08";
    if ($winningRow[0] == 10)
    {
        $locked[$i] = "color:#006699";
    }
}

?>

<nav class="navbar navbar-dark bg-dark mb-5">
    <span class="navbar-brand mb-0 h1 mx-auto text-center">TicTacToe</span>
</nav>

<form action="1P.php" method="get" autocomplete="off">

<div class="container-fluid">
    <div class="row">

        <div class="col-md-12 col-lg-3 order-2 order-lg-1 mb-5 text-md-center text-lg-left control-row">

            <div class="row">

                <div class="col-sm-4 col-lg-12 text-sm-center">

                    <div class="btn-group">
                        <button id="difficulty-selected-button" type="button" class="btn btn-light difficulty-dropdown">Novice</button>
                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split difficulty-dropdown" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item difficulty-option" data-difficulty="Novice" href="#">Novice</a>
                            <a class="dropdown-item difficulty-option" data-difficulty="Normal" href="#">Normal</a>
                            <a class="dropdown-item difficulty-option" data-difficulty="Genius" href="#">Genius</a>
                        </div>
                    </div>

                </div>
                <div class="col-sm-3 col-lg-12 text-sm-center">

                    <div class="btn-group">
                        <button id="symbol-selected-button" type="button" class="btn btn-light symbol-dropdown">X</button>
                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split symbol-dropdown" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item symbol-option" data-symbol="X" href="#">X</a>
                            <a class="dropdown-item symbol-option" data-symbol="O" href="#">O</a>
                        </div>
                    </div>

                </div>
                <div class="col-sm-5 col-lg-12 text-sm-center">

                    <div class="btn-group">
                        <button id="player-start-selected-button" type="button" class="btn btn-light player-start-dropdown">Player Start</button>
                        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split player-start-dropdown" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item player-start-option" data-symbol="Player Start" href="#">Player Start</a>
                            <a class="dropdown-item player-start-option" data-symbol="Computer Start" href="#">Computer Start</a>
                        </div>
                    </div>

                </div>

            </div>

            <div id="message-div"><?= $errmsg; ?></div>

        </div>

        <div class="col-md-12 col-lg-6 order-1 order-lg-2 mb-5 text-sm-center">

            <table id="game-board" class="mx-auto text-center">
                <tr>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[0] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[1] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[2] ?></button></td>
                </tr>
                <tr>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[3] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[4] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[5] ?></button></td>
                </tr>
                <tr>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[6] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[7] ?></button></td>
                    <td><button type="button" class="btn btn-danger btn-sq-lg btn3d"><?= $gridValues[8] ?></button></td>
                </tr>
            </table>

        </div>

        <div class="col-md-12 col-lg-3 order-3 order-lg-3 mb-5 text-md-center text-lg-right control-row">
            <div class="row">

                <div class="col-sm-5 col-lg-12 text-sm-center">
                    <button type="submit" name="clear" class="btn btn-danger">Start Over</button>
                </div>
                
                <div class="col-sm-7 col-lg-12 text-sm-center">
                    <a href="2P.php" class="btn btn-warning">2 Player Game</a>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="old1" value='<?= $old[0]; ?>'>
<input type="hidden" name="old2" value='<?= $old[1]; ?>'>
<input type="hidden" name="old3" value='<?= $old[2]; ?>'>
<input type="hidden" name="old4" value='<?= $old[3]; ?>'>
<input type="hidden" name="old5" value='<?= $old[4]; ?>'>
<input type="hidden" name="old6" value='<?= $old[5]; ?>'>
<input type="hidden" name="old7" value='<?= $old[6]; ?>'>
<input type="hidden" name="old8" value='<?= $old[7]; ?>'>
<input type="hidden" name="old9" value='<?= $old[8]; ?>'>
<input type="hidden" name="turn" value='<?= $turn; ?>'>
<input type="hidden" name="win" value='<?= $win; ?>'>

</form>

</body>
</html>
