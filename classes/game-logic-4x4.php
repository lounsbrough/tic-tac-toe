<?PHP
class GameLogic
{
    public function noviceMove(&$grid, $playerSymbol)
    {
        $symbol = $this->oppositeSymbol($playerSymbol);

        $this->playRandom($grid, $symbol);
    }

    public function normalMove(&$grid, $playerSymbol)
    {
        $symbol = $this->oppositeSymbol($playerSymbol);

        if ($this->tryToWin($grid, $symbol)) return;

        if ($this->tryToBlockWin($grid, $symbol)) return;

        $this->playRandom($grid, $symbol);
    }

    public function geniusMove(&$grid, $playerSymbol)
    {
        $symbol = $this->oppositeSymbol($playerSymbol);

        if ($this->tryToWin($grid, $symbol)) return;

        if ($this->tryToBlockWin($grid, $symbol)) return;

        if ($this->tryToFork($grid, $symbol)) return;

        if ($this->tryToBlockFork($grid, $symbol)) return;

        if ($this->tryToForceDefense($grid, $symbol)) return;

        if ($this->isBoardEmpty($grid)) {
            rand(0, 1) == 0 ? $this->tryToPlayCenter($grid, $symbol) : $this->tryToPlayCorner($grid, $symbol);
            return;
        }

        if ($this->tryToPlayOppositeCorner($grid, $symbol)) return;

        if ($this->tryToPlayCorner($grid, $symbol)) return;

        $this->playRandom($grid, $symbol);
    }

    private function tryToWin(&$grid, $symbol)
    {
        $winningMoves = $this->winAvailable($grid, $symbol);
        if (!empty($winningMoves))
        {
            $grid[$winningMoves[rand(0, count($winningMoves) - 1)]] = $symbol;
            return true;
        }

        return false;
    }

    private function tryToBlockWin(&$grid, $symbol)
    {
        $opponentSymbol = $this->oppositeSymbol($symbol);
        $winningMoves = $this->winAvailable($grid, $opponentSymbol);
        if (!empty($winningMoves))
        {
            $grid[$winningMoves[rand(0, count($winningMoves) - 1)]] = $symbol;
            return true;
        }

        return false;
    }

    private function tryToFork(&$grid, $symbol)
    {
        foreach ($this->emptyBoxes($grid) as $i)
        {
            $testGrid = $grid;
            $testGrid[$i] = $symbol;
            $winningMoves = $this->winAvailable($testGrid, $symbol);
            if (count($winningMoves) >= 2)
            {
                $grid[$i] = $symbol;
                return true;
            }
        }

        return false;
    }

    private function tryToBlockFork(&$grid, $symbol)
    {
        $opponentSymbol = $this->oppositeSymbol($symbol);

        $blockingMoves = array();
        foreach ($this->emptyBoxes($grid) as $i)
        {
            $futureGrid = $grid;
            $futureGrid[$i] = $opponentSymbol;
            $winningMoves = $this->winAvailable($futureGrid, $opponentSymbol);
            if (count($winningMoves) >= 2)
            {
                $blockingMoves[] = $i;
            }
        }

        // Check if there is only one fork-blocking move
        if (count($blockingMoves) == 1)
        {
            $grid[$blockingMoves[0]] = $symbol;
            return true;
        }

        // Check if there is a fork-blocking move that doesn't result in another fork
        foreach ($blockingMoves as $blockingMove)
        {
            $futureGrid = $grid;
            $futureGrid[$blockingMove] = $symbol;
            if ($this->tryToBlockWin($futureGrid, $opponentSymbol))
            {
                $winningMoves = $this->winAvailable($futureGrid, $opponentSymbol);
                if (count($winningMoves) < 2)
                {
                    $grid[$blockingMove] = $symbol;
                    return true;
                }
            }
            else 
            {
                if (!$this->tryToFork($futureGrid, $opponentSymbol))
                {
                    $grid[$blockingMove] = $symbol;
                    return true;
                }
            }
        }

        return false;
    }

    private function tryToForceDefense(&$grid, $symbol)
    {
        $opponentSymbol = $this->oppositeSymbol($symbol);
        $corners = $this->corners();

        $forceDefenseMovesCorner = array();
        $forceDefenseMovesEdge = array();
        foreach ($this->emptyBoxes($grid) as $i)
        {
            $futureGrid = $grid;
            $futureGrid[$i] = $symbol;
            if ($this->tryToBlockWin($futureGrid, $opponentSymbol))
            {
                $winningMoves = $this->winAvailable($futureGrid, $opponentSymbol);
                if (count($winningMoves) < 2)
                {
                    if (in_array($i, $corners)) {
                        $forceDefenseMovesCorner[] = $i;
                    } else {
                        $forceDefenseMovesEdge[] = $i;
                    }
                }
            }
        }

        if (!empty($forceDefenseMovesCorner)) {
            $grid[$forceDefenseMovesCorner[array_rand($forceDefenseMovesCorner)]] = $symbol;
            return true;
        }

        if (!empty($forceDefenseMovesEdge)) {
            $grid[$forceDefenseMovesEdge[array_rand($forceDefenseMovesEdge)]] = $symbol;
            return true;
        }

        return false;
    }

