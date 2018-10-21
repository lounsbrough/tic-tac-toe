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

        if ($this->tryToPlayCenter($grid, $symbol)) return;

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

        foreach ($this->emptyBoxes($grid) as $i)
        {
            $futureGrid = $grid;
            $futureGrid[$i] = $symbol;
            if ($this->tryToBlockWin($futureGrid, $opponentSymbol))
            {
                $winningMoves = $this->winAvailable($futureGrid, $opponentSymbol);
                if (count($winningMoves) < 2)
                {
                    $grid[$i] = $symbol;
                    return true;
                }
            }
        }
    }

    private function tryToPlayCenter(&$grid, $symbol)
    {
        if (empty($grid[4]))
        {
            $grid[4] = $symbol;
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

        $index = rand(0, 8);
        while (!empty($grid[$index]))
        {
            $index = rand(0, 8);
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
                $grid[$gridRow[2]] ?? ''
            );

            if ((array_count_values($gridLine)[$symbol] ?? 0) == 2 && (array_count_values($gridLine)[''] ?? 0) == 1)
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
                $grid[$gridRow[2]] ?? ''
            );

            if (max($gridLine) != '' && array_count_values($gridLine)[max($gridLine)] == 3)
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
            $winningRow = array_fill(0, 3, 10);
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

    private function gridRows()
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

    private function corners()
    {
        return array(0, 2, 6, 8);
    }

    private function oppositeCorners()
    {
        return array(
            array(0, 8),
            array(2, 6)
        );
    }

    private function oppositeSymbol($symbol)
    {
        return $symbol == 'X' ? 'O' : 'X';
    }
}