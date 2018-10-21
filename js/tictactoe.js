$(() => {
    const applySelectedDifficulty = (difficulty) => {
        $('#difficulty-selected-button').html(difficulty);
        $('.difficulty-dropdown').removeClass('btn-light btn-primary btn-warning btn-danger').prop('disabled', false);
        switch (difficulty) {
            case 'Novice':
                $('.difficulty-dropdown').addClass('btn-primary');
                break;
            case 'Normal':
                $('.difficulty-dropdown').addClass('btn-warning');
                break;
            case 'Genius':
                $('.difficulty-dropdown').addClass('btn-danger');
                break;
        }

        currentGameState['game-difficulty'] = difficulty;
        saveGameState();
    };

    $('.difficulty-option').click((e, h) => {
        applySelectedDifficulty($(e.target).attr('data-difficulty'));
    });

    $('#difficulty-selected-button').click((e, h) => {
        const selectedDifficulty = $(e.target).html();
        switch (selectedDifficulty) {
            case 'Novice':
                applySelectedDifficulty('Normal');
                break;
            case 'Normal':
                applySelectedDifficulty('Genius');
                break;
            case 'Genius':
                applySelectedDifficulty('Novice');
                break;
        }
    });

    const applySelectedSymbol = (symbol) => {
        $('#symbol-selected-button').html(symbol);
        $('.symbol-dropdown').removeClass('btn-light btn-primary btn-warning').prop('disabled', false);
        $('.symbol-dropdown').addClass(symbol == 'X' ? 'btn-primary' : 'btn-warning');

        currentGameState['player-symbol'] = symbol;
        saveGameState();
    };

    $('.symbol-option').click((e, h) => {
        applySelectedSymbol($(e.target).attr('data-symbol'));
    });

    $('#symbol-selected-button').click((e, h) => {
        const selectedSymbol = $(e.target).html();
        applySelectedSymbol(selectedSymbol == 'X' ? 'O' : 'X');
    });

    const applyPlayerStart = (playerStart) => {
        $('#player-start-selected-button').html(playerStart ? 'Player' : 'Computer');
        $('.player-start-dropdown').removeClass('btn-light btn-primary btn-warning').prop('disabled', false);
        $('.player-start-dropdown').addClass(playerStart ? 'btn-primary' : 'btn-warning');

        currentGameState['player-start'] = playerStart;
        saveGameState();
    };

    $('.player-start-option').click((e, h) => {
        applyPlayerStart($(e.target).attr('data-player-start') == 'true');
    });

    $('#player-start-selected-button').click((e, h) => {
        const selectedPlayerStart = $(e.target).html() == 'Player' ? false : true;
        applyPlayerStart(selectedPlayerStart);
    });
    
    const updateBoardGridState = () => {
        currentGameState['game-board']['grid-values'] = [];
        $('#game-board').find('button').each((index, value) => {
            currentGameState['game-board']['grid-values'].push($(value).html());
        });
    };
    
    const resetGameBoard = () => {
        if (currentGameState['game-board'] != null) {
            currentGameState['game-board']['grid-values'] = [];
        }
        currentGameState['winning-row'] = [];
    };

    const setGameInProgress = (inProgress) => {
        currentGameState['game-in-progress'] = inProgress;
        saveGameState();
    };
    
    const saveGameState = async () => {
        await $.ajax({
            url: 'ajax/save-game-state.php',
            method: 'POST',
            data: currentGameState,
            cache: false
        });
    };

    const checkGameOver = async () => {
        let gameOver;

        await $.ajax({
            url: 'ajax/check-game-over.php',
            method: 'POST',
            cache: false
        }).done((response) => {
            gameOver = JSON.parse(response);
            const winResult = gameOver[0];
            const winningRow = gameOver[1];
            if (winResult != 0) {
                $('#game-board').find('button').prop('disabled', true);
                $('#game-board').find('button').filter((index, value) => {
                    return $.inArray(index, winningRow) > -1;
                }).addClass('winning-cell');
                currentGameState['winning-row'] = winningRow;
                saveGameState();
            }
        });

        return gameOver;
    };
    
    const processPlayerMove = async (button) => {
        if (button.html == '') {
            return;
        }
    
        button.html(currentGameState['player-symbol']).removeClass('btn-default').addClass(currentGameState['player-symbol'] == 'X' ? 'btn-primary' : 'btn-warning');
        updateBoardGridState();
        await saveGameState();

        const gameOver = await checkGameOver();

        if (gameOver[0] == 0)
        {
            await processAIMove();
            window.location.reload();
        }
    };

    const processAIMove = async () => {
        await $.ajax({
            url: 'ajax/process-ai-move.php',
            method: 'POST',
            cache: false
        }).done((response) => {
            currentGameState['game-board']['grid-values'] = JSON.parse(response);
        });

        await checkGameOver();
    };

    $('#game-board').find('button').click((e, h) => {
        processPlayerMove($(e.target));
    });

    $('#start-over-button').click(() => {
        resetGameBoard();
        setGameInProgress(false);
        saveGameState();
        window.location.reload();
    });

    $('#start-game-button').click(async () => {
        setGameInProgress(true);

        if (!currentGameState['player-start'] && currentGameState['game-board']['grid-values'].filter((e) => e != '').length == 0) {
            await processAIMove();
        }

        window.location.reload();
    });

    applySelectedDifficulty(currentGameState['game-difficulty']);
    applySelectedSymbol(currentGameState['player-symbol']);
    applyPlayerStart(currentGameState['player-start'] == 'true');
});