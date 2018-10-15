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

<div style="position: absolute; top: 350px; z-index: 10">

<!-- PHP Code -->

<?PHP

if (!isset($_GET['go'])) {
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
    if (isset($_GET['symboldsp'])) {
        $symbol = $_GET['symboldsp'];
        $start = isset($_GET['startdsp']) ? 'checked' : '';
    } else {
        $symbol = $_GET['symbolhidden'] == 'checked' ? 'X' : 'O';
        $start = $_GET['start'];
    }
}

$diffcheck = array_fill(0, 3, '');
$diffcheck[$difficulty] = 'checked';

$symbolcheck = array_fill(0, 2, '');
$symbol == 'X' ? $symbolcheck[0] = 'checked' : $symbolcheck[1] = 'checked';

$startcheck = $start == 'checked' ? 'checked' : '';

if (isset($_GET['go'])) {
    for ($i = 0; $i <= 8; $i++) {
        $old[$i] = $_GET['old' . ($i + 1)];
        $new[$i] = $_GET['new' . ($i + 1)];
        $new[$i] = empty(trim($new[$i])) ? null : strtoupper($new[$i]);
    }
}

$win = (!isset($_GET['win'])) ? 0 : $_GET['win'];

if (isset($_GET['go'])) {
    $invchar = preg_match("/[^XO]/i", implode($new)) ? 1 : 0;
    if ($invchar == 0) {
        $chgcount = 0;
        for ($i = 0; $i <= 8; $i++) {
            if ($old[$i] != $new[$i]) {
                if ($new[$i] != $symbol && $win != 1)
                    $errmsg = "Please Mark '$symbol'";
                $chgcount++;
            }
        }
        if ($chgcount != 1 && $win != 1)
            $errmsg = 'Mark Exactly One Box';
    } else {
        $errmsg = "Please Mark '$symbol'";
    }
}

$win = 0;

(isset($errmsg)) ? $new = $old : $errmsg = "";

if ($start == '' && $turn == 1 && isset($_GET['go'])) {
    $new = array_fill(0, 9, '');
    $errmsg = '';
    switch ($difficulty) {
        case 0:
            $new = novice_move($new, $symbol);
            break;
        case 1:
            $new = normal_move($new, $symbol);
            break;
        case 2:
            $new = genius_move($new, $turn, $start, $symbol);
            break;
    }
    $turn++;
    $old = $new;
    $disabled = 'disabled';
} elseif ($new != $old) {
    $turn++;
    $check_win = check_win($new, $symbol);
    $win = $check_win[0];
    $win_row = $check_win[1];
    if ($win == 1) {
        $errmsg = 'You Win!';
    } elseif ($win == 2) {
        $win = 1;
        $errmsg = 'Computer Wins!';
    } elseif ($win == 3) {
        $win = 1;
        $errmsg = 'Cat\'s Game.';
    } elseif (isset($_GET['go']) && $turn <= 9) {
        switch ($difficulty) {
            case 0:
                $new = novice_move($new, $symbol);
                break;
            case 1:
                $new = normal_move($new, $symbol);
                break;
            case 2:
                $new = genius_move($new, $turn, $start, $symbol);
                break;
        }
        $check_win = check_win($new, $symbol);
        $win = $check_win[0];
        $win_row = $check_win[1];
        $turn++;
        if ($win == 1) {
            $errmsg = 'You Win!';
        } elseif ($win == 2) {
            $win = 1;
            $errmsg = 'Computer Wins!';
        } elseif ($win == 3) {
            $win = 1;
            $errmsg = 'Cat\'s Game.';
        } else {
            $old = $new;
        }
    }
}

if (!isset($win_row)) {
    $win_row = array_fill(0, 3, 9);
}

for ($i = 0; $i <= 8; $i++) {
    ($new[$i] != '' or $win == 1) ? $readonly[$i] = "readonly" : $readonly[$i] = "";
    ($win_row[0] == $i or $win_row[1] == $i or $win_row[2] == $i) ? $locked[$i] = "color:#C00000" : $locked[$i] = "color:#8A4B08";
    if ($win_row[0] == 10)
        $locked[$i] = "color:#006699";
}

function novice_move($new, $playerSymbol)
{    
    $symbol = ($playerSymbol == 'X') ? 'O' : 'X';
    
    $index = rand(0, 8);
    while ($new[$index] != '') {
        $index = rand(0, 8);
    }
    $new[$index] = $symbol;
    
    return $new;    
}

function normal_move($new, $playerSymbol)
{    
    $symbol = ($playerSymbol == 'X') ? 'O' : 'X';
    
    $forced = forced($new, $symbol);
    $new = $forced[0];
    $done = $forced[1];
    
    if ($done) {
        return $new;
    }

    $index = rand(0, 8);
    while ($new[$index] != '') {
        $index = rand(0, 8);
    }
    $new[$index] = $symbol;
    
    return $new;    
}

