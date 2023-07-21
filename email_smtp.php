<?php

/**
 * Brave Email Smtp
 *
 * @package       Brave Email Smtp
 * @author        Sanjeet Chahal
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   BraveEmailSmtp
 * Description:   Brave Email SMTP for Gmail
 * Version:       1.0.0
 * Author:        Sanjeet chahal
 * Text Domain:   BraveEmailSmtp
 * Domain Path:   /languages
 * 
 * 
 */

register_activation_hook( __FILE__,     'email_smtp_activation' );
register_deactivation_hook( __FILE__,   'email_smtp_deactivation' );

function email_smtp_activation()
{
       
}

function email_smtp_deactivation()
{
	    
}

include("brave_email_smtp.php");

$v = new braveEmail();
$v->setDocumentationMenu("Brave Email SMTP");
$v->doSetup();