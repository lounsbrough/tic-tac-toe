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
});