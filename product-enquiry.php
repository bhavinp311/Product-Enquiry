<?php
/**
 * Plugin Name: Product Enquiry WooCommerce Addon
 * Description: Product Enquiry WooCommerce Addon
 * Plugin URI: ''
 * Author: Bhavin Patel
 * Version: 1.0
 * Author URI: https://profiles.wordpress.org/bhavinp311/
 *
 * Text Domain: product_enquiry_woocommerce_addon
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Define Plugin URL and Directory Path
 */
define('PEWA_URL', plugins_url('/', __FILE__));  // Define Plugin URL
define('PEWA_PATH', plugin_dir_path(__FILE__));  // Define Plugin Directory Path
define('PEWA_TEXTDOMAIN', 'product_enquiry_woocommerce_addon');

/**
 * Main plugin class.
 * 
 * @access public
 * @since  1.0
 */
if (!class_exists('product_enquiry_woocommerce_addon')) :

    class product_enquiry_woocommerce_addon {

        /**
         * Main constructor.
         * 
         * @access public
         * @since  1.0
         */
        public function __construct() {
            $this->hooks();
        }

        /**
         * Initialize
         */
        public function hooks() {
            add_action('wp_enqueue_scripts', array($this, 'pewa_script_register'));
            add_action('plugins_loaded', array($this, 'pewa_plugin_load'));
            add_action('admin_menu', array($this, 'pewa_add_menu_page'), 99);
            require_once PEWA_PATH . 'inc/pewa-front.php';
        }

        /**
         * Load scripts and styles
         */
        public function pewa_script_register() {
            wp_enqueue_style('pewa-style', PEWA_URL . 'assets/css/pewa-style.css');
            wp_enqueue_script('pewa-script', PEWA_URL . 'assets/js/pewa-script.js', array('jquery'), time(), '', true);
        }

        /**
         * Add page to admin menu
         */
        public function pewa_add_menu_page() {
            add_submenu_page(
                    'options-general.php',
                    esc_html__('Product Enquiry', PEWA_TEXTDOMAIN),
                    esc_html__('Product Enquiry', PEWA_TEXTDOMAIN),
                    'manage_woocommerce',
                    'pewa-option',
                    array($this, 'pewa_settings_page'));
        }

        public function pewa_settings_page() {
            require_once PEWA_PATH . 'admin/pewa-addon.php';
        }

        /*
         * Check for WooCommerce
         */

        public function pewa_plugin_load() {
            // Load plugin textdomain
            load_plugin_textdomain('PEWA_TEXTDOMAIN');
            if (!class_exists('WooCommerce')) {
                add_action('admin_notices', array($this, 'pewa_widget_fail_load'));
                return;
            }
        }

        /**
         * This notice will appear if Elementor is not installed or activated or both
         */
        public function pewa_widget_fail_load() {
            $screen = get_current_screen();
            if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
                return;
            }

            $plugin = 'woocommerce/woocommerce.php';

            if (!is_plugin_active($plugin)) {
                if (!current_user_can('activate_plugins')) {
                    return;
                }
                $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
                $message = '<p><strong>' . __('Product Enquiry WooCommerce Addon  plugin is not working because you need to activate the WooCommerce plugin.', PEWA_TEXTDOMAIN) . '</strong></p>';
                $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $activation_url, __('Activate WooCommerce Now', PEWA_TEXTDOMAIN)) . '</p>';
            } else {
                if (!current_user_can('install_plugins')) {
                    return;
                }
                $install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=woocommerce'), 'install-plugin_woocommerce');
                $message = '<p><strong>' . __('Product Enquiry WooCommerce Addon  plugin is not working because you need to install the WooCommerce plugin', PEWA_TEXTDOMAIN) . '</strong></p>';
                $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $install_url, __('Install WooCommerce Now', PEWA_TEXTDOMAIN)) . '</p>';
            }
            echo '<div class="error"><p>' . $message . '</p></div>';
        }

        /**
         * Display admin notice for Elementor update if Elementor version is old
         */
        public function pewa_update_notice() {
            if (!current_user_can('update_plugins')) {
                return;
            }

            $file_path = 'elementor/elementor.php';
            $upgrade_link = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file_path, 'upgrade-plugin_' . $file_path);
            $message = '<p><strong>' . __('Product Enquiry WooCommerce Addon ', PEWA_TEXTDOMAIN) . '</strong>' . __(' not working because you are using an old version of Elementor.', PEWA_TEXTDOMAIN) . '</p>';
            $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', $upgrade_link, __('Update Elementor Now', PEWA_TEXTDOMAIN)) . '</p>';
            echo '<div class="error">' . $message . '</div>';
        }

        /**
         * Add to cart product programmatically.
         */
        public function add_to_cart_handler() {
            global $woocommerce;

            $product_id = isset($_POST['product_id']) ? sanitize_text_field($_POST['product_id']) : '';
            $variation_id = isset($_POST['variation_id']) ? sanitize_text_field($_POST['variation_id']) : '';
            $quantity = 1;

            if (!empty($variation_id)) {
                WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
            } else {
                WC()->cart->add_to_cart($product_id, $quantity);
            }
            echo wc_get_cart_url();
            wp_die();
        }

    }

    endif;

/**
 * Initialize Plugin Class
 *
 * @access public
 * @since  1.0
 */
new product_enquiry_woocommerce_addon();
