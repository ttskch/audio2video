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

    // advanced settings switcher.
    var $sw = $('#advanced-switcher');
    $sw.on('click', function () {
        $('#advanced-settings').slideToggle(300);
        $sw.toggleClass('active');
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
