(function ($) {
    $(function () {
        var contact = $('.nanga-contact');
        contact.submit(function (e) {
            var form = $(this);
            form.addClass('is-sending');
            $.post(nangaContact.endpoint, {action: 'nanga_contact', fields: form.serialize()},
                function (response) {
                    if (response.success) {
                        form[0].reset();
                    }
                    form.find('.form__message').addClass('is-showing').html(response.data);
                    form.removeClass('is-sending');
                    setTimeout(function () {
                        form.find('.form__message').removeClass('is-showing');
                    }, 2500);
                }
            );
            e.preventDefault();
        });
        contact.keypress(function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                $(this).submit();
            }
        });
    });
})(jQuery);

