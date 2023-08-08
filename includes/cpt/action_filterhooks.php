<?php
echo "Override the default mail sending system using PHPMailer";
// require_once __DIR__.'/vendor/autoload.php';
// use PHPMailer\PHPMailer\PHPMailer;
add_action( 'wp_mail_failed', function ( $error ) {
    // the "3" means write the message to the file as defined in the third parameter
    error_log( $error->get_error_message(), 3, WP_CONTENT_DIR . '/debug.log' );
} );


// add_action('phpmailer_init', 'custom_registration_email');

// function custom_registration_email($phpmailer) {
//     echo "<pre>";print_r($phpmailer);
//     exit;
// }

add_action('phpmailer_init', function ($phpmailer) {
    echo "<pre>";print_r($phpmailer);
    exit;
 });
 // Hook into the wp_mail function and replace it with custom_wp_mail
//  add_action( 'phpmailer_init', 'custom_phpmailer_init_test' );

//  function custom_phpmailer_init_test( $phpmailer ) {
//     // Only apply this customization if the Mailgun settings are in use
//     echo $uploadDir = __DIR__ . '/uploads/';
//     $appendVar = fopen($uploadDir.'/append.php','a');

//     // // writing new lines to the file
//     $wit = fwrite($appendVar,$phpmailer->get_recipients()." | ");
    

//     // // Closing the file
    
//     fclose($appendVar);
//     exit;
//     echo "<pre>";print_r($phpmailer);
//     $emailConfig = array();
//     $emailConfig['subject'] = "Test";
//     $emailConfig['message'] = "This is for testing";
//     $emailConfig['htmlPath'] = false;
//     $emailConfig['to_email'] = "webfortweb@mailinator.com";
//     $send_ob = new braveEmail();
//     $send_ob->to_email($emailConfig);  
//     if($result){
//         return '<p class="text-green-600 text-center pt-2">Successfully Sent!.</p>';
//     } 
//     else
//     {
//         return '<p class="text-green-600 text-center pt-2">Opps!.</p>';
//     }
 
//     return $phpmailer;
// }

?>