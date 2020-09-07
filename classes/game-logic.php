<?PHP
class GameLogic
{
    private $gridSize;

    public function __construct($gridSize) {
        $this->gridSize = $gridSize;
        $this->gridCellCount = pow($gridSize, 2);
    }

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
            if ($this->center() != null) {
                rand(0, 1) == 0 ? $this->tryToPlayCenter($grid, $symbol) : $this->tryToPlayCorner($grid, $symbol);
            } else {
                $this->tryToPlayCorner($grid, $symbol);
            }
            return;
        }
        
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
        $cornersAndCenter = $this->cornersAndCenter();

        $forceDefenseMovesCornerOrCenter = array();
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
                    if (in_array($i, $cornersAndCenter)) {
                        $forceDefenseMovesCornerOrCenter[] = $i;
                    } else {
                        $forceDefenseMovesEdge[] = $i;
                    }
                }
            }
        }
        
        if (!empty($forceDefenseMovesCornerOrCenter)) {
            $grid[$forceDefenseMovesCornerOrCenter[array_rand($forceDefenseMovesCornerOrCenter)]] = $symbol;
            return true;
        }
        
        if (!empty($forceDefenseMovesEdge)) {
            $grid[$forceDefenseMovesEdge[array_rand($forceDefenseMovesEdge)]] = $symbol;
            return true;
        }
        
        return false;
    }

    private function tryToPlayCenter(&$grid, $symbol)
    {
        $center = $this->center();

        if ($center == null) {
            return false;
        }

        if (empty($grid[$center]))
        {
            $grid[$this->center()] = $symbol;
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

        $index = rand(0, $this->gridCellCount - 1);
        while (!empty($grid[$index]))
        {
            $index = rand(0, $this->gridCellCount - 1);
        }
        $grid[$index] = $symbol;
    }

    private function winAvailable($grid, $symbol)
    {
        $gridLines = $this->gridLines();
        $winningMoves = array();

        foreach ($gridLines as $gridLine)
        {
            $gridLineValues = array_map(
                function($value) use ($grid) {
                    return $grid[$value] ?? '';
                },
                $gridLine
            );

            if ((array_count_values($gridLineValues)[$symbol] ?? 0) == $this->gridSize - 1 && (array_count_values($gridLineValues)[''] ?? 0) == 1)
            {
                $winningMoves[] = $gridLine[array_search('', $gridLineValues)];
            }
        }

        return $winningMoves;
    }

    public function checkGameOver($grid, $symbol)
    {
        $gridLines = $this->gridLines();

        $winningRow = array();
        $win = 0;
        foreach ($gridLines as $gridLine)
        {
            $gridLineValues = array_map(
                function($value) use ($grid) {
                    return $grid[$value] ?? '';
                },
                $gridLine
            );

            if (max($gridLineValues) != '' && array_count_values($gridLineValues)[max($gridLineValues)] == $this->gridSize)
            {
                $winningRow = $gridLine;
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
        return count($this->emptyBoxes($grid)) == $this->gridCellCount;
    }

    private function gridLines()
    {
        $gridLines = array();

        for ($i=0; $i<$this->gridSize; $i++) {
            $rowLine = array();
            $columnLine = array();
            for ($j=0; $j<$this->gridSize; $j++) {
                array_push($rowLine, $i * $this->gridSize + $j);
                array_push($columnLine, $i + $j * $this->gridSize);
            }
            array_push($gridLines, $rowLine);
            array_push($gridLines, $columnLine);
        }

        $diagonalLeft = array();
        $diagonalRight = array();
        for ($i=0; $i<$this->gridSize; $i++) {
            array_push($diagonalLeft, $i * ($this->gridSize + 1));
            array_push($diagonalRight, ($i + 1) * ($this->gridSize - 1));
        }

        array_push($gridLines, $diagonalLeft);
        array_push($gridLines, $diagonalRight);

        return $gridLines;
    }

    private function center()
    {
        return $this->gridCellCount % 2 == 0 ? null : ($this->gridCellCount - 1) / 2;
    }
    
    private function corners()
    {
        return array(
            0,
            $this->gridSize - 1,
            $this->gridCellCount - $this->gridSize,
            $this->gridCellCount - 1
        );
    }

    private function cornersAndCenter()
    {
        $cornersAndCenter = $this->corners();

        $center = $this->center();
        if ($center != null) {
            array_push($cornersAndCenter, $center);
        }

        return $cornersAndCenter;
    }

    private function oppositeCorners()
    {
        return array(
            array(
                0,
                $this->gridCellCount - 1),
            array(
                $this->gridSize - 1,
                $this->gridCellCount - $this->gridSize
            )
        );
    }

    private function oppositeSymbol($symbol)
    {
        return $symbol == 'X' ? 'O' : 'X';
    }
}
