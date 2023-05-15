<div class="wrap">
    <h1><?php echo esc_html__('Contact Forms List', PEWA_TEXTDOMAIN); ?></h1>
    <?php
    $retrieved_nonce = isset($_POST['_wpnonce']) ? sanitize_text_field($_POST['_wpnonce']) : '';
    if (wp_verify_nonce($retrieved_nonce, 'form_action')) {
        $contact_form_id = isset($_POST['contact_forms_list']) ? sanitize_text_field($_POST['contact_forms_list']) : '';
        update_option('contact_form_id', $contact_form_id);
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html__('Setting saved!', PEWA_TEXTDOMAIN); ?></p>
        </div>
        <?php
    }
    ?>
    <form method="post" action="" novalidate="novalidate">
        <table class="form-table" role="presentation">
            <tbody>
                <?php
                $contact_form_id = get_option('contact_form_id');
                $args = array(
                    'post_type' => 'wpcf7_contact_form',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'order_by' => 'date',
                    'order' => 'DESC'
                );
                // Custom query. 
                $query = new WP_Query($args);
                // Check that we have query results. 
                if ($query->have_posts()) {
                    ?>
                    <tr>
                        <th scope="row"><label for="contact_forms_list"><?php echo esc_html__('Select Contact Form', PEWA_TEXTDOMAIN); ?></label></th>
                        <td>
                            <select name="contact_forms_list" id="contact_forms_list">
                                <option value="0"><?php echo esc_html__('--Select--', PEWA_TEXTDOMAIN); ?></option>
                                <?php
                                // Start looping over the query results. 
                                while ($query->have_posts()) {
                                    $query->the_post();
                                    ?>  
                                    <option value="<?php echo get_the_ID(); ?>" <?php selected(get_the_ID(), $contact_form_id); ?>><?php echo esc_html__(get_the_title(), PEWA_TEXTDOMAIN); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <?php wp_nonce_field('form_action'); ?>
                        </td>
                    </tr>
                    <?php
                }
                // Restore original post data. 
                wp_reset_postdata();
                ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save Changes', PEWA_TEXTDOMAIN); ?>">
        </p>
    </form>
</div>