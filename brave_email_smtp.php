<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class braveEmail
{
    private $docMenuName;

    public function setDocumentationMenu($menuName)
    {
       
        $this->docMenuName = $menuName;

        return $this;
    }

    public function doSetup()
    {
        add_action( 'admin_menu', array(&$this, 'email_submenu_page') );
        add_action( 'phpmailer_init', array(&$this, 'custom_phpmailer_init' ) );
        add_filter( 'wp_mail_from', array(&$this, 'filter_from_email'), PHP_INT_MAX );
        // add_filter( 'wp_mail_from_name', array( $this, 'filter_mail_from_name' ), PHP_INT_MAX );
        add_filter('wp_new_user_notification_email', array(&$this, 'custom_new_user_notification_email'), 10, 3);
        add_filter('wp_new_user_notification_email_admin', array(&$this, 'custom_new_user_notification_email_admin'), 10, 3);
        add_filter('retrieve_password_message', array(&$this, 'customize_password_reset_email'), 10, 4);
    }

    function customize_password_reset_email($message, $key, $user_login, $user_data) {
       
    
        // Customize the content of the email
        // $message = "Hi " . $user_data->user_login . ",\n\n";
        // $message .= "We received a request to reset the password for your account.\n\n";
        // $message .= "To reset your password, please click on the following link:\n";
        // $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_data->user_login), 'login') . "\n\n";
        // $message .= "If you didn't request a password reset, you can ignore this email.\n\n";
        // $message .= "Thank you,\nThe " . get_bloginfo('name') . " Team";

        $blogname = get_bloginfo('name');
        $template = array(
			'post_type' => 'mails',
			'numberposts'  => 1,
			'post_status'   => 'publish',
			'meta_query' => array(
				array(
					'key'   => 'notification',
					'value' => 'forgot-password',
				)
			)
		);

		$templatePosts = get_posts($template);
        
        if(count($templatePosts)){
            $subject = get_post_meta( $templatePosts[0]->ID, 'subject',true );
        
            $email['subject'] = '['.$blogname.'] '.$subject;
    
            $message = get_post_meta( $templatePosts[0]->ID, 'message-body',true );
    
            $reset_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_data->user_login), 'login');
    
            $message = str_replace("[reset-link]",$reset_url,$message);
            $message = str_replace("[company]",$blogname,$message);
            $message = str_replace("[username]",$user_data->user_login,$message);
        }
       
        
        
    
        return $message;
    }
     public function custom_new_user_notification_email($email, $user, $blogname){


        $template = array(
			'post_type' => 'mails',
			'numberposts'  => 1,
			'post_status'   => 'publish',
			'meta_query' => array(
				array(
					'key'   => 'notification',
					'value' => 'new-registration-client',
				)
			)
		);

		$templatePosts = get_posts($template);
        
        if(count($templatePosts)){
            $subject = get_post_meta( $templatePosts[0]->ID, 'subject',true );
        
            $email['subject'] = '['.$blogname.'] '.$subject;
    
            $message = get_post_meta( $templatePosts[0]->ID, 'message-body',true );
    
            $reset_key = get_password_reset_key($user);
            $reset_url = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');
    
            $message = str_replace("[reset-link]",$reset_url,$message);
            $message = str_replace("[company]",$blogname,$message);
            $message = str_replace("[username]",$user->user_login,$message);
            
            
        }
       
        
        $email['message'] = $message;

        return $email;
    }
    public function custom_new_user_notification_email_admin($email, $user, $blogname){


        $template = array(
			'post_type' => 'mails',
			'numberposts'  => 1,
			'post_status'   => 'publish',
			'meta_query' => array(
				array(
					'key'   => 'notification',
					'value' => 'new-registration',
				)
			)
		);

		$templatePosts = get_posts($template);
        
        if(count($templatePosts)){
            $subject = get_post_meta( $templatePosts[0]->ID, 'subject',true );
        
            $email['subject'] = '['.$blogname.'] '.$subject;
    
            $message = get_post_meta( $templatePosts[0]->ID, 'message-body',true );
    
           
    
            $message = str_replace("[email]",$user->user_email,$message);
            $message = str_replace("[company]",$blogname,$message);
            $message = str_replace("[username]",$user->user_login,$message);
            
        }
       
        
        $email['message'] = $message;

        return $email;
    }

    public function custom_phpmailer_init( $phpmailer ) { 
            
            $message = $phpmailer->Body;
            $recipients = $phpmailer->getToAddresses();
            
            $toEmail = array();
            foreach ( $recipients as $recipient ) {
                $toEmail[] = $recipient[0];
            }
            $recipients = implode( ', ', $toEmail );
            $emailConfig = array();
            $emailConfig['subject'] = $phpmailer->Subject;
            $emailConfig['message'] = $message;
            $emailConfig['htmlPath'] = false;
            $emailConfig['to_email'] = $recipients;
            $emailConfig['from_email'] = $phpmailer->From;
            $emailConfig['from_name'] = $phpmailer->FromName;
            $result = $this->to_email($emailConfig);  
            if($result){
                return '<p class="text-green-600 text-center pt-2">Successfully Sent!.</p>';
            } 
            else
            {
                return '<p class="text-green-600 text-center pt-2">Opps!.</p>';
            }
    }
    public function filter_from_email( $wp_email ) {
        $wp_email = get_bloginfo('admin_email');
        return $wp_email;
    }
    public function email_submenu_page()
    {
        
        // add_submenu_page(
        //     'options-general.php', // Parent slug jis bhi page ka submenu banan na us page par click kar ke last path copy kar lo edit.php?post_type=spaces
        //     $this->docMenuName, // Page title
        //     $this->docMenuName, // Menu title
        //     'administrator', // Capability required
        //     'brave_email_smtp', // Menu slug
        //     array(&$this, 'brave_email_smtp_page_callback') // Callback function
        //   );

          $menuAccessCapability = 'manage_options';
            add_menu_page(
                'Brave Email SMTP',     // Page title
                'Brave Email SMTP',     // Menu title
                $menuAccessCapability,  // Capability required to access
                'brave-email-smtp',     // Menu slug (should be unique)
                array(&$this, 'brave_email_smtp_page_callback'),
                'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTMiIHZpZXdCb3g9IjAgMCAyMCAxMyIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTUuODgyMTEgMTEuMzI4NkM2LjAxMzM2IDExLjI1ODggNi4xNjAxMiAxMS4yMjIyIDYuMzA5MjIgMTEuMjIyMkwxMy42OTA4IDExLjIyMjJDMTMuODM5OSAxMS4yMjIyIDEzLjk4NjYgMTEuMjU4OCAxNC4xMTc5IDExLjMyODZDMTQuOTQxMiAxMS43NjY2IDE0LjYyNiAxMyAxMy42OTA4IDEzTDYuMzA5MjEgMTNDNS4zNzQwMSAxMyA1LjA1ODgzIDExLjc2NjYgNS44ODIxMSAxMS4zMjg2WiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTMuMTMzNTQgOC4yMTc0OUMzLjI2MjYzIDguMTQ3NjcgMy40MDY5OCA4LjExMTExIDMuNTUzNjIgOC4xMTExMUwxNi40NDY0IDguMTExMTFDMTYuNTkzIDguMTExMTEgMTYuNzM3NCA4LjE0NzY3IDE2Ljg2NjUgOC4yMTc0OUMxNy42NzYyIDguNjU1NSAxNy4zNjYyIDkuODg4ODkgMTYuNDQ2NCA5Ljg4ODg5TDMuNTUzNjIgOS44ODg4OUMyLjYzMzc5IDkuODg4ODkgMi4zMjM4IDguNjU1NSAzLjEzMzU0IDguMjE3NDlaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMi4yNjMzNCAwLjUzNjY5M0MyLjEyMTQyIDAuNjY4NzY5IDEuOTk5MDIgMC44MjQwMzMgMS45MDIzNSAwLjk5ODgyM0wwLjIzNTM4MiA0LjAxMjg3Qy0wLjQ1MTQ3OCA1LjI1NDc4IDAuNDQ3MTA2IDYuNzc3NzggMS44NjY3MSA2Ljc3Nzc4TDE4LjEzMzMgNi43Nzc3OEMxOS41NTI5IDYuNzc3NzggMjAuNDUxNSA1LjI1NDc4IDE5Ljc2NDYgNC4wMTI4N0wxOC4wOTc2IDAuOTk4ODIyQzE3Ljk5MjMgMC44MDgzNTQgMTcuODU2NCAwLjY0MTA3MiAxNy42OTggMC41MDE3MTRDMTYuMDk1OSAxLjUwNTg5IDEyLjA5MTQgMy44NzM3NyA5Ljk1MjcyIDMuODczNzdDNy44Mzg0OSAzLjg3Mzc3IDMuOTAwODcgMS41NTk3MyAyLjI2MzM0IDAuNTM2NjkzWiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTIuOTYwNjMgMC4xMjcyMzNDNC43MjU2NiAxLjE5ODU5IDguMDk5MzEgMy4wODY3NyA5Ljk1MjcyIDMuMDg2NzdDMTEuODE3MiAzLjA4Njc3IDE1LjIyMDIgMS4xNzU5MiAxNi45NzYzIDAuMTA4MDlDMTYuODEyNyAwLjA2MTU0MjMgMTYuNjQxMyAwLjAzNzAzOSAxNi40NjYzIDAuMDM3MDM5TDMuNTMzNjggMC4wMzcwMzc4QzMuMzM2MTIgMC4wMzcwMzc5IDMuMTQzMSAwLjA2ODI4MjcgMi45NjA2MyAwLjEyNzIzM1oiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=',
				40,
                
            );
            
            // Add a submenu item under the custom top-level menu
            add_submenu_page(
                'brave-email-smtp',      // Parent menu slug
                'Mail Template',          // Page title
                'Mail Template',          // Menu title
                $menuAccessCapability,   // Capability required to access
                'mails',   // Menu slug (should be unique)
                array(&$this, 'brave_email_mail_template_callback') // Callback function to display the page content
            );
    }

    public function brave_email_mail_template_callback() {
        echo '<script>
            window.location.href = "' . admin_url('edit.php?post_type=mails') . '";
        </script>';
    }
    public function brave_email_smtp_page_callback() {
        // echo "fads";exit;
        if (isset($_GET['page']) && !isset($_GET['provider'])) {
            include('includes/setup.php');
        }
        else
        {
            if (isset($_GET['page']) && isset($_GET['provider'])) {
                if(isset($_GET['tabview']) && $_GET['provider'] == "google"){
                    if($_GET['tabview'] == ''){
                        include('includes/google/setup_google.php');
                        
                    }
                    elseif($_GET['tabview'] == 'gjson'){
                        include('includes/google/update_google_secret.php');
                    }

                    elseif($_GET['tabview'] == 'usage'){
                        include('includes/google/usage_smtp_mail.php');
                    }
                    elseif($_GET['tabview'] == 'test-email'){
                        include('includes/google/test_mail.php');
                    }
                    
                }

                if(isset($_GET['tabview']) && $_GET['provider'] == "smtp"){
                    if($_GET['tabview'] == 'settings'){
                        include('includes/smtp/smtp_settings.php');
                        
                    }
                    elseif($_GET['tabview'] == 'usage'){
                        include('includes/smtp/usage_smtp_mail.php');
                    }
                    elseif($_GET['tabview'] == 'test-email'){
                        include('includes/smtp/test_mail.php');
                    }
                }
                
                
            }
        }
        
    }

    public function to_email($emailConfig){
        $config = include(__DIR__ . DIRECTORY_SEPARATOR . 'includes/config.php');
        $result = false;
        if($config['active_provider'] == "google"){
            $result = $this->google_mail($config,$emailConfig);
        }

        if($config['active_provider'] == "smtp"){
            $result = $this->smtp_mail($config,$emailConfig);
        }

        return $result;
        
    }

    public function smtp_mail($config,$emailConfig){
        
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes/smtp/PHPMailer/autoload.php';
        $mail = new PHPMailer(true);;
        ;

        // Set up your SMTP settings
        $mail->isSMTP();
        $mail->Host = $config['smtp_settings']['host']; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_settings']['username']; // Replace with your SMTP username
        $mail->Password = $config['smtp_settings']['password']; // Replace with your SMTP password
        $mail->SMTPSecure = $config['smtp_settings']['encryptionType']; // Use 'ssl' if required by your SMTP provider
        $mail->Port = $config['smtp_settings']['port']; // Replace with your SMTP port number
        // $mail->Port = 587;
        // $mail->SMTPSecure = 'tls';


        $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']); // Replace with your email address and name

        $mail->addAddress($emailConfig['to_email']);
        $mail->isHTML(true);  
        $mail->Subject = $emailConfig['subject'];
        $mail->Body = $emailConfig['message'];
        
        // $mail->AltBody = strip_tags($message); // Optional: plain text version of the message
        // echo $emailConfig['message'];exit;
        if (!$mail->send()) {
            return false; // Email sending failed
        } else {
            return true; // Email sent successfully
        }


    }

    public function google_mail($config,$emailConfig){
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes/google/vendor/autoload.php';
        
        $client = new Google_Client();
        $client->setApplicationName('BraveEmail');
        $client->addScope(Google_Service_Gmail::GMAIL_SEND);
        $client->setAuthConfig(__DIR__ . DIRECTORY_SEPARATOR. 'includes/'.$config['client_secret']);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR .'includes/google/uploads/token.json')) {
            $accessToken = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'includes/google/uploads/token.json'), true);
            $client->setAccessToken($accessToken);
        }
        //echo $currentURL; 
        //  exit; 
        $client->setRedirectUri($config['redirect_url']);
        $service = new Google_Service_Gmail($client);
        $message = new Google_Service_Gmail_Message();
        $rawMessage = "From: mail.sanjeetchahal@gmail.com\r\n";
        $rawMessage .= "To: ".$emailConfig['to_email']."\r\n";

        // Pick email template from email.html file and replace the placeholders with actual values {name},{email},{backup-time},{backup-location},{backup-size}, and extract subject and body
        
        $subject = 'Brave Email';
        if($emailConfig['htmlPath'] == true)
        {
            $emailTemplate = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'includes/email.html');
            // Get subject in subject tag
            
            if (preg_match('/<subject>(.*?)<\/subject>/s', $emailTemplate, $matches)) {
                $subject = $matches[1];
            }
            // Remove subject; the rest of the email template is the body
            $emailTemplate = preg_replace('/<subject>(.*?)<\/subject>/s', '', $emailTemplate);
            // Replace placeholders with actual values
            

            $emailTemplate = str_replace('{email}', $emailConfig['to_email'], $emailTemplate);
            
            // Replace body in email template
            $body = $emailTemplate;
        }
        elseif($emailConfig['htmlPath'] != true && $emailConfig['htmlPath'] != "")
        {
        
        }
        elseif($emailConfig['message']!=""){
            $body = $emailConfig['message'];
        }

        if($emailConfig['subject']){
            $subject = $emailConfig['subject'];
        }
        

        // Add subject and body to raw message
        $rawMessage .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
        $rawMessage .= "MIME-Version: 1.0\r\n";
        $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessage .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $rawMessage .= chunk_split(base64_encode($body));

        // Encode the message
        $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
        $message->setRaw($encodedMessage);

        try {
            // Send the message
            $service->users_messages->send('me', $message);
           return true;
        } catch (Google_Service_Exception $e) {
            echo "Error sending notification to $email: " . $e->getMessage() . "<br>";
            return false;
        } catch (Google_Exception $e) {
            echo "Error sending notification to $email: " . $e->getMessage() . "<br>";
            return false;
        }
    }
    
}

