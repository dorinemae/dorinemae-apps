<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add a settings submenu under Options.
 */
function dm_add_settings_page() {
    add_options_page(
        'DorineMae Apps Plugin Settings',
        'DorineMae Apps',
        'manage_options',
        'dm-plugin-settings',
        'dm_render_settings_page'
    );
}
add_action( 'admin_menu', 'dm_add_settings_page' );

/**
 * Render the settings page.
 */
function dm_render_settings_page() {
    // Retrieve saved options.
    $cache_sync_value     = get_option( 'dm_cache_sync', 'disabled' );
    $auto_expire_settings = get_option( 'dm_auto_expire', array( 'entries' => array() ) );
    ?>
    <div class="wrap">
        <h1>DorineMae Apps Settings (v<?php echo DM_PLUGIN_VERSION; ?>)</h1>
        
        <!-- Cache & Sync Settings -->
        <h2>Enable Elementor Cache & Sync</h2>
        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            <?php wp_nonce_field( 'dm_update_cache_sync' ); ?>
            <input type="hidden" name="action" value="dm_update_cache_sync">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Cache & Sync Status</th>
                    <td>
                        <select name="cache_sync_status">
                            <option value="enabled" <?php selected( $cache_sync_value, 'enabled' ); ?>>Enabled</option>
                            <option value="disabled" <?php selected( $cache_sync_value, 'disabled' ); ?>>Disabled</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button( 'Save Cache & Sync Settings' ); ?>
        </form>

        <!-- Auto-Expire Settings -->
        <h2>Auto-Expire Sections</h2>
        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            <?php wp_nonce_field( 'dm_update_auto_expire' ); ?>
            <input type="hidden" name="action" value="dm_update_auto_expire">
            <table class="form-table">
                <thead>
                    <tr>
                        <th>CSS Class</th>
                        <th>Expiry Date/Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="auto-expire-table-body">
                    <?php 
                    if ( isset( $auto_expire_settings['entries'] ) && is_array( $auto_expire_settings['entries'] ) ) {
                        foreach ( $auto_expire_settings['entries'] as $entry ) {
                            $css_class   = isset( $entry['css_class'] ) ? $entry['css_class'] : '';
                            $expiry_time = isset( $entry['expiry_time'] ) ? $entry['expiry_time'] : '';
                            echo '<tr>';
                            echo '<td><input type="text" name="auto_expire[css_class][]" value="' . esc_attr( $css_class ) . '" placeholder="CSS Class" /></td>';
                            echo '<td><input type="datetime-local" name="auto_expire[expiry_time][]" value="' . esc_attr( $expiry_time ) . '" /></td>';
                            echo '<td><button class="button remove-expire-row">Remove</button></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
            <button class="button" type="button" id="add-expire-row">Add New Row</button>
            <?php submit_button( 'Save Auto-Expire Settings' ); ?>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#add-expire-row').on('click', function(e) {
            e.preventDefault();
            var newRow = '<tr>' +
                         '<td><input type="text" name="auto_expire[css_class][]" value="" placeholder="CSS Class" /></td>' +
                         '<td><input type="datetime-local" name="auto_expire[expiry_time][]" value="" /></td>' +
                         '<td><button class="button remove-expire-row">Remove</button></td>' +
                         '</tr>';
            $('#auto-expire-table-body').append(newRow);
        });
        $(document).on('click', '.remove-expire-row', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });
    });
    </script>
    <?php
}

/**
 * Process Cache & Sync settings.
 */
function dm_update_cache_sync() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Not allowed' );
    }
    check_admin_referer( 'dm_update_cache_sync' );
    if ( isset( $_POST['cache_sync_status'] ) && in_array( $_POST['cache_sync_status'], array( 'enabled', 'disabled' ) ) ) {
        update_option( 'dm_cache_sync', sanitize_text_field( $_POST['cache_sync_status'] ) );
        wp_redirect( add_query_arg( 'updated', 'true', wp_get_referer() ) );
        exit;
    }
}
add_action( 'admin_post_dm_update_cache_sync', 'dm_update_cache_sync' );

/**
 * Process Auto-Expire settings.
 */
function dm_update_auto_expire() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Not allowed' );
    }
    check_admin_referer( 'dm_update_auto_expire' );
    $css_classes = isset( $_POST['auto_expire']['css_class'] ) ? $_POST['auto_expire']['css_class'] : array();
    $expiry_times = isset( $_POST['auto_expire']['expiry_time'] ) ? $_POST['auto_expire']['expiry_time'] : array();
    $entries = array();
    foreach ( $css_classes as $index => $css_class ) {
        if ( ! empty( $css_class ) && isset( $expiry_times[ $index ] ) && ! empty( $expiry_times[ $index ] ) ) {
            $entries[] = array(
                'css_class'   => sanitize_text_field( $css_class ),
                'expiry_time' => sanitize_text_field( $expiry_times[ $index ] ),
            );
        }
    }
    update_option( 'dm_auto_expire', array( 'entries' => $entries ) );
    wp_redirect( add_query_arg( 'updated', 'true', wp_get_referer() ) );
    exit;
}
add_action( 'admin_post_dm_update_auto_expire', 'dm_update_auto_expire' );
?>
