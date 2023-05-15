<?php

function pewa_additional_button() {
    if (is_user_logged_in()) {
        ?>
        <a href="javascript:void(0);" role="button" class="button" id="enqBtn"><?php echo esc_html__('Make an enquiry', 'woocommerce') ?></a>
        <!-- The Modal -->
        <div id="pewaModal" class="pewa-modal">
            <!-- Modal content -->
            <div class="pewa-modal-content">
                <div class="pewa-modal-header">
                    <span class="pewa-close">&times;</span>
                    <h2><?php echo esc_html__('Product Enquiry', 'woocommerce') ?></h2>
                </div>
                <div class="pewa-modal-body">
                    <?php
                    $contact_form_id = get_option('contact_form_id');
                    $form_title = get_the_title($contact_form_id);
                    // Use shortcode in a PHP file (outside the post editor).
                    echo do_shortcode('[contact-form-7 id="' . $contact_form_id . '" title="' . $form_title . '"]');
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
}

add_action('woocommerce_after_add_to_cart_button', 'pewa_additional_button');
