"use strict"

if (window.location.toString().indexOf('https') === -1) {
    setTimeout(() => {
        window.location.replace(window.location.toString().replace('http://', 'https://'));
    }, 500); 
}



function inputIsOk() {
    var type, val;
    var allGood = true;
    
    allGood = ($('#upload-file').val().length !== 0);
    // $('input').each(function() {
        
    //     val = $(this).val();

    //     if (val.length === 0) {
    //         allGood = false;
    //     }            
    // })

    if ($('select').val() === 'NULL' || $('select').val() === '') {
        allGood = false;
    }

    return allGood;
}


function displayMessage(msg, isError) {
    if (isError) {
        $('#message').removeClass('is-success').addClass('is-danger');
    } else {
        $('#message').removeClass('is-danger').addClass('is-success');
    }

    $('#message-text').html(msg || '');
    $('#message-wrapper').css('display', 'flex');

    $('.notification .delete').on('click touchend', function() {
        $('#message-wrapper').hide();
    })
}


function showGUID(data) {
    $('#upload-form').hide();
    $('#result-form').show();

    var url = "https://chat-analyzer.bruness.org/charts.php?chat=" + data.guid + "&pass=" + data.pass;

    $('#server-guid').val(data.guid);
    $('#server-pass').val(data.pass);
    
    $('#chat-stats-url').text(url);
    $('#chat-stats-url').attr("href", url);
}


function uploadFile() {
    var file = $('#upload-file').get(0);
    console.log('File type: ' + file.type);
    var formData = new FormData($('#upload-form')[0]);

    $('#progress-bar').show();

    $.ajax({
        url: 'process.php',
        type: 'POST',
        data: formData,
        // Tell jQuery not to process data or worry about content-type
        // You *must* include these options!
        cache: false,
        contentType: false,
        processData: false,

        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                // For handling the progress of the upload
                myXhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        $('progress').attr({
                            value: e.loaded,
                            max: e.total,
                        });
                    }
                } , false);
            }
            return myXhr;
        },

        success: function(data) {
            $('#upload-btn').removeClass('is-loading');
            var message;
            var defaultErrMsg = 'Something went wrong. Try again later or check your file for correctness.';
            if (!data || !data.success) {
                message = data ? (data.message || defaultErrMsg) : defaultErrMsg;
                displayMessage(message, true);
                return;
            }
            message = data ? data.message || 'Success!' : 'Success';
            
            showGUID(data);            
        },

        error: function(data) {
            $('#upload-btn').removeClass('is-loading');
            var message;
            var defaultErrMsg = 'Something went wrong. Try again later or check your file for correctness.';
            try {
                message = data ? (data.message || defaultErrMsg) : defaultErrMsg;
            } catch (error) {
                message = defaultErrMsg;
            }
            displayMessage(message, true);
            return;
        }
    });
}



$(function() {

    $('input, select').on('change keyup', function() {
        if (inputIsOk()) {
            $('#upload-btn').prop('disabled', false);
            $('#upload-btn').removeAttr('disabled');
        } else {
            $('#upload-btn').prop('disabled', true);
            $('#upload-btn').attr('disabled', true);
        }
    })

    $('.input.target').on('change keyup', function() {
        var min = parseInt($(this).attr('min'));
        var max = parseInt($(this).attr('max'));
        var val = $(this).val();
        val = val.replace(',', '.');
        if (val == "") return;
        val = parseFloat(val);
        if (val > max) val = max;
        if (val < min) val = min;
        $(this).val(val);
    })

    $('#upload-file').on('change', function() {
        var file = $(this).get(0);
        if (file.files.length > 0) {
            $('#filename').html(file.files[0].name);
        }
    })

    $('#upload-btn').on('click touchend', function() {
        if (!inputIsOk()) return;

        $('#upload-btn').addClass('is-loading');
        //$('#upload-form').submit();
        uploadFile();
    })

});