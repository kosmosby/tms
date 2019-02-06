jQuery(function ($) {
    $('.js-form-newsletter').each(function () {
        var $form = $(this);
        $form.submit(function (e) {
            e.preventDefault();

            var $message = $form.find('.message').addClass('uk-hidden');
            $.post($form.attr('action'), $form.serialize(), function (res) {
                if (res.message) {
                    message(res.message);
                    $form.get(0).reset();
                } else if (res.redirect) {
                    window.location.href = res.redirect;
                } else {
                    message('No valid response', true);
                }
            }).fail(function (res) {
                message(res.responseJSON || res.responseText || res.statusText, true);
            });

            function message(text, error) {
                $message.removeClass('uk-hidden uk-text-danger').addClass('uk-text-' + (error ? 'danger' : 'success')).html(text);
            }

        });
    });
});

