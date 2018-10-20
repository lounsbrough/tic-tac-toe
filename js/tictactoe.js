$(() => {
    const applySelectedDifficulty = (difficulty) => {
        $('#difficulty-selected-button').html(difficulty);
        $('.difficulty-dropdown').removeClass('btn-primary btn-warning btn-danger');
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
        $('.symbol-dropdown').removeClass('btn-primary btn-warning');
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
        $('.player-start-dropdown').removeClass('btn-primary btn-warning');
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
        currentGameState["game-board"]["grid-values"] = [];
        $('#game-board').find('button').each((index, value) => {
            currentGameState["game-board"]["grid-values"].push($(value).html());
        });
    };
    
    const resetGameBoard = () => {
        currentGameState["game-board"]["grid-values"] = [];
    };

    const setGameInProgress = (inProgress) => {
        currentGameState['game-in-progress'] = inProgress;
        saveGameState();
    }
    
    const saveGameState = () => {
        $.ajax({
            url: 'ajax/save-game-state.php',
            method: 'POST',
            data: currentGameState,
            cache: false
        });
    };
    
    const processPlayerMove = (button) => {
        if (button.html == '') {
            return;
        }
    
        button.html(playerSymbol);
        updateBoardGridState()
        saveGameState();
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

    $('#start-game-button').click(() => {
        setGameInProgress(true);
        window.location.reload();
    });

    applySelectedDifficulty(gameDifficulty);
    applySelectedSymbol(playerSymbol);
    applyPlayerStart(playerStart);
});