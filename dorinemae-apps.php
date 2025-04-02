<?php
/**
 * Plugin Name: DorineMae Apps
 * Plugin URI: https://github.com/dorinemae/dorinemae-apps
 * Description: A lightweight Elementor plugin that streamlines cache management and enables automatic expiration of sections or widgets for better site performance.
 * Version: 1.4
 * Author: Dorine Mae
 * Author URI: http://dorinemae.com
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin version constant.
define( 'DM_PLUGIN_VERSION', '1.4' );

// Include the settings page.
require_once plugin_dir_path( __FILE__ ) . 'settings.php';

/**
 * Enqueue plugin scripts and styles.
 */
function dm_enqueue_scripts() {
    // Load jQuery hoverIntent. Try to load the local file; otherwise, fall back to a CDN.
    $hoverIntentPath = plugin_dir_path( __FILE__ ) . 'js/jquery.hoverIntent.min.js';
    if ( file_exists( $hoverIntentPath ) ) {
        $hoverIntentUrl = plugins_url( 'js/jquery.hoverIntent.min.js', __FILE__ );
    } else {
        $hoverIntentUrl = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.hoverintent/1.10.1/jquery.hoverIntent.min.js';
    }
    wp_register_script(
        'jquery-hoverIntent',
        $hoverIntentUrl,
        array( 'jquery' ),
        '1.10.1',
        true
    );
    wp_enqueue_script( 'jquery-hoverIntent' );

    // Enqueue Auto-Expire script.
    wp_enqueue_script(
        'dm-auto-expire',
        plugins_url( 'js/auto-expire.js', __FILE__ ),
        array( 'jquery', 'jquery-hoverIntent' ),
        DM_PLUGIN_VERSION,
        true
    );
    // Localize auto-expire settings.
    $auto_expire_settings = get_option( 'dm_auto_expire', array( 'entries' => array() ) );
    wp_localize_script( 'dm-auto-expire', 'dm_auto_expire_settings', $auto_expire_settings );

    // Enqueue Cache & Sync script.
    wp_enqueue_script(
        'dm-cache-sync',
        plugins_url( 'js/cache-sync.js', __FILE__ ),
        array( 'jquery', 'jquery-hoverIntent' ),
        DM_PLUGIN_VERSION,
        true
    );
    wp_localize_script(
        'dm-cache-sync',
        'dm_ajax',
        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
    );

    // Enqueue plugin stylesheet.
    wp_enqueue_style(
        'dm-plugin-style',
        plugins_url( 'style.css', __FILE__ ),
        array(),
        DM_PLUGIN_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'dm_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'dm_enqueue_scripts' );

/**
 * AJAX action handler for Cache & Sync.
 */
function dm_cache_sync_handler() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Insufficient permissions.' );
    }
    $enable = isset( $_POST['enable'] ) && $_POST['enable'] === 'true';
    if ( $enable ) {
        update_option( 'dm_cache_sync', 'enabled' );
        wp_send_json_success( 'Elementor Cache & Sync Enabled' );
    } else {
        update_option( 'dm_cache_sync', 'disabled' );
        wp_send_json_success( 'Elementor Cache & Sync Disabled' );
    }
}
add_action( 'wp_ajax_dm_cache_sync', 'dm_cache_sync_handler' );

/**
 * Add "CleanUp Elementor" to the admin bar when Cache & Sync is enabled.
 */
function dm_admin_bar_cleanup( $wp_admin_bar ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $cache_sync_status = get_option( 'dm_cache_sync', 'disabled' );
    if ( 'enabled' === $cache_sync_status ) {
        $wp_admin_bar->add_node( array(
            'id'    => 'cleanup-elementor',
            'title' => 'CleanUp Elementor',
            'href'  => '#'  // Using a hash to prevent navigation.
        ) );
    }
}
add_action( 'admin_bar_menu', 'dm_admin_bar_cleanup', 100 );

/**
 * AJAX action handler for Elementor cleanup.
 * (You can add any server-side cleanup code here if needed.)
 */
function dm_cleanup_elementor_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( "Not allowed" );
    }
    // (Optional) Add server-side cleanup tasks here.
    wp_send_json_success( "Cleanup complete" );
}
add_action( 'wp_ajax_dm_cleanup_elementor', 'dm_cleanup_elementor_ajax' );

/**
 * Inline script in the admin footer to handle the "CleanUp Elementor" click.
 * Updates spinner classes in sequence and shows a centered popup.
 */
function dm_cleanup_inline_script() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Bind click handler on the admin bar node ("CleanUp Elementor").
        $('#wp-admin-bar-cleanup-elementor').on('click', function(e) {
            e.preventDefault();
            console.log("Cleanup Elementor button clicked.");
            
            // Identify spinner elements.
            var $spinners = $(".elementor-button-spinner");
            if ($spinners.length) {
                // Reset spinner classes to default, then add the "loading" state.
                $spinners.removeClass("loading success").addClass("loading");
            }
            
            // Simulate clicks on Elementor's Clear Cache and Library Sync buttons, if they exist.
            $("#elementor-clear-cache-button").trigger("click");
            $("#elementor-library-sync-button").trigger("click");
            
            // Perform AJAX cleanup action.
            $.ajax({
                url: ajaxurl,
                method: "POST",
                data: { action: "dm_cleanup_elementor" },
                success: function(response) {
                    if (response.success) {
                        // Update spinner elements to show "success" after cleanup.
                        if ($spinners.length) {
                            $spinners.removeClass("loading").addClass("success");
                        }
                        // Create a centered popup message.
                        var popup = $('<div class="cleanup-popup">Elementor cache cleared and library synced successfully.</div>');
                        popup.css({
                            position: 'fixed',
                            top: '50%',
                            left: '50%',
                            transform: 'translate(-50%, -50%)',
                            background: '#46b450',
                            color: '#fff',
                            padding: '20px 30px',
                            'border-radius': '5px',
                            'z-index': 9999,
                            display: 'none',
                            'font-size': '16px'
                        });
                        $('body').append(popup);
                        popup.fadeIn(500).delay(2000).fadeOut(500, function() {
                            $(this).remove();
                            // Refresh the page 1 second after the popup disappears.
                            setTimeout(function(){
                                location.reload();
                            }, 1000);
                        });
                    } else {
                        console.error("Cleanup failed:", response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error:", textStatus, errorThrown);
                }
            });
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'dm_cleanup_inline_script');

/**
 * Customize the Plugins page action links.
 * Only the "Settings" link is retained.
 */
function dm_plugin_action_links( $links ) {
    $settings_link = '<a href="' . admin_url( 'options-general.php?page=dm-plugin-settings' ) . '">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dm_plugin_action_links' );
?>
