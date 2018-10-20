const applySelectedDifficulty = (difficulty) => {
    $('#difficulty-selected-button').html(difficulty);
    $('.difficulty-dropdown').removeClass('btn-light btn-warning btn-danger');
    switch (difficulty) {
        case 'Novice':
            $('.difficulty-dropdown').addClass('btn-light');
            break;
        case 'Normal':
            $('.difficulty-dropdown').addClass('btn-warning');
            break;
        case 'Genius':
            $('.difficulty-dropdown').addClass('btn-danger');
            break;
    }
};

const getGameStateObject = () => {
    let gameState = {
        "game-board": {
            "grid-values" : []
        }
    };

    $('#game-board').find('button').each((index, value) => {
        gameState["game-board"]["grid-values"].push($(value).html());
    });

    return gameState;
};

const clearGameBoard = () => {
    $('#game-board').find('button').html('').prop('disabled', false);
};

const saveGameState = () => {
    $.ajax({
        url: 'ajax/save-game-state.php',
        method: 'POST',
        data: getGameStateObject(),
        cache: false
    });
};

$(() => {
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

    $('#game-board').find('button').click((e, h) => {
        $(e.target).html(Math.random(0, 1) > 0.5 ? 'X' : '');
        saveGameState();
    });

    $('#start-over-button').click(() => {
        clearGameBoard();
        saveGameState();
    })
});