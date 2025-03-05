<?php
function zynith_seo_tbyb_notices() {
    if (get_option('zynith_seo_tbyb') === 'expired') {
    ?>
    <div class="notice notice-error">
        <p>
            Thank you for trying Zynith SEO! Your trial period has ended.<br>
            If you’d like to continue using it, please <a href="https://zynith.app/my-account/" style="text-decoration: underline; color: #0073aa;" target="_blank">purchase a license key</a>.<br>
            If you decided it wasn’t for you, we’d love <a href="https://m.me/zynithseo" style="text-decoration: underline; color: #0073aa;" target="_blank">your feedback</a> on why you chose not to continue.
        </p>
    </div>
    <?php
    }
}