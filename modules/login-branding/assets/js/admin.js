jQuery(function ($) {
    function openMediaFrame(targetInput) {
        const frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();
            $(targetInput).val(attachment.url).trigger('change');
        });

        frame.open();
    }

    $(document).on('click', '#wom_login_logo_upload', function (e) {
        e.preventDefault();
        openMediaFrame('#wom_login_logo_url');
    });
});