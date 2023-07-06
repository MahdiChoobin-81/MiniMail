<?php
/*

Plugin Name: MiniMail
Description: Send MMail to thousands of your users.
Version: 0.0.1
Author: Mahdi Choobin
Author URI: https://github.com/Mahdi-Choobin

*/


defined('ABSPATH') || exit('No Access');

class MMailMarketing
{

    public function __construct()
    {

        $this->define_constants();
        require_once MM_DIR . 'vendor/autoload.php';
        register_activation_hook(__FILE__, array($this, 'MM_RG_activate'));

        if (is_admin()) {
            add_action('admin_menu', array($this, 'MM_admin_menu'));
        }

    }

    public function MM_admin_menu()
    {
        add_menu_page(
            'MiniMail',
            'MiniMail',
            'manage_options',
            'mini_mail',
            array($this, 'MM_main_page'),
            'dashicons-email-alt',
            6
        );

        add_submenu_page(
            'mini_mail',
            'Configuration',
            'Configuration',
            'manage_options',
            'config',
            array($this, 'MM_configuration_settings')
        );

    }


    public function MM_configuration_settings()
    {
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {

            echo
            '<div class="notice notice-info  is-dismissible">
                        <p>This plugin only works on a real web server.☁</p>
                     </div>';
            exit;
        }
        if(isset($_POST['submit'])){
            new StoreConfiguration();
        }

        include MM_TPL . 'settings.php';
    }

    public function MM_main_page()
    {

        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
            echo
            '<div class="notice notice-info  is-dismissible">
                        <p>This plugin only works on a real web server.☁</p>
                     </div>';
            exit;
        }
        $config_path = MM_CRON_JOBS_DATA . 'config.json';
        if(!file_exists($config_path)){
            echo
            '<div class="notice notice-info  is-dismissible">
                        <p>Please first Fill Configuration tab Fields.</p>
                     </div>';
            exit;
        }

        $config_string = file_get_contents($config_path);
        $config_data= json_decode($config_string, true);

        if($_SESSION['confirm_info'] == false){
           echo
            '<div class="notice notice-warning  is-dismissible">
                        <p>Please first make sure that your information is correct in the Configuration section.</p>
                     </div>';
            exit();
        }

        if(isset($_POST['submit']))
            new SendEmail();


        include MM_TPL . 'send_mail_page.php';
    }

    public function MM_RG_activate()
    {

    }

    private function define_constants()
    {

        define('MM_DIR', trailingslashit(plugin_dir_path(__FILE__)));
        define('MM_URL', trailingslashit(plugin_dir_url(__FILE__)));
        define('MM_INC', trailingslashit(MM_DIR . 'inc'));
        define('MM_CSS', trailingslashit(MM_URL . 'assets/css'));
        define('MM_JS', trailingslashit(MM_URL . 'js'));
        define('MM_IMAGES', trailingslashit(MM_URL . 'images'));
        define('MM_LIBS', trailingslashit(MM_DIR . 'libs'));
        define('MM_TPL', trailingslashit(MM_DIR . 'tpl'));
        define('MM_CLASSES', trailingslashit(MM_DIR . 'classes'));
        define('MM_CRON_JOBS', trailingslashit(MM_DIR . 'cron-jobs'));
        define('MM_CRON_JOBS_DATA', trailingslashit(MM_CRON_JOBS . 'data'));



    }
}

new MMailMarketing();

function register_my_session()
{
    if( !session_id() )
    {
        session_start();
    }
}


add_action('init', 'register_my_session');

