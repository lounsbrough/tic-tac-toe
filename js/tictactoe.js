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
    
    const updateStateBoardFromScreen = async () => {
        currentGameState['game-board']['grid-values'] = [];
        $('#game-board').find('button').each((index, button) => {
            currentGameState['game-board']['grid-values'].push($(button).html());
        });
        await saveGameState();
    };

    const updateScreenBoardFromState = () => {
        $('#game-board').find('button').each((index, button) => {
            setGridCellValue($(button), currentGameState['game-board']['grid-values'][index]);
        });
    };

    const setGridCellValue = (button, symbol) => {
        if ($.inArray(symbol, ['X', 'O']) !== -1) {
            $(button).html(symbol).removeClass('btn-default').addClass(symbol == 'X' ? 'btn-primary' : 'btn-warning');
        }
    };

    const disableAllCells = (button, symbol) => {
        $('#game-board').find('button').prop('disabled', true);
    };
    
    const resetGameBoard = async () => {
        if (currentGameState['game-board'] != null) {
            currentGameState['game-board']['grid-values'] = new Array(9).fill('');
        }
        currentGameState['win-result'] = 0;
        currentGameState['winning-row'] = [];

        await saveGameState();
    };

    const setGameInProgress = async (inProgress) => {
        currentGameState['game-in-progress'] = inProgress;
        await saveGameState();
    };

    const setGameMessage = async (message) => {
        $('#game-message-alert').html(message);
        if (message != '') {
            $('#game-message-alert').removeClass('hide-message');
        }
        else
        {
            $('#game-message-alert').addClass('hide-message');
        }
        currentGameState['game-message'] = message;
        await saveGameState();
    };

    const getGameOverMessage = async (winResult) => {
        switch (winResult) {
            case 1:
                setGameMessage('You Win! 👤');
                break;
            case 2:
                setGameMessage('Computer Wins! 🤖');
                break;
            case 3:
                setGameMessage('Cat\'s Game 😸');
                break;
        }
        await saveGameState();
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
        }).done(async (response) => {
            gameOver = JSON.parse(response);
            const winResult = gameOver[0];
            const winningRow = gameOver[1];
            if (winResult > 0) {
                $('#game-board').find('button').filter((index, button) => {
                    return $.inArray(index, winningRow) == -1;
                }).removeClass('btn-primary btn-warning').addClass('btn-default');
                currentGameState['win-result'] = winResult;
                currentGameState['winning-row'] = winningRow;

                await getGameOverMessage(winResult);
                $('#game-message-alert').addClass('game-message-alert-' + (winningRow.length == 0 ? 'default' : (currentGameState['game-board']['grid-values'][winningRow[0]] == 'X' ? 'primary' : 'warning')));
            }
            else
            {
                disableAllCells();
                $('#game-board').find('button').filter((index, button) => {
                    return $(button).html() == '';
                }).prop('disabled', false);
            }
        });

        return gameOver;
    };
    
    const processPlayerMove = async (button) => {
        if (button.html == '') {
            return;
        }
    
        setGridCellValue(button, currentGameState['player-symbol']);
        await updateStateBoardFromScreen();

        const gameOver = await checkGameOver();

        if (gameOver[0] == 0)
        {
            disableAllCells();
            await processAIMove();
        }
    };

    const processAIMove = async () => {
        await $.ajax({
            url: 'ajax/process-ai-move.php',
            method: 'POST',
            cache: false
        }).done(async (response) => {
            currentGameState['game-board']['grid-values'] = JSON.parse(response);
            saveGameState();
            await updateScreenBoardFromState();
            await checkGameOver();
        });
    };

    $('#game-board').find('button').click(async (e, h) => {
        disableAllCells();
        await processPlayerMove($(e.target));
    });

    $('#start-over-button').click(async () => {
        await resetGameBoard();
        await setGameInProgress(false);
        await setGameMessage('');
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
