<!DOCTYPE html>
<html>
<body>

<link rel="stylesheet" href="css/tictactoe.css">

<!-- Background Image and Title -->

<div style = "position:absolute; left:0px; top:0px; z-index:0">
<img src='assets/title.png' height="200px" width="400px">
</div>
<div style = "position:absolute; right:5%; top:60px; z-index:0">
<img src='assets/credit.png' height="70px" width="300px">
</div>

<div style="position: absolute; top: 350px; z-index: 10">

<!-- PHP Code -->

<?PHP

require_once dirname(__FILE__).'/classes/game-logic.php';
$gameLogic = new GameLogic();

$turn = ((!isset($_GET['turn'])) ? ((rand(0, 1) == 0) ? 'O' : 'X') : $_GET['turn']);

$win = (!isset($_GET['win'])) ? 0 : $_GET['win'];

if (!isset($_GET['new1']))
{
    $new = array_fill(0, 9, '');
}
else
{
    for ($i = 0; $i <= 8; $i++)
    {
        $new[$i] = $_GET['new' . ($i + 1)];
        $new[$i] = empty(trim($new[$i])) ? '' : strtoupper($new[$i]);
    }
}  
  
if (isset($_GET['clear'])) 
{
    $win = 1;
    $turn = (rand(0, 1) == 0) ? 'O' : 'X';
    $new = array_fill(0, 9, '');
    $old = array_fill(0, 9, '');
}    
  
if ($win == 1) $new = array_fill(0, 9, ''); 

if (!isset($_GET['old1']))
{
    $old = array_fill(0, 9, '');
}
else
{
    for ($i = 0; $i <= 8; $i++)
    {
        $old[$i] = $_GET['old' . ($i + 1)];        
    }
}    
  
if (isset($_GET['new1']))
{
    if (!preg_match("/[^XO]/i", implode($new)))
    {
        $changedCount = 0;
        for ($i = 0; $i <= 8; $i++)
        {
            if ($old[$i] != $new[$i])
            {	
                if ($new[$i] != $turn && $win != 1) $errmsg = "Please Mark '".$turn."'";
                $changedCount++;
            }
        }
        if ($changedCount != 1 && $win != 1) $errmsg = "Mark Exactly One Box";
    }
    else
    {
        if ($win != 1) $errmsg = "Please Mark '".$turn."'";
    }	    
}
  
if (isset($errmsg))
{
    $new = $old;
}
else
{
    $errmsg = '';  	  
    $checkWin = $gameLogic->checkWin($new,$turn);
    $win = $checkWin[0];
    $winningRow = $checkWin[1];
    if ($win == 1)
    {
        $errmsg = $turn." Wins!";
        (rand(0,1) == 0) ? $turn = 'O' : $turn = 'X';
    }
    elseif ($win == 3)
    {
        $win = 1;
        $old = array_fill(0,9,'');    
        $errmsg = "Cat's Game.";
        (rand(0,1) == 0) ? $turn = 'O' : $turn = 'X';
        $old = array_fill(0, 9, '');     
    }
    else
    {  
        ($turn == 'X') ? $turn = 'O' : $turn = 'X';
        $win = 0;
        $old = $new; 
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

</div>

<div style="position: absolute; left: 30%; top: 200px; z-index: 0">

<form action="2P.php" method="get" autocomplete="off">

<table id="game-board">
    <tr>
        <td><input style='<?= $locked[0]; ?>' <?= $readonly[0]; ?> type="text" name="new1" value='<?= $new[0]; ?>' maxlength="1"></td>
        <td><input style='<?= $locked[1]; ?>' <?= $readonly[1]; ?> type="text" name="new2" value='<?= $new[1]; ?>' maxlength="1"></td>
        <td><input style='<?= $locked[2]; ?>' <?= $readonly[2]; ?> type="text" name="new3" value='<?= $new[2]; ?>' maxlength="1"></td>
    </tr>
    <tr>
        <td><input style='<?= $locked[3]; ?>' <?= $readonly[3]; ?> type="text" name="new4" value='<?= $new[3]; ?>' maxlength="1"></td>
        <td><input style='<?= $locked[4]; ?>' <?= $readonly[4]; ?> type="text" name="new5" value='<?= $new[4]; ?>' maxlength="1"></td> 
        <td><input style='<?= $locked[5]; ?>' <?= $readonly[5]; ?> type="text" name="new6" value='<?= $new[5]; ?>' maxlength="1"></td>
    </tr>
    <tr>
        <td><input style='<?= $locked[6]; ?>' <?= $readonly[6]; ?> type="text" name="new7" value='<?= $new[6]; ?>' maxlength="1"></td>
        <td><input style='<?= $locked[7]; ?>' <?= $readonly[7]; ?> type="text" name="new8" value='<?= $new[7]; ?>' maxlength="1"></td>
        <td><input style='<?= $locked[8]; ?>' <?= $readonly[8]; ?> type="text" name="new9" value='<?= $new[8]; ?>' maxlength="1"></td>
    </tr>
</table>

</div>

<input type="hidden" name="old1" value='<?= $old[0];?>'>
<input type="hidden" name="old2" value='<?= $old[1];?>'>
<input type="hidden" name="old3" value='<?= $old[2];?>'>
<input type="hidden" name="old4" value='<?= $old[3];?>'>
<input type="hidden" name="old5" value='<?= $old[4];?>'>
<input type="hidden" name="old6" value='<?= $old[5];?>'>
<input type="hidden" name="old7" value='<?= $old[6];?>'>
<input type="hidden" name="old8" value='<?= $old[7];?>'>
<input type="hidden" name="old9" value='<?= $old[8];?>'>
<input type="hidden" name="turn" value='<?= $turn;?>'>
<input type="hidden" name="win" value='<?= $win;?>'>

<input type="submit" name="go" value="GO" class="go">
<input type="submit" name="clear" value="Start Over" class="clear">

<!-- Turn Indicator -->

<div style="position: absolute; left: 50px; top: 405px;">
    <span>Turn:</span>
    <span><?= $turn;?></span>
</div>

<!-- Error Message Field -->

<div id="message-div" style="position: absolute; left: 50px; top: 725px"><?= $errmsg; ?></div>

</form>

<!-- Go To 1 Player Game -->

<div style = "position: absolute; left: 50px; top: 852px">
    <a href="1P.php" class="game-mode">1 Player Game</a>
</div>

</body>
</html>