function genius_move($new, $turn, $start, $playerSymbol)
{    
    $symbol = ($playerSymbol == 'X') ? 'O' : 'X';
    
    $old = $new;
    
    $forced = forced($new, $symbol);
    $new = $forced[0];
    $done = $forced[1];
    
    if ($done) {
        return $new;
    }
    
    if ($start == '') {
        
        if ($turn == 1) {
            $new[rand(0, 4) * 2] = $symbol;
        } elseif ($turn == 3) {
            if ($new[4] == $symbol) {
                if ($new[0] != null) {
                    $new[8] = $symbol;
                } elseif ($new[1] != null) {
                    $new[rand(0, 1) * 2 + 6] = $symbol;
                } elseif ($new[2] != null) {
                    $new[6] = $symbol;
                } elseif ($new[3] != null) {
                    $new[rand(0, 1) * 6 + 2] = $symbol;
                } elseif ($new[5] != null) {
                    $new[rand(0, 1) * 6] = $symbol;
                } elseif ($new[6] != null) {
                    $new[2] = $symbol;
                } elseif ($new[7] != null) {
                    $new[rand(0, 1) * 2] = $symbol;
                } elseif ($new[8] != null) {
                    $new[0] = $symbol;
                }
            } else {
                if ($new[4] != null) {
                    if ($new[0] == $symbol) {
                        $new[8] = $symbol;
                    } elseif ($new[2] == $symbol) {
                        $new[6] = $symbol;
                    } elseif ($new[6] == $symbol) {
                        $new[2] = $symbol;
                    } elseif ($new[8] == $symbol) {
                        $new[0] = $symbol;
                    }
                } elseif ($new[0] == $symbol) {
                    if ($new[1] == null && $new[2] == null) {
                        $new[2] = $symbol;
                    } elseif ($new[3] == null && $new[6] == null) {
                        $new[6] = $symbol;
                    }
                } elseif ($new[2] == $symbol) {
                    if ($new[0] == null && $new[1] == null) {
                        $new[0] = $symbol;
                    } elseif ($new[5] == null && $new[8] == null) {
                        $new[8] = $symbol;
                    }
                } elseif ($new[6] == $symbol) {
                    if ($new[0] == null && $new[3] == null) {
                        $new[0] = $symbol;
                    } elseif ($new[7] == null && $new[8] == null) {
                        $new[8] = $symbol;
                    }
                } elseif ($new[8] == $symbol) {
                    if ($new[2] == null && $new[5] == null) {
                        $new[2] = $symbol;
                    } elseif ($new[6] == null && $new[7] == null) {
                        $new[6] = $symbol;
                    }
                }
            }
        } elseif ($turn == 5) {
            if ($new[4] == $symbol) {
                if ($new[0] != null && $new[0] != $symbol) {
                    if ($new[7] != null && $new[7] != $symbol) {
                        $new[2] = $symbol;
                    } elseif ($new[5] != null && $new[5] != $symbol) {
                        $new[6] = $symbol;
                    }
                } elseif ($new[2] != null && $new[2] != $symbol) {
                    if ($new[7] != null && $new[7] != $symbol) {
                        $new[0] = $symbol;
                    } elseif ($new[3] != null && $new[3] != $symbol) {
                        $new[8] = $symbol;
                    }
                } elseif ($new[6] != null && $new[6] != $symbol) {
                    if ($new[1] != null && $new[1] != $symbol) {
                        $new[8] = $symbol;
                    } elseif ($new[5] != null && $new[5] != $symbol) {
                        $new[0] = $symbol;
                    }
                } elseif ($new[8] != null && $new[8] != $symbol) {
                    if ($new[1] != null && $new[1] != $symbol) {
                        $new[6] = $symbol;
                    } elseif ($new[3] != null && $new[3] != $symbol) {
                        $new[2] = $symbol;
                    }
                }
            } else {
                if ($new[0] == $symbol && $new[2] == $symbol) {
                    if ($new[6] != null) {
                        $new[8] = $symbol;
                    } elseif ($new[8] != null) {
                        $new[6] = $symbol;
                    } else {
                        $new[4] = $symbol;
                    }
                } elseif ($new[0] == $symbol && $new[6] == $symbol) {
                    if ($new[2] != null) {
                        $new[8] = $symbol;
                    } elseif ($new[8] != null) {
                        $new[2] = $symbol;
                    } else {
                        $new[4] = $symbol;
                    }
                } elseif ($new[2] == $symbol && $new[8] == $symbol) {
                    if ($new[0] != null) {
                        $new[6] = $symbol;
                    } elseif ($new[6] != null) {
                        $new[0] = $symbol;
                    } else {
                        $new[4] = $symbol;
                    }
                } elseif ($new[6] == $symbol && $new[8] == $symbol) {
                    if ($new[0] != null) {
                        $new[2] = $symbol;
                    } elseif ($new[2] != null) {
                        $new[0] = $symbol;
                    } else {
                        $new[4] = $symbol;
                    }
                }
            }
        }
        
    } else {
        
        if ($turn == 2) {
            if ($new[4] != null) {
                $new[rand(0, 1) * 2 + rand(0, 1) * 6] = $symbol;
            } else {
                $new[4] = $symbol;
            }
        } elseif ($turn == 4) {
            if ($new[4] != null && $new[4] != $symbol) {
                if ($new[0] == $symbol or $new[8] == $symbol) {
                    $new[rand(0, 1) * 4 + 2] = $symbol;
                } elseif ($new[2] == $symbol or $new[6] == $symbol) {
                    $new[rand(0, 1) * 8] = $symbol;
                }
            } elseif ($new[4] == $symbol) {
                if ($new[0] != null && $new[0] != $symbol && $new[8] != null && $new[8] != $symbol) {
                    $new[rand(0, 3) * 2 + 1] = $symbol;
                } elseif ($new[2] != null && $new[2] != $symbol && $new[6] != null && $new[6] != $symbol) {
                    $new[rand(0, 3) * 2 + 1] = $symbol;
                } elseif ($new[0] != null && $new[0] != $symbol && $new[8] == null) {
                    $new[8] = $symbol;
                } elseif ($new[8] != null && $new[8] != $symbol && $new[0] == null) {
                    $new[0] = $symbol;
                } elseif ($new[2] != null && $new[2] != $symbol && $new[6] == null) {
                    $new[6] = $symbol;
                } elseif ($new[6] != null && $new[6] != $symbol && $new[2] == null) {
                    $new[2] = $symbol;
                } elseif ($new[1] != null && $new[1] != $symbol && $new[7] != null && $new[7] != $symbol) {
                    $new[rand(0, 1) * 2 + rand(0, 1) * 6] = $symbol;
                } elseif ($new[3] != null && $new[3] != $symbol && $new[5] != null && $new[5] != $symbol) {
                    $new[rand(0, 1) * 2 + rand(0, 1) * 6] = $symbol;
                } elseif ($new[1] != null && $new[1] != $symbol && $new[3] != null && $new[3] != $symbol) {
                    $new[0] = $symbol;
                } elseif ($new[1] != null && $new[1] != $symbol && $new[5] != null && $new[5] != $symbol) {
                    $new[2] = $symbol;
                } elseif ($new[3] != null && $new[3] != $symbol && $new[7] != null && $new[7] != $symbol) {
                    $new[6] = $symbol;
                } elseif ($new[5] != null && $new[5] != $symbol && $new[7] != null && $new[7] != $symbol) {
                    $new[8] = $symbol;
                }
            }
        }
        
    }
    
    if ($old == $new) {
        while ($new == $old) {
            $i = rand(0, 8);
            if ($new[$i] == null)
                $new[$i] = $symbol;
        }
    }
    
    return $new;    
}

