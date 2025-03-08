jQuery(document).ready(function ($) {
    /************************************************************
     * BUILD PLACEHOLDER MAP FROM BUTTONS
     ************************************************************/
    let placeholdersMap = {};
    let $placeholderButtons = $('.zynith-placeholder-button');

    // Each button has:
    //   text() = the literal placeholder code, e.g. "%%title%%"
    //   data('placeholder') = the real text, e.g. "My Post Title"
    $placeholderButtons.each(function () {
        let code = $(this).text().trim(); // e.g. "%%title%%"
        let realVal = $(this).data('placeholder'); // e.g. "My Actual Title"
        placeholdersMap[code] = realVal;
    });

    /************************************************************
     * FUNCTION TO REPLACE PLACEHOLDERS IN A STRING
     ************************************************************/
    function replacePlaceholdersInString(str) {
        if (!str) return '';
        // For each known placeholder code, do a global replacement
        for (let code in placeholdersMap) {
            let realVal = placeholdersMap[code] || '';
            // Escape special regex chars in "code"
            let safeCode = code.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            let re = new RegExp(safeCode, 'g');
            str = str.replace(re, realVal);
        }
        return str;
    }

    /************************************************************
     * GOOGLE PREVIEW UPDATER
     *    Fields: #zynith_seo_meta_title, #zynith_seo_meta_description
     ************************************************************/
    let $titleInput = $('#zynith_seo_meta_title');
    let $descInput = $('#zynith_seo_meta_description');

    let $previewTitle = $('#zynith-seo-google-preview-title');
    let $previewDescription = $('.zynith-seo-google-preview-description');
    let $titleCount = $('#meta_title_count');
    let $descCount = $('#meta_description_count');

    function updatePreview() {
        // Get raw user text
        let rawTitle = $titleInput.val() || '';
        let rawDescription = $descInput.val() || '';

        // Replace placeholders with real text
        let replacedTitle = replacePlaceholdersInString(rawTitle);
        let replacedDescription = replacePlaceholdersInString(rawDescription);

        // Update the preview
        if ($previewTitle.length) $previewTitle.text(replacedTitle);
        if ($previewDescription.length) $previewDescription.text(replacedDescription);

        // Update counters (count the raw length, not replaced length)
        if ($titleCount.length) $titleCount.text(rawTitle.length);
        if ($descCount.length) $descCount.text(rawDescription.length);
    }

    // Listen for changes in the fields
    $titleInput.on('input', updatePreview);
    $descInput.on('input', updatePreview);

    // Fire once on load to initialize
    updatePreview();

    /************************************************************
     * CHARACTER COUNTERS
     ************************************************************/
    function updateCharacterCount(inputId, countId) {
        const inputField = $('#' + inputId);
        const countDisplay = $('#' + countId);

        inputField.on('input', function () {
            countDisplay.text(inputField.val().length);
        });
    }

    //updateCharacterCount('zynith_seo_meta_title', 'meta_title_count');
    //updateCharacterCount('zynith_seo_meta_description', 'meta_description_count');
    updateCharacterCount('zynith_seo_og_meta_title', 'og_meta_title_count');
    updateCharacterCount('zynith_seo_og_meta_description', 'og_meta_description_count');

    /************************************************************
     * IMAGE MEDIA PICKER
     ************************************************************/
    let $ogImageField = $('#zynith_seo_og_meta_image');
    let $ogImageButton = $('#zynith_seo_og_meta_image_button');
    let $ogImagePreview = $('#zynith_seo_og_meta_image_preview');

    // Media library
    $ogImageButton.on('click', function (e) {
        e.preventDefault();

        let custom_uploader = wp.media({
            title: 'Select OG Image',
            library: { type: 'image' },
            button: { text: 'Use this image' },
            multiple: false
        });

        custom_uploader.on('select', function () {
            let attachment = custom_uploader.state().get('selection').first().toJSON();
            $ogImageField.val(attachment.url);
            $ogImagePreview.attr('src', attachment.url).show();
        });

        custom_uploader.open();
    });

    $ogImageField.on('input', function () {
        let url = $(this).val().trim();
        if (url === '') {
            $ogImagePreview.hide();
        } else {
            $ogImagePreview.attr('src', url).show();
        }
    });

    /************************************************************
     * PLACEHOLDER BUTTON INSERTION
     *    Insert the literal code (e.g. %%title%%) into the fields
     ************************************************************/
    let $lastFocusedField = null;
    let $fields = $(
        '#zynith_seo_meta_title, #zynith_seo_meta_description, #zynith_seo_og_meta_title, #zynith_seo_og_meta_description, #zynith_seo_og_meta_image, #zynith_seo_schema_data'
    );

    // Disable the placeholder buttons by default
    $placeholderButtons.prop('disabled', true);

    // Focus => enable
    $fields.on('focus', function () {
        $lastFocusedField = $(this);
        $placeholderButtons.prop('disabled', false);
    });

    // Blur => check if we truly left these fields
    $fields.on('blur', function (e) {
        setTimeout(function () {
            let activeEl = document.activeElement;
            // If focus is NOT on a field or a placeholder button, disable them
            if (!$fields.is(activeEl) && !$placeholderButtons.is(activeEl)) {
                $lastFocusedField = null;
                $placeholderButtons.prop('disabled', true);
            }
        }, 10);
    });

    // Insert the *button text* into the field
    $placeholderButtons.on('mousedown', function (e) {
        e.preventDefault();
        if (!$lastFocusedField) return;

        // The button label is the literal code, e.g. "%%title%%"
        let literalCode = $(this).text().trim();

        let field = $lastFocusedField[0];
        if (field && typeof field.selectionStart === 'number') {
            let startPos = field.selectionStart;
            let endPos = field.selectionEnd;
            let originalVal = $lastFocusedField.val();

            let newVal = originalVal.substring(0, startPos) + literalCode + originalVal.substring(endPos);

            $lastFocusedField.val(newVal);

            // Move caret
            let caretPos = startPos + literalCode.length;
            field.setSelectionRange(caretPos, caretPos);
            field.focus();
        } else {
            // Fallback
            let oldVal = $lastFocusedField.val();
            $lastFocusedField.val(oldVal + literalCode).focus();
        }

        // Re-run preview update so we see changes immediately
        updatePreview();
    });
});
