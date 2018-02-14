import isLoading from 'is-loading';

$('select').select2({
    theme: 'bootstrap4',
    width: '100%',
});

$('[data-toggle="tooltip"]').tooltip();

// isLoading config.
$('#convert-form').on('submit', function () {
    isLoading({
        type: 'full-overlay',
        text: 'Processing...',
    }).loading();
});

// advanced settings switcher.
let $sw = $('#advanced-switcher');
$sw.on('click', function () {
    $('#advanced-settings').slideToggle(300);
    $sw.toggleClass('active');

    // set hidden field.
    let $hidden = $('#convert-form [name*="[showAdvanced]"]');
    $hidden.val($hidden.val() == 1 ? 0 : 1);
});

// tab switcher.
$('#tab-switcher a').on('click', function (e) {
    e.preventDefault();
    $(this).tab('show');

    // set hidden field.
    $('#convert-form [name*="[selectedTab]"]').val($(this).attr('href'));
});

// color previewer.
let $input = $('#convert-form [name*="[imageColor]"]');
$input.on('keyup', function () {
    $('#color-preview').css('background-color', '#' + $input.val());
}).keyup();