function forced($grid, $symbol)
{
    $winningLines = winningRows();

    $forcedWin = false;
    foreach ($winningLines as $winningLine) {
        $gridLine = array(
            $grid[$winningLine[0]] ?? '',
            $grid[$winningLine[1]] ?? '',
            $grid[$winningLine[2]] ?? ''
        );

        if ((array_count_values($gridLine)[$symbol] ?? 0) == 2 && (array_count_values($gridLine)[''] ?? 0) == 1) {
            $grid[$winningLine[array_search('', $gridLine)]] = $symbol;
            $forcedWin = true;
            break;
        }
    }

    return array(
        $grid,
        $forcedWin
    );
}

function check_win($grid, $symbol)
{
    $winningLines = winningRows();

    $winningRow = array();
    $win = 0;
    foreach ($winningLines as $winningLine) {
        $gridLine = array(
            $grid[$winningLine[0]] ?? '',
            $grid[$winningLine[1]] ?? '',
            $grid[$winningLine[2]] ?? ''
        );

        if (max($gridLine) != '' && array_count_values($gridLine)[max($gridLine)] == 3)
        {
            $winningRow = $winningLine;
            break;
        }
    }
    
    if (!empty($winningRow)) {
        ($new[$winningRow[0]] == $symbol) ? $win = 1 : $win = 2;
    }
    
    if ($win == 0 && (array_count_values($grid)[null] ?? 0) == 0)
    {
        $win = 3;
        $winningRow = array_fill(0, 3, 10);
    }
    
    return array(
        $win,
        $winningRow
    );    
}

