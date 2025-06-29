$(document).on('submit', 'form#add_sell_form', function(e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();
    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function(result) {
            if (result.success == true) {
                    pos_print(result.receipt);
                    toastr.success(result.msg);
                setTimeout(function() {
                    window.location = '/sells';
                }, 4000);
            } else {
                toastr.error(result.msg);
                __enable_submit_button(form.find('button[type="submit"]'));
            }
        },
    });
});

$(document).on('submit', 'form#edit_sell_form', function(e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serialize();
    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button(form.find('button[type="submit"]'));
        },
        success: function(result) {
            if (result.success == true) {
                   toastr.success(result.msg);
                    pos_print(result.receipt);
                setTimeout(function() {
                    window.location = '/sells';
                }, 4000);

             } else {
                toastr.error(result.msg);
                __enable_submit_button(form.find('button[type="submit"]'));
            }
        },
    });
});