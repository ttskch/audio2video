$(function () {

    // ------------------------------
    // common
    // ------------------------------

    // select2 config.
    var withLabel = function (result) {
        if (!result.id) {
            return result.text;
        }

        var x, y;
        [x, y] = result.id.split('x');

        var label = 'unknown';
        if (x == y) {
            label = 'square';
        } else if (x * 3 == y * 4) {
            label = '4 : 3';
        } else if (x * 9 == y * 16) {
            label = '16 : 9';
        }

        return $(
            '<span><label class="label label-default">' + label + '</label> ' + result.text + '</span>'
        );
    };
    $('select').select2({
        theme: 'bootstrap',
        templateResult: withLabel
    });

    // ------------------------------
    // index
    // ------------------------------

    // is-loading config.
    $('#convert-form').on('submit', function () {
        $.isLoading({
            'class': "fa fa-spinner fa-spin",
        });
    });

    // advanced settings switcher.
    var $sw = $('#advanced-switcher');
    $sw.on('click', function () {
        $('#advanced-settings').slideToggle(300);
        $sw.toggleClass('active');

        // (en|dis)able advanced setting form widgets.
        if ($('#advanced-settings').css('display').toLowerCase() == 'none') {
            $('#advanced-settings').find('input, button, select, textarea').attr('disabled', true);
        } else {
            $('#advanced-settings').find('input, button, select, textarea').removeAttr('disabled');
        }
    });

    // tab switcher.
    $('#tab-switcher a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // color previewer.
    var $input = $('#convert-form [name*="image_color"]');
    $input
        .on('keyup', function () {
            $('#color-preview').css('background-color', '#' + $input.val());
        })
        .keyup()
    ;
});