function winningRows()
{
    return array(
        array(0, 1, 2),
        array(3, 4, 5),
        array(6, 7, 8),
        array(0, 3, 6),
        array(1, 4, 7),
        array(2, 5, 8),
        array(2, 4, 6),
        array(0, 4, 8)
    );
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

<!-- Input Fields - Old and New -->

<form action="1P.php" method="get" autocomplete="off">

<table id="board" border="0" style="border-collapse:collapse;border:none;">
<tr style = "border-bottom:5px solid #DF7401">
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[0]; ?>' <?= $readonly[0]; ?> type="text" name="new1" value='<?= $new[0]; ?>' maxlength="1"></td>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[1]; ?>' <?= $readonly[1]; ?> type="text" name="new2" value='<?= $new[1]; ?>' maxlength="1"></td>
<td><input style='<?= $locked[2]; ?>' <?= $readonly[2]; ?> type="text" name="new3" value='<?= $new[2]; ?>' maxlength="1"></td>
</tr>
<tr style = "border-bottom:5px solid #DF7401">
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[3]; ?>' <?= $readonly[3]; ?> type="text" name="new4" value='<?= $new[3]; ?>' maxlength="1"></td>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[4]; ?>' <?= $readonly[4]; ?> type="text" name="new5" value='<?= $new[4]; ?>' maxlength="1"></td> 
<td><input style='<?= $locked[5]; ?>' <?= $readonly[5]; ?> type="text" name="new6" value='<?= $new[5]; ?>' maxlength="1"></td>
</tr>
<tr>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[6]; ?>' <?= $readonly[6]; ?> type="text" name="new7" value='<?= $new[6]; ?>' maxlength="1"></td>
<td style = "border-right:5px solid #DF7401"><input style='<?= $locked[7]; ?>' <?= $readonly[7]; ?> type="text" name="new8" value='<?= $new[7]; ?>' maxlength="1"></td>
<td><input style='<?= $locked[8]; ?>' <?= $readonly[8]; ?> type="text" name="new9" value='<?= $new[8]; ?>' maxlength="1"></td>
</tr>
</table>

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

<input type="submit" name="go" value="GO" class="go">
<input type="submit" name="clear" value="Start Over" class="clear">

<!-- Difficulty Settings -->

<div style="position: absolute; left: 50px; top: 250px; z-index: 0; font-size: 23px">
<table>
<tr><td style = "color:#006699">Novice</td><td style = "padding:10px">
<input type="radio" name="difficulty" value=0 <?= $diffcheck[0] ?>>
</td></tr>
<tr><td style = "color:white">Normal</td><td style = "padding:10px">
<input type="radio" name="difficulty" value=1 <?= $diffcheck[1] ?>>
</td></tr>
<tr><td style = "color:#C00000">Genius</td><td style = "padding:10px">
<input type="radio" name="difficulty" value=2 <?= $diffcheck[2] ?>>
</td></tr>
</table>
</div>

<!-- Play As 'X' Or 'O' -->

<div style="position: absolute; left: 50px; top: 450px; z-index: 0; font-size: 23px">
<table>
<tr><td style = "white-space:nowrap;color:white">Play As X</td><td style = "padding:10px">
<input <?= $disabled ?> type="radio" name="symboldsp" value='X' <?= $symbolcheck[0] ?>>
</td></tr>
<tr><td style = "white-space:nowrap;color:white">Play As O</td><td style = "padding:10px">
<input <?= $disabled ?> type="radio" name="symboldsp" value='O' <?= $symbolcheck[1] ?>>
</td></tr>
</table>
<input type="hidden" name="symbolhidden" value='<?= $symbolcheck[0]; ?>'>
</div>

<!-- Player Start Or Computer Start -->

<div style="position: absolute; left: 50px; top: 600px; z-index: 0; font-size: 23px">
<table>
<tr><td style = "white-space:nowrap;color:white">Player Start?</td><td style = "padding:10px">
<input <?= $disabled ?> type="checkbox" name="startdsp" value='<?= $startcheck; ?>'>
</td></tr>
</table>
<input type="hidden" name="start" value='<?= $startcheck; ?>'>
</div>

<!-- Error Message Field -->

<table border="0" style="position: absolute; left: 50px; top: 725px; font-size:30px">
<tr>
<td style = "color:#990000"><?= $errmsg; ?></td>
</tr>
</table>

</form>

<!-- Go To 2 Player Game -->

<div style = "position: absolute; left: 50px; top: 852px; z-index: 20; font-size: 25px">
<a href="2P.php"><button style = "color:#990000;border:none;background:none" class="game-mode">2 Player Game</button></a>
</div>

</body>
</html>