<?php
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
    }

    public function email_submenu_page()
    {
        
        add_submenu_page(
            'options-general.php', // Parent slug jis bhi page ka submenu banan na us page par click kar ke last path copy kar lo edit.php?post_type=spaces
            $this->docMenuName, // Page title
            $this->docMenuName, // Menu title
            'administrator', // Capability required
            'brave_email_smtp', // Menu slug
            array(&$this, 'brave_email_smtp_page_callback') // Callback function
          );
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
                
                
            }
        }
        
    }

    public function to_email($emailConfig){
        $config = include(__DIR__ . DIRECTORY_SEPARATOR . 'includes/config.php');
        $result = false;
        if($config['active_provider'] == "google"){
            $result = $this->google_mail($config,$emailConfig);
        }

        return $result;
        
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