    private function tryToPlayOppositeCorner(&$grid, $symbol)
    {
        $opponentSymbol = $this->oppositeSymbol($symbol);
        $oppositeCorners = $this->oppositeCorners();
        foreach ($oppositeCorners as $oppositeCorner)
        {
            if ($grid[$oppositeCorner[0]] == $opponentSymbol && empty($grid[$oppositeCorner[1]]))
            {
                $grid[$oppositeCorner[1]] = $symbol;
                return true;
            }
            else if (empty($grid[$oppositeCorner[0]]) && $grid[$oppositeCorner[1]] == $opponentSymbol)
            {
                $grid[$oppositeCorner[0]] = $symbol;
                return true;
            }
        }

        return false;
    }

    private function tryToPlayCorner(&$grid, $symbol)
    {
        $corners = $this->corners();
        while (!empty($corners))
        {
            $i = rand(0, count($corners) - 1);

            if (empty($grid[$corners[$i]]))
            {
                $grid[$corners[$i]] = $symbol;
                return true;
            }

            unset($corners[$i]);
            $corners = array_values($corners);
        }

        return false;
    }

    private function playRandom(&$grid, $symbol)
    {
        if ((array_count_values($grid)[''] ?? 0) == 0)
        {
            return;
        }

        $index = rand(0, 15);
        while (!empty($grid[$index]))
        {
            $index = rand(0, 15);
        }
        $grid[$index] = $symbol;
    }

    private function winAvailable($grid, $symbol)
    {
        $gridRows = $this->gridRows();
        $winningMoves = array();

        foreach ($gridRows as $gridRow)
        {
            $gridLine = array(
                $grid[$gridRow[0]] ?? '',
                $grid[$gridRow[1]] ?? '',
                $grid[$gridRow[2]] ?? '',
                $grid[$gridRow[3]] ?? ''
            );

            if ((array_count_values($gridLine)[$symbol] ?? 0) == 3 && (array_count_values($gridLine)[''] ?? 0) == 1)
            {
                $winningMoves[] = $gridRow[array_search('', $gridLine)];
            }
        }

        return $winningMoves;
    }

    public function checkGameOver($grid, $symbol)
    {
        $gridRows = $this->gridRows();

        $winningRow = array();
        $win = 0;
        foreach ($gridRows as $gridRow)
        {
            $gridLine = array(
                $grid[$gridRow[0]] ?? '',
                $grid[$gridRow[1]] ?? '',
                $grid[$gridRow[2]] ?? '',
                $grid[$gridRow[3]] ?? ''
            );

            if (max($gridLine) != '' && array_count_values($gridLine)[max($gridLine)] == 4)
            {
                $winningRow = $gridRow;
                break;
            }
        }
        
        if (!empty($winningRow))
        {
            ($grid[$winningRow[0]] == $symbol) ? $win = 1 : $win = 2;
        }
        
        if ($win == 0 && (array_count_values($grid)[''] ?? 0) == 0)
        {
            $win = 3;
        }
        
        return array(
            $win,
            $winningRow
        );    
    }

    private function emptyBoxes($grid)
    {
        return array_keys($grid, '');
    }
    
    private function isBoardEmpty($grid)
    {
        return count($this->emptyBoxes($grid)) == 16;
    }

    private function gridRows()
    {
        return array(
            array(0, 1, 2, 3),
            array(4, 5, 6, 7),
            array(8, 9, 10, 11),
            array(12, 13, 14, 15),
            array(0, 4, 8, 12),
            array(1, 5, 9, 13),
            array(2, 6, 10, 14),
            array(3, 7, 11, 15),
            array(0, 5, 10, 15),
            array(3, 6, 9, 12)
        );
    }

    private function corners()
    {
        return array(0, 3, 12, 15);
    }

    private function oppositeCorners()
    {
        return array(
            array(0, 15),
            array(3, 12)
        );
    }

    private function oppositeSymbol($symbol)
    {
        return $symbol == 'X' ? 'O' : 'X';
    }
}
