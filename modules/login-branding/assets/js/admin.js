jQuery(function ($) {
    let mediaFrame = null;

    function openMediaFrame(targetInput) {
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            return;
        }

        if (mediaFrame) {
            mediaFrame.off('select');
        }

        mediaFrame = wp.media({
            title: 'Select Logo',
            button: {
                text: 'Use this image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        mediaFrame.on('select', function () {
            const attachment = mediaFrame.state().get('selection').first().toJSON();

            if (attachment && attachment.url) {
                $(targetInput).val(attachment.url).trigger('change');
            }
        });

        mediaFrame.open();
    }

    $(document).on('click', '#wom-login-logo-upload', function (e) {
        e.preventDefault();
        openMediaFrame('#wom-login-logo-url');
    });
});