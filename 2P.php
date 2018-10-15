<!DOCTYPE html>
<html>
<body>

<style>
html { 
  background: url(assets/background.jpg) center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>

<!-- Background Image and Title -->

<div style = "position:absolute; left:0px; top:0px; z-index:0">
<img src='assets/title.png' height="200px" width="400px">
</div>
<div style = "position:absolute; right:5%; top:60px; z-index:0">
<img src='assets/credit.png' height="70px" width="300px">
</div>

<div style = "position:absolute; z-index:10">

<?PHP

(!isset($_GET['turn'])) ? ((rand(0,1) == 0) ? $turn = 'O' : $turn = 'X') : $turn = $_GET['turn'];

(!isset($_GET['win'])) ? $win = 0 : $win = $_GET['win'];

if (!isset($_GET['new1']))
  {
  $new = array_fill(0,9,"");
  }
else
  {
  for ($i=0; $i<=8; $i++)
    {
    $new[$i] = $_GET['new'.($i+1)];        
    if ($new[$i] == " ") $new[$i] = null;
    if ($new[$i] == "x") $new[$i] = "X";
    if ($new[$i] == "o") $new[$i] = "O";
    }
  }  
  
if (isset($_GET['clear'])) 
  {
  $win = 1;
  (rand(0,1) == 0) ? $turn = 'O' : $turn = 'X';  
  $new = array_fill(0,9,"");
  $old = array_fill(0,9,"");
  }    
  
if ($win == 1) $new = array_fill(0,9,""); 

if (!isset($_GET['old1']))
  {
  $old = array_fill(0,9,"");
  }
else
  {
  for ($i=0; $i<=8; $i++)
    {
    $old[$i] = $_GET['old'.($i+1)];        
    }
  }    
  
if (isset($_GET['new1']))
  {
  preg_match("/[^XO]/i",implode($new)) ? $invchar = 1 : $invchar = 0;
  if ($invchar == 0)
    {    
    $chgcount = 0;
    for ($i=0; $i<=8; $i++)
      {
      if ($old[$i] != $new[$i])
        {	
        if ($new[$i] != $turn and $win != 1) $errmsg = "Please Mark '".$turn."'";
        $chgcount++;
        }
      }
    if ($chgcount != 1 and $win != 1) $errmsg = "Mark Exactly One Box";
    }
  else
    {
    if ($win != 1) $errmsg = "Please Mark '".$turn."'";
    }	    
  }
  
if (isset($errmsg)) {
    $new = $old;
} else {
    $errmsg = "";  	  
    $check_win = check_win($new,$turn);
    $win = $check_win[0];
    $win_row = $check_win[1];
    if ($win == 1) {
        $errmsg = $turn." Wins!";
        (rand(0,1) == 0) ? $turn = 'O' : $turn = 'X';
    } elseif ($win == 3) {
        $win = 1;
        $old = array_fill(0,9,"");    
        $errmsg = "Cat's Game.";
        (rand(0,1) == 0) ? $turn = 'O' : $turn = 'X';
        $old = array_fill(0,9,"");     
    } else {  
        ($turn == 'X') ? $turn = 'O' : $turn = 'X';
        $win = 0;
        $old = $new; 
    }
}  

if (!isset($win_row)) {
    $win_row = array(9,9,9);
}

for ($i=0; $i<=8; $i++) {
    ($new[$i] != '' or $win == 1) ? $readonly[$i] = "readonly" : $readonly[$i] = "";
    ($win_row[0] == $i or $win_row[1] == $i or $win_row[2]== $i) ? $locked[$i]="color:#C00000" : $locked[$i]="color:#8A4B08";
    if ($win_row[0] == 10) $locked[$i]="color:#006699";
}
 
function check_win($new,$symbol) {

$win_row = array(9,9,9);
$win = 0;

if ($new[0] != null and $new[0] == $new[1] and $new[0] == $new[2]) {
    $win_row = array(0,1,2);
} elseif ($new[0] != null and $new[0] == $new[4] and $new[0] == $new[8]) {
    $win_row = array(0,4,8);
} elseif ($new[0] != null and $new[0] == $new[3] and $new[0] == $new[6]) {
    $win_row = array(0,3,6);
} elseif ($new[2] != null and $new[2] == $new[4] and $new[2] == $new[6]) {
    $win_row = array(2,4,6);
} elseif ($new[2] != null and $new[2] == $new[5] and $new[2] == $new[8]) {
    $win_row = array(2,5,8);
} elseif ($new[4] != null and $new[4] == $new[1] and $new[4] == $new[7]) {
    $win_row = array(1,4,7);
} elseif ($new[4] != null and $new[4] == $new[3] and $new[4] == $new[5]) {
    $win_row = array(3,4,5);
} elseif ($new[6] != null and $new[6] == $new[7] and $new[6] == $new[8]) {
    $win_row = array(6,7,8);
}

if ($win_row != array(9,9,9)) {	
    $win = 1;
}

if ($new[0] != null and $new[1] != null and $new[2] != null and
     $new[3] != null and $new[4] != null and $new[5] != null and
     $new[6] != null and $new[7] != null and $new[8] != null) {
    if ($win == 0) {
    	$win = 3;
    	$win_row = array(10,10,10);
    }
}

return array ($win,$win_row);

}  

?>

</div>

<div style="position: absolute; left: 30%; top: 200px; z-index: 0">

<style type="text/css">

#board input {width:150px;height:150px;font-size:100px;text-align:center;border:none;background:none;}

.go {position:absolute;right:5%;top:411px;font-size:45px;border:none;background:none;color:#990000}
.clear {position:absolute;right:5%;top:850px;font-size:25px;border:none;background:none;color:#990000}
.game-mode {font-size:25px;color:#990000}

</style>

<form action="2P.php" method="get" autocomplete="off">

<table id="board" border="0" style="border-collapse:collapse;border:none;">
<tr style = "border-bottom:5px solid #DF7401">
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[0];?>' <?= $readonly[0];?> type="text" name="new1" value='<?= $new[0];?>' maxlength="1"></td>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[1];?>' <?= $readonly[1];?> type="text" name="new2" value='<?= $new[1];?>' maxlength="1"></td>
<td><input style='<?= $locked[2];?>' <?= $readonly[2];?> type="text" name="new3" value='<?= $new[2];?>' maxlength="1"></td>
</tr>
<tr style = "border-bottom:5px solid #DF7401">
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[3];?>' <?= $readonly[3];?> type="text" name="new4" value='<?= $new[3];?>' maxlength="1"></td>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[4];?>' <?= $readonly[4];?> type="text" name="new5" value='<?= $new[4];?>' maxlength="1"></td> 
<td><input style='<?= $locked[5];?>' <?= $readonly[5];?> type="text" name="new6" value='<?= $new[5];?>' maxlength="1"></td>
</tr>
<tr>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[6];?>' <?= $readonly[6];?> type="text" name="new7" value='<?= $new[6];?>' maxlength="1"></td>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[7];?>' <?= $readonly[7];?> type="text" name="new8" value='<?= $new[7];?>' maxlength="1"></td>
<td><input style='<?= $locked[8];?>' <?= $readonly[8];?> type="text" name="new9" value='<?= $new[8];?>' maxlength="1"></td>
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

</form>

<!-- Turn Indicator -->

<table border="0" style="position: absolute; left: 50px; top: 405px; border: none; font-size: 45px">
<tr>
<td style = "padding-left:20px;color:white">Turn:</td>
<td style = "padding-left:20px;color:#990000"><?= $turn;?></td>
</tr>
</table>

<!-- Error Message Field -->

<table border="0" style="position: absolute; left: 50px; top: 725px; font-size:30px">
<tr>
<td style = "color:#990000"><?= $errmsg;?></td>
</tr>
</table>

<!-- Go To 1 Player Game -->

<div style = "position: absolute; left: 50px; top: 852px; z-index: 20; font-size: 25px">
<a href="1P.php"><button style = "color:#990000;border:none;background:none" class="game-mode">1 Player Game</button></a>
</div>

</body>
</html>
