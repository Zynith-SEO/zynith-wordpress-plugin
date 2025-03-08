jQuery(document).ready(function ($) {
    // When the "Select Image" button is clicked
    $('.zynith-seo-upload-button').on('click', function (e) {
        e.preventDefault();

        // The input field to which we'll set the image URL
        var targetInputSelector = $(this).data('target');

        // Create a new media frame
        var frame = wp.media({
            title: 'Select or Upload a Logo',
            button: { text: 'Use this image' },
            multiple: false // only one image
        });

        // When an image is selected in the media frame...
        frame.on('select', function () {
            // Get the attachment JSON data
            var attachment = frame.state().get('selection').first().toJSON();
            // Put the image URL into the text field
            $(targetInputSelector).val(attachment.url);
        });

        // Finally, open the modal
        frame.open();
    });
});
