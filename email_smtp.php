<?php

/**
 * Brave Email Smtp
 *
 * @package       Brave Email Smtp
 * @author        Sanjeet Chahal
 * @version       2.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   BraveEmailSmtp
 * Description:   Brave Email SMTP for Gmail
 * version:       2.0.0
 * Author:        Sanjeet chahal
 * Text Domain:   BraveEmailSmtp
 * Domain Path:   /languages
 *
 *
 */
defined( 'ABSPATH' ) || exit;
if( ! class_exists( 'BraveEmailSmtp' ) ) {

 class BraveEmailSmtp{

     public $plugin_slug;
     public $version;
     public $cache_key;
     public $cache_allowed;

     public function __construct() {

         $this->plugin_slug = plugin_basename( __DIR__ );
         $this->version = '1.0';
         $this->cache_key = 'brave_email_library';
         $this->cache_allowed = false;

         add_filter( 'plugins_api', array( $this, 'info' ), 20, 3 );
         add_filter( 'site_transient_update_plugins', array( $this, 'update' ) );
         add_action( 'upgrader_process_complete', array( $this, 'purge' ), 10, 2 );

     }

     public function request(){

         $remote = get_transient( $this->cache_key );

         if( false === $remote || ! $this->cache_allowed ) {



             $remote = wp_remote_get(
                 "https://raw.githubusercontent.com/sanjeetchahal/BraveEmail/main/info.json",
                 array(
                     'timeout' => 10,
                     'headers' => array(
                         'Accept' => 'application/json',


                     )
                 )
             );
             if(
                 is_wp_error( $remote )
                 || 200 !== wp_remote_retrieve_response_code( $remote )
                 || empty( wp_remote_retrieve_body( $remote ) )
             ) {
                 return false;
             }

             set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );

         }

         $remote = json_decode( wp_remote_retrieve_body( $remote ) );

         return $remote;


     }


     function info( $res, $action, $args ) {


         // do nothing if you're not getting plugin information right now
         if( 'plugin_information' !== $action ) {
             return $res;
         }

         // do nothing if it is not our plugin
         if( $this->plugin_slug !== $args->slug ) {
             return $res;
         }

         // get updates
         $remote = $this->request();

         if( ! $remote ) {
             return $res;
         }

         $res = new stdClass();

         $res->name = $remote->name;
         $res->slug = $remote->slug;
         $res->version = $remote->version;
         $res->tested = $remote->tested;
         $res->requires = $remote->requires;
         if(isset($remote->author)){
            $res->author = $remote->author;
         }
         if(isset($remote->author)){
            $res->author_profile = $remote->author_profile;
         }
         
         
         $res->download_link = $remote->download_url;
         $res->trunk = $remote->download_url;
         $res->requires_php = $remote->requires_php;
         $res->last_updated = $remote->last_updated;

         $res->sections = array(
             'description' => $remote->sections->description,
             'installation' => $remote->sections->installation,
             'changelog' => $remote->sections->changelog
         );

         if( ! empty( $remote->banners ) ) {
             $res->banners = array(
                 'low' => $remote->banners->low,
                 'high' => $remote->banners->high
             );
         }

         return $res;

     }

     public function update( $transient ) {

         if ( empty($transient->checked ) ) {
             return $transient;
         }

         $remote = $this->request();

         if(
             $remote
             && version_compare( $this->version, $remote->version, '<' )
             && version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' )
             && version_compare( $remote->requires_php, PHP_VERSION, '<' )
         ) {
             $res = new stdClass();
             $res->slug = $this->plugin_slug;
             $res->plugin = plugin_basename( __FILE__ );
             $res->new_version = $remote->version;
             $res->tested = $remote->tested;
             $res->package = $remote->download_url;

             $transient->response[ $res->plugin ] = $res;

     }

         return $transient;

     }

     public function purge( $upgrader, $options ){

         if (
             $this->cache_allowed
             && 'update' === $options['action']
             && 'plugin' === $options[ 'type' ]
         ) {
             // just clean the cache when new plugin version is installed
             delete_transient( $this->cache_key );
         }

     }


 }

 new BraveEmailSmtp();

}
register_activation_hook( __FILE__,     'email_smtp_activation' );
register_deactivation_hook( __FILE__,   'email_smtp_deactivation' );

function email_smtp_activation()
{
    add_option('new_registration_client_template', '');
    add_option('new_registration_client_template_subject', 'Login Details');
    add_option('new_registration_client_template_formatting', 'plain');
    add_option('new_registration_client_template_notification', 'disabled');
    
    add_option('new_registration_admin_template', '');
    add_option('new_registration_admin_template_subject', 'New User Registration');
    add_option('new_registration_admin_template_formatting', 'plain');
    add_option('new_registration_admin_template_notification', 'disabled');

    add_option('forgot_password_template', '');
    add_option('forgot_password_template_subject', 'Password Reset');
    add_option('forgot_password_template_formatting', 'plain');
    add_option('forgot_password_template_notification', 'disabled');
}

function email_smtp_deactivation()
{
    delete_option('new_registration_client_template');
    delete_option('new_registration_client_template_subject');
    delete_option('new_registration_client_template_formatting');
    delete_option('new_registration_client_template_notification');
    
    delete_option('new_registration_admin_template');
    delete_option('new_registration_admin_template_subject');
    delete_option('new_registration_admin_template_formatting');
    delete_option('new_registration_admin_template_notification');

    delete_option('forgot_password_template');
    delete_option('forgot_password_template_subject');
    delete_option('forgot_password_template_formatting');
    delete_option('forgot_password_template_notification');
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_custom_plugin_document_links');
function my_custom_plugin_document_links($links) {
    // Check if the plugin is active
    // echo "ddds";
    // exit;
        if (is_plugin_active(plugin_basename(__FILE__))) {
            $settingsLink = '<a href="' . esc_url(admin_url('admin.php?page=brave-email-smtp')) . '">Settings</a>';
            $mailTemplate = '<a href="' . esc_url(admin_url('admin.php?page=mail-templates')) . '">Mail Templates</a>';
            // $mailTemplate = '<a href="' . esc_url(admin_url('edit.php?post_type=mails')) . '">Mail Templates</a>';
            array_push($links, $settingsLink);
            array_push($links, $mailTemplate);
        }
    return $links;
}

// add_action( 'admin_menu', 'add_admin_options_page');

// function add_admin_options_page() {
    
	

			
		

		

		
// 	}

include("brave_email_smtp.php");

//  CPT include
// include("includes/cpt/mail.php");
// include("includes/cpt/mail_meta.php");
// include("includes/cpt/mail_register_rest_field.php");


$braveEmailObj = new braveEmail();
$braveEmailObj->setDocumentationMenu("Brave Email SMTP");
$braveEmailObj->doSetup();