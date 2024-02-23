<?php
/**
 * Settings page
 *
 * @package   dp-maintenance-mode
 * @copyright Copyright (c) 2024, John Greenfield
 * @license   GPL3+
 */
?>
<div class="wrap">
    <h2><?php _e('DP Maintenance Mode', DPMM_PLUGIN_DOMAIN); ?></h2>
    <p class="description"><?php _e('Thanks for choosing DP Maintenance Mode.', DPMM_PLUGIN_DOMAIN); ?></p>
    <h3><?php _e('Usage', DPMM_PLUGIN_DOMAIN); ?></h3>
    <p class="description"><?php _e('To get started, simply choose the \'Enabled\' option below.<br /> Then, select either Maintenance Mode or the Coming Soon page. Customise the content below to your liking, and remember to click \'Save Changes\' when you\'re finished.', DPMM_PLUGIN_DOMAIN); ?></p> 
    <form method="post" action="options.php">
        <?php 
        settings_fields('dpmm');
        do_settings_sections('dpmm');
        // Set class property.
        $this->options = array('dpmm-social-profiles' => get_option( 'dpmm-social-profiles')); ?>

        <?php $this->notify(); ?>

        <table class="form-table form--dpmm-enabled">
            <tr valign="top">
                <th scope="row">
                    <label for="dpmm_enabled"><?php _e('Enabled', DPMM_PLUGIN_DOMAIN); ?></label>
                </th>
                <td>
                    <?php $dpmm_enabled = esc_attr(get_option('dpmm-enabled')); ?>
                    <input type="checkbox" id="dpmm_enabled" name="dpmm-enabled" value="1" <?php checked($dpmm_enabled, 1); ?>>
                    <?php if ($dpmm_enabled) : ?>
                        <p class="description"><?php echo $this->dpmm_get_messages('dpmm_enabled'); ?></p>
                    <?php else: ?>
                        <p class="description"><?php echo $this->dpmm_get_messages('dpmm_disabled'); ?></p>
                    <?php endif; ?>
                        <p class="inline-warning" id="enabled-warning" style="display: none;">Remember to click the 'Save' button to ensure your changes are applied!</p>
                </td>
            </tr>
        </table>

        <div class="dpmm-settings-wrapper" <?php $settingsDisplay = $dpmm_enabled == 1 ? "style=\"display: table;\"" : "style=\"display: none;\""; echo $settingsDisplay; ?>>
            <table class="form-table form--dpmm-settings">
                <tr>
                    <th scope="row"><?php _e('Mode', DPMM_PLUGIN_DOMAIN); ?></th>
                    <td>
                        <?php $dpmm_mode = esc_attr(get_option('dpmm-mode')); ?>
                        <?php $mode_default = $dpmm_mode == 'default' ? true : false; ?>
                        <?php $mode_con = $dpmm_mode == 'con' ? true : false; ?>
                        <label>
                            <input name="dpmm-mode" type="radio" value="default" <?php checked($mode_default, 1); ?>>
                            <?php _e('Maintenance Mode', DPMM_PLUGIN_DOMAIN); ?> (<?php _e('Default', DPMM_PLUGIN_DOMAIN); ?>)
                        </label>
                        <label>
                            <input name="dpmm-mode" type="radio" value="con" <?php checked($mode_con, 1); ?>>
                            <?php _e('Construction Mode', DPMM_PLUGIN_DOMAIN); ?>
                        </label>
                        <p class="description">
                            <?php _e('If you are putting your site into maintenance mode for a longer period of time, you should set this to Construction Mode. Otherwise use "Maintenance Mode".', DPMM_PLUGIN_DOMAIN); ?><br />
                            <?php _e('Maintenance Mode sets HTTP status to 503, Construction Mode will set HTTP status to 200.', DPMM_PLUGIN_DOMAIN); ?> <a href="https://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="blank"><?php _e('Learn more.', DPMM_PLUGIN_DOMAIN); ?></a>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Content', DPMM_PLUGIN_DOMAIN); ?></th>
                    <td>
                        <?php $this->editor_content(); ?>
                    </td>
                </tr>
            </table>

            <table class="form-table form--dpmm-construction-settings" <?php $constructionDisplay = $dpmm_mode == 'con' ? "style=\"display: table;\"" : "style=\"display: none;\""; echo $constructionDisplay; ?>>
                <tr id="dpmm-logo-wrapper">
                    <th scope="row">
                        <?php _e('Logo', DPMM_PLUGIN_DOMAIN); ?>
                        <p class="description">Note: Logo is displayed in the header section of the page. The logo will be displayed with a max-width of 250px. If no logo is chosen, then the site title will be displayed instead.</p>
                    </th>
                    <td>
                        <?php 
                        $image_id = get_option( 'dpmm-image-id' );
                        if( intval( $image_id ) > 0 ) {
                            // Change with the image size you want to use
                            $image = wp_get_attachment_image( $image_id, 'medium', false, array( 'id' => 'dpmm-preview-image' ) );
                        } else {
                            // Some default image
                            $image = '<img id="dpmm-preview-image" src="' . DPMM_PLUGIN_URL . 'assets/images/placeholder.png" />';
                        }

                        echo $image; ?>
                        <p>
                        <input type="hidden" name="dpmm-image-id" id="dpmm-image-id" value="<?php echo esc_attr( $image_id ); ?>" class="regular-text" />
                        <input type='button' class="button-primary" value="<?php esc_attr_e( 'Select image', DPMM_PLUGIN_DOMAIN ); ?>" id="dpmm-media-manager"/>
                        <!-- <?php if( intval( $image_id ) > 0 ) { ?>
                            <input type='button' class="button-primary" value="<?php esc_attr_e( 'Delete image', DPMM_PLUGIN_DOMAIN ); ?>" id="dpmm-media-delete"/>
                        <?php } ?> -->
                        <input type='button' class="button-primary" value="<?php esc_attr_e( 'Delete image', DPMM_PLUGIN_DOMAIN ); ?>" id="dpmm-media-delete" style="display: none;"/>
                        </p>
                    </td>
                </tr>

                <tr id="dpmm-social-icons-wrapper">
                    <th scope="row">
                        <?php _e('Social Icons', DPMM_PLUGIN_DOMAIN); ?>
                    </th>
                    <td>
                        <?php if ( ! empty( $this->options['dpmm-social-profiles'] ) ) {
                            foreach ( (array) $this->options['dpmm-social-profiles'] as $name => $element ) {
                            foreach ( $element as $index => $value ) { ?>
                                <div class="dp-social-profile">
                                    <label for="dpmm-social-profiles-<?php echo esc_attr( $name ); ?>-<?php echo esc_attr( $index ); ?>" class="dp-option-label">
                                        <?php echo esc_html( $this->social[ $name ] ); ?>:
                                    </label>
                                    <input
                                        type="text"
                                        id="dpmm-social-profiles-<?php echo esc_attr( $name ); ?>-<?php echo esc_attr( $index ); ?>"
                                        name="dpmm-social-profiles[<?php echo esc_attr( $name ); ?>][]"
                                        class="<?php echo esc_attr( $name ); ?>"
                                        value="<?php echo esc_attr( $value ); ?>"
                                        placeholder="<?php esc_attr_e( 'http://' ); ?>"
                                    />
                                    <button class="button dp-social-remove"><b>–</b></button>
                                </div>
                                <?php
                            }
                            }
                        } else { ?>
                            <div class="dp-social-profile">
                            <label for="dpmm-social-profiles-bandcamp-0" class="dp-option-label"><?php echo esc_html( $this->social['bandcamp'] ); ?>:</label>
                            <input
                                type="text"
                                id="dpmm-social-profiles-bandcamp-0"
                                name="dpmm-social-profiles[bandcamp][]"
                                class="bandcamp"
                                value=""
                                placeholder="<?php esc_attr_e( 'http://' ); ?>"
                            />
                            <button class="button dp-social-remove">-</button>
                            </div>
                            <?php  } ?>
                        <hr>
                        <div class="dp-social-profile-selector-wrapper">
                            <label for="social_profile_selector" class="dp-option-label"><?php esc_attr_e( 'Select profile: ' ); ?></label>
                            <select id="social_profile_selector">
                            <?php
                            foreach ( $this->social as $name => $option ) { ?>
                                <option <?php selected( $name, 'bandcamp' ); ?> value="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $option ); ?></option>
                            <?php } ?>
                            </select>
                            <button id="social_profile_add" class="button">Add new...</button>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="form-table form--dpmm-preview">
                <tr>
                    <th scope="row">
                        <?php _e('Preview', DPMM_PLUGIN_DOMAIN); ?>
                        <p class="description">Press the preview button to see how your site will look.</p>
                    </th>
                    <td>
                        <a href="<?php echo add_query_arg('dpmm', 'preview', esc_url(bloginfo('url') ?? '')); ?>" target="_blank" class="button button-secondary"><?php _e('Preview', DPMM_PLUGIN_DOMAIN); ?></a>
                    </td>
                </tr>
            </table>

            <a href="#" class="dpmm-advanced-settings">
                <span class="dpmm-advanced-settings__label-advanced">
                    <?php _e('Advanced Settings', DPMM_PLUGIN_DOMAIN); ?>
                </span>
                <span class="dpmm-advanced-settings__label-hide-advanced" style="display: none;">
                    <?php _e('Hide Advanced Settings', DPMM_PLUGIN_DOMAIN); ?>
                </span>
            </a>

            <table class="form-table form--dpmm-advanced-settings" style="display: none">

                <tr valign="top">
                    <th scope="row"><?php _e('Site Title', DPMM_PLUGIN_DOMAIN); ?></th>
                    <td>
                        <?php $dpmm_site_title = esc_attr(get_option('dpmm-site-title')); ?>
                        <input name="dpmm-site-title" type="text" id="dpmm-site-title" placeholder="<?php echo $this->site_title(); ?>" value="<?php echo $dpmm_site_title; ?>" class="regular-text">
                        <p class="description"><?php _e('Overrides default site meta title.', DPMM_PLUGIN_DOMAIN); ?></p>
                    </td>
                </tr>

                <?php $options = get_option('dpmm-roles'); ?>
                <?php $wp_roles = get_editable_roles(); ?>
                <?php if ($wp_roles && is_array($wp_roles)) : ?>
                    <tr valign="top">
                        <th scope="row"><?php _e('User Roles', DPMM_PLUGIN_DOMAIN); ?>
                            <p class="description"><?php _e('Tick the ones that can access front-end of your website if maintenance mode is enabled', DPMM_PLUGIN_DOMAIN); ?>.</p>
                            <p class="description"><?php _e('Please note that this does NOT apply to admin area', DPMM_PLUGIN_DOMAIN); ?>.</p>
                            <p><a href="#" class="dpmm-toggle-all"><?php _e('Toggle all', DPMM_PLUGIN_DOMAIN); ?></a></p>
                        </th>
                        <td>
                            <?php foreach ($wp_roles as $role => $role_details) :  ?>
                                <?php if ($role !== 'administrator') : ?>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span><?php echo (isset($options[$role])) ? $options[$role] : ''; ?></span>
                                        </legend>
                                        <label>
                                            <input type="checkbox" class="dpmm-roles" name="dpmm-roles[<?php echo $role; ?>]" value="1" <?php checked(isset($options[$role]), 1); ?> /> <?php echo $role_details['name']; ?>
                                        </label>
                                    </fieldset>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr valign="top">
                        <th scope="row" colspan="2">
                            <p class="description"><?php _e('User Role control is currently not available on your website. Sorry!', DPMM_PLUGIN_DOMAIN); ?></p>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr valign="middle">
                    <th scope="row"><?php _e('Custom Stylesheet', DPMM_PLUGIN_DOMAIN); ?></th>
                    <td>
                        <?php $dpmm_site_title = esc_attr(get_option('dpmm-site-title')); ?>
                        <?php $dpmm_stylesheet_filename = $this->get_css_filename(); ?>
                        <?php $dpmm_has_custom_stylesheet = (bool) $this->get_custom_stylesheet_url(); ?>
                        <?php if ($dpmm_has_custom_stylesheet) : ?>
                            <p>
                                <span style="line-height: 1.3; font-weight: 600; color: green;">You are currently using custom stylesheet.</span>
                                <span class="description">(<?php _e("'$dpmm_stylesheet_filename' file in your theme folder", DPMM_PLUGIN_DOMAIN); ?>)</span>
                            </p>
                        <?php else : ?>
                            <p class="description"><?php _e("To enable custom styling, simply include a file named '$dpmm_stylesheet_filename' in your theme folder. Once detected by DP Maintenance Mode, its usage will be reflected in this section.", DPMM_PLUGIN_DOMAIN); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="dpmm_code_snippet"><?php _e('Inject code snippet', DPMM_PLUGIN_DOMAIN); ?></label>
                    </th>
                    <td>
                        <textarea id="dpmm_code_snippet" name="dpmm_code_snippet" style="width:100%;height:150px"><?php echo esc_attr(get_option('dpmm_code_snippet')); ?></textarea>
                        <p class="description">
                            <?php _e('This is useful to add a Javascript snippet to the page.', DPMM_PLUGIN_DOMAIN); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(); ?>
    </form>
</div>