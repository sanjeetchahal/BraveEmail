<?php
if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Save options on form submission
    if (isset($_POST['submit'])) {
        update_option('new_registration_client_template', wp_kses_post($_POST['new_registration_client_template']));
        update_option('new_registration_client_template_subject', sanitize_text_field($_POST['new_registration_client_template_subject']));
        update_option('new_registration_client_template_formatting', sanitize_text_field($_POST['new_registration_client_template_formatting']));
        update_option('new_registration_client_template_notification', sanitize_text_field($_POST['new_registration_client_template_notification']));
        
        update_option('new_registration_admin_template', wp_kses_post($_POST['new_registration_admin_template']));
        update_option('new_registration_admin_template_subject', sanitize_text_field($_POST['new_registration_admin_template_subject']));
        update_option('new_registration_admin_template_formatting', sanitize_text_field($_POST['new_registration_admin_template_formatting']));
        update_option('new_registration_admin_template_notification', sanitize_text_field($_POST['new_registration_admin_template_notification']));

        update_option('forgot_password_template', wp_kses_post($_POST['forgot_password_template']));
        update_option('forgot_password_template_subject', sanitize_text_field($_POST['forgot_password_template_subject']));
        update_option('forgot_password_template_formatting', sanitize_text_field($_POST['forgot_password_template_formatting']));
        update_option('forgot_password_template_notification', sanitize_text_field($_POST['forgot_password_template_notification']));

        
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    // Insert default templates
    if (isset($_POST['insert_default_templates'])) {
        $new_registration_client_template = get_option('new_registration_client_template');
        $new_registration_admin_template = get_option('new_registration_admin_template');
        $forgot_password_template = get_option('forgot_password_template');


        if (empty($new_registration_client_template)) {
            // Insert default On Registration Template
            $default_registration_template = "Hello {username}, thanks for registering. {reset_link}";
            update_option('new_registration_client_template', $default_registration_template);
            update_option('new_registration_client_template_subject', 'Login Details');
            update_option('new_registration_client_template_formatting', 'plain');
            update_option('new_registration_client_template_notification', 'enabled');
        
        }

        if (empty($new_registration_admin_template)) {
            // Insert default On Registration Template
            $default_registration_admin_template = "New user registration on your site {company}, Username : {username}, Email : {email}";
            update_option('new_registration_admin_template', $default_registration_admin_template);
            update_option('new_registration_admin_template_subject', 'New User Registration');
            update_option('new_registration_admin_template_formatting', 'plain');
            update_option('new_registration_admin_template_notification', 'enabled');

        }

        if (empty($forgot_password_template)) {
            // Insert default Forgot Password Template
            $default_forgot_password_template = "Hey you forgot your password. {reset_link}";
            update_option('forgot_password_template', $default_forgot_password_template);
            update_option('forgot_password_template_subject', 'Password Reset');
            update_option('forgot_password_template_formatting', 'plain');
            update_option('forgot_password_template_notification', 'enabled');
        }
    }

    // Retrieve saved options
    $new_registration_client_template = get_option('new_registration_client_template');
    $new_registration_client_template_subject = get_option('new_registration_client_template_subject');
    $new_registration_client_template_formatting = get_option('new_registration_client_template_formatting');
    $new_registration_client_template_notification = get_option('new_registration_client_template_notification');

    $new_registration_admin_template = get_option('new_registration_admin_template');
    $new_registration_admin_template_subject = get_option('new_registration_admin_template_subject');
    $new_registration_admin_template_formatting = get_option('new_registration_admin_template_formatting');
    $new_registration_admin_template_notification = get_option('new_registration_admin_template_notification');

    $forgot_password_template = get_option('forgot_password_template');
    $forgot_password_template_subject = get_option('forgot_password_template_subject');
    $forgot_password_template_formatting = get_option('forgot_password_template_formatting');
    $forgot_password_template_notification = get_option('forgot_password_template_notification');
    

    // Display the settings form
    ?>
    <div class="wrap">
        <h2>Custom Templates Settings</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" colspan="2">
                        <h3  style="margin:0">New Registration - For User</h3>
                    </th>
                    
                </tr>
                <tr valign="top">
                    <th scope="row">Formatting:</th>
                    <td>
                        <label><input type="radio" name="new_registration_client_template_formatting" value="plain" <?php checked($new_registration_client_template_formatting, 'plain'); ?>> Plain</label>
                        <label><input type="radio" name="new_registration_client_template_formatting" value="html" <?php checked($new_registration_client_template_formatting, 'html'); ?>> HTML</label>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Template Notification:</th>
                    <td>
                        <label><input type="radio" name="new_registration_client_template_notification" value="enabled" <?php checked($new_registration_client_template_notification, 'enabled'); ?>> Enabled</label>
                        <label><input type="radio" name="new_registration_client_template_notification" value="disabled" <?php checked($new_registration_client_template_notification, 'disabled'); ?>> Disabled</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Subject:</th>
                    <td>
                        <input type="text" name="new_registration_client_template_subject" value="<?php echo esc_attr($new_registration_client_template_subject); ?>" style="width:100%;" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">On Registration Template:</th>
                    <td>
                        <?php
                        wp_editor($new_registration_client_template, 'new_registration_client_template', array('textarea_name' => 'new_registration_client_template'));
                        ?>
                        <p>
                            <input type="button" class="button-secondary" value="Use Default Template" onclick="insertDefaultTemplate('new_registration_client_template', '<?php echo esc_js("Hello {username}, thanks for registering. {reset_link}"); ?>')" />
                        </p>
                    </td>
                </tr>
                
                <tr valign="top" style="border-top:1px solid">
                    <th scope="row" colspan="2">
                    <h3 style="margin:0">New Registration - For Admin</h3>

                    </th>
                    
                </tr>
                <tr valign="top">
                    <th scope="row">Formatting:</th>
                    <td>
                        <label><input type="radio" name="new_registration_admin_template_formatting" value="plain" <?php checked($new_registration_admin_template_formatting, 'plain'); ?>> Plain</label>
                        <label><input type="radio" name="new_registration_admin_template_formatting" value="html" <?php checked($new_registration_admin_template_formatting, 'html'); ?>> HTML</label>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Template Notification:</th>
                    <td>
                        <label><input type="radio" name="new_registration_admin_template_notification" value="enabled" <?php checked($new_registration_admin_template_notification, 'enabled'); ?>> Enabled</label>
                        <label><input type="radio" name="new_registration_admin_template_notification" value="disabled" <?php checked($new_registration_admin_template_notification, 'disabled'); ?>> Disabled</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Subject:</th>
                    <td>
                        <input type="text" name="new_registration_admin_template_subject" value="<?php echo esc_attr($new_registration_admin_template_subject); ?>" style="width:100%;" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">On Registration Template:</th>
                    <td>
                        <?php
                        wp_editor($new_registration_admin_template, 'new_registration_admin_template', array('textarea_name' => 'new_registration_admin_template'));
                        ?>
                        <p>
                            <input type="button" class="button-secondary" value="Use Default Template" onclick="insertDefaultTemplate('new_registration_admin_template', '<?php echo esc_js("New user registration on your site {company}, Username : {username}, Email : {email}"); ?>')" />
                        </p>
                    </td>
                </tr>
                <tr valign="top" style="border-top:1px solid">
                    <th scope="row" colspan="2">
                        <h3 style="margin:0">Forgot Password - For User</h3>

                    </th>
                    
                </tr>
                <tr valign="top">
                    <th scope="row">Formatting:</th>
                    <td>
                        <label><input type="radio" name="forgot_password_template_formatting" value="plain" <?php checked($forgot_password_template_formatting, 'plain'); ?>> Plain</label>
                        <label><input type="radio" name="forgot_password_template_formatting" value="html" <?php checked($forgot_password_template_formatting, 'html'); ?>> HTML</label>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Template Notification:</th>
                    <td>
                        <label><input type="radio" name="forgot_password_template_notification" value="enabled" <?php checked($forgot_password_template_notification, 'enabled'); ?>> Enabled</label>
                        <label><input type="radio" name="forgot_password_template_notification" value="disabled" <?php checked($forgot_password_template_notification, 'disabled'); ?>> Disabled</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Subject:</th>
                    <td>
                        <input type="text" name="forgot_password_template_subject" value="<?php echo esc_attr($forgot_password_template_subject); ?>" style="width:100%;" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Forgot your Password Template:</th>
                    <td>
                        <?php
                        wp_editor($forgot_password_template, 'forgot_password_template', array('textarea_name' => 'forgot_password_template'));
                        ?>
                        <p>
                            <input type="button" class="button-secondary" value="Use Default Template" onclick="insertDefaultTemplate('forgot_password_template', '<?php echo esc_js("Hey you forgot your password. {reset_link}"); ?>')" />
                        </p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="submit" class="button-primary" value="Save Changes" />
            </p>
        </form>
        <form method="post" action="">
            <p>
                <input type="submit" name="insert_default_templates" class="button-secondary" value="Insert Default Templates" />
                <span class="description">Click to insert default templates based on selected options.</span>
            </p>
        </form>
        <div class="ctp-help-text">
            <h3>Template Placeholders:</h3>
            <p>{company} - Company Name</p>
            <p>{username} - User's username</p>
            <p>{reset_link} - Forgot password link</p>
        </div>
    </div>
    <script>
        function insertDefaultTemplate(editorId, template) {
            var editor = tinyMCE.get(editorId);
            editor.setContent(template);
        }
    </script>
   