jQuery(document).ready(function($) {
    
    console.log("TEST");
    console.log(document.querySelectorAll('.bulk-meta-title, .bulk-meta-description'));
    
    const textareas = $('.bulk-meta-title, .bulk-meta-description');
    const defaultHeight = 'auto';
    
    // Function to resize the textarea to fit content
    function autoSizeTextarea(el) {
        el.style.height = 'auto';                     // Temporarily reset height
        el.style.height = el.scrollHeight + 'px';     // Set it to the scrollHeight
    }
    
    // Initialize all textareas on load (in case they have pre-filled content)
    textareas.each(function() {
        this.style.height = defaultHeight;
    });
    
    // Expand while typing/focused
    textareas.on('focus input', function() {
        autoSizeTextarea(this);
    });
    
    // Listen for blur event on meta title and description inputs
    textareas.on('blur', function() {
        this.style.height = defaultHeight;
        
        let postId = $(this).data('post-id');
        let metaKey = $(this).hasClass('bulk-meta-title') ? '_zynith_seo_meta_title' : '_zynith_seo_meta_description';
        let metaValue = $(this).val();

        // AJAX request to save meta information
        $.post(bulkMetaEditor.ajax_url, {
            action: 'bulk_meta_editor_save_meta',
            post_id: postId,
            meta_key: metaKey,
            meta_value: metaValue
        }).done(function (response) {
            
        }).fail(function () {
            console.error('AJAX request failed.');
        });
    });
});