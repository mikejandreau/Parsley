<?php 
/*
 * theme-options.php
 * 
 * Table of contents:
 * 
 * 1. DEFINITIONS
 * 2. HOOKS
 * 3. RENDER FUNCTIONS
 * 4. SANITIZE FUNCTIONS
 * 5. CUSTOM SCRIPTS
 * 6. OTHER FUNCTIONS
 */



/*
 * 1. DEFINITIONS
 * Section information.
 */
$parsley_sections = [
    'section-theme-settings' => [
        'title' => 'Theme Settings',
        'desc'  => 'General theme settings.'
    ],
    'section-styling-settings' => [
        'title' => 'Styling Settings',
        'desc'  => 'Settings for editing colors, fonts and CSS.'
    ],
    'section-social-settings' => [
        'title' => 'Social Settings',
        'desc'  => 'Edit your social media profiles.'
    ]
];

/*
 * Field information.
 */
$parsley_fields = [
    'parsley-logo' => [
        'title'    => 'Default logo',
        'type'     => 'upload',
        'section'  => 'section-theme-settings',
        'default'  => '',
        'desc'     => 'Set your default logo. Upload or choose an existing one.',
        'sanitize' => ''
    ],
    'parsley-logo-alternate' => [
        'title'    => 'Alternate logo',
        'type'     => 'upload',
        'section'  => 'section-theme-settings',
        'default'  => '',
        'desc'     => 'Set your alternate logo. This can be used for an inverted background for example. Upload or choose an existing one.',
        'sanitize' => ''
    ],
    'parsley-google-analytics' => [
        'title'    => 'Google Analytics Tracking ID',
        'type'     => 'text',
        'section'  => 'section-theme-settings',
        'default'  => '',
        'desc'     => 'Only enter your tracking ID in the format: UA-XXXXX-X. For example: UA-12345-6.',
        'sanitize' => 'google-analytics'
    ],
    'parsley-search-bar' => [
        'title'    => 'Search Bar',
        'type'     => 'checkbox',
        'label'    => 'Display search bar in the site header.',
        'section'  => 'section-theme-settings',
        'default'  => 0,
        'desc'     => '',
        'sanitize' => ''
    ],
    'parsley-color-scheme' => [
        'title'    => 'Color Scheme',
        'type'     => 'radio',
        'children' => ['Light', 'Dark'],
        'section'  => 'section-styling-settings',
        'default'  => 0,
        'desc'     => '',
        'sanitize' => ''
    ],
    'parsley-font-pair' => [
        'title'    => 'Font Pair',
        'type'     => 'select',
        'children' => ['Modern', 'Classic', 'Futuristic', 'Thin', 'Narrow'],
        'section'  => 'section-styling-settings',
        'default'  => 0,
        'desc'     => '',
        'sanitize' => ''
    ],
    'parsley-custom-css' => [
        'title'    => 'Custom CSS',
        'type'     => 'textarea',
        'section'  => 'section-styling-settings',
        'default'  => '',
        'desc'     => '',
        'sanitize' => 'default'
    ],
    'parsley-social-twitter' => [
        'title'    => 'Twitter Profile',
        'type'     => 'text',
        'section'  => 'section-social-settings',
        'default'  => '',
        'desc'     => '',
        'sanitize' => 'full'
    ],
    'parsley-social-facebook' => [
        'title'    => 'Facebook Profile',
        'type'     => 'text',
        'section'  => 'section-social-settings',
        'default'  => '',
        'desc'     => '',
        'sanitize' => 'full'
    ],
    'parsley-social-googleplus' => [
        'title'    => 'Google+ Profile',
        'type'     => 'text',
        'section'  => 'section-social-settings',
        'default'  => '',
        'desc'     => '',
        'sanitize' => 'full'
    ]
];



/*
 * 2. HOOKS
 */
add_action( 'after_setup_theme', 'parsley_init_option' );
add_action( 'admin_menu', 'parsley_update_menu' );
add_action( 'admin_init', 'parsley_init_settings' );
add_action( 'admin_enqueue_scripts', 'parsley_options_custom_scripts' );



/*
 * 3. RENDER FUNCTIONS
 * Renders a section description.
 */
function parsley_render_section( $args ) {
    global $parsley_sections;

    echo "<p>" . $parsley_sections[ $args['id'] ]['desc'] . "</p>";
    echo "<hr />";
}

/*
 * Renders input fields: can be text, textarea, checkbox, radio, select, or upload
 */
function parsley_render_field( $id ) {
    global $parsley_fields;

    $options = get_option( 'parsley_options' );

    // If options are not set yet for that ID, grab the default value.
    $field_value = isset( $options[ $id ] ) ? $options[ $id ] : parsley_get_field_default( $id );

    // Generate HTML markup based on field type.
    switch ( $parsley_fields[ $id ]['type'] ) {
        case 'text': 
            echo "<input type='text' name='parsley_options[" . $id . "]' value='" . $field_value . "' />";
            echo "<p class='description'>" . $parsley_fields[ $id ]['desc'] . "</p>";
            
            break;

        case 'upload':
            $visibility_class = ( '' != $field_value ) ? "" : "hide";

            echo "<img src='" . $field_value . "' alt='Logo' class='parsley-custom-thumbnail " . $visibility_class . "' id='" . $id . "-thumbnail' />";
            echo "<input type='hidden' name='parsley_options[" . $id . "]' id='" . $id . "-upload-field' value='" . $field_value . "' />";
            echo "<input type='button' class='btn-upload-img button' value='Upload logo' data-field-id='" . $id . "' />";
            echo "<input type='button' class='btn-remove-img button " . $visibility_class . "' value='Remove logo' data-field-id='" . $id . "' id='" . $id . "-remove-button' />";
            echo "<p class='description'>" . $parsley_fields[ $id ]['desc'] . "</p>";
            
            break;

        case 'textarea': 
            echo "<textarea name='parsley_options[" . $id . "]' cols='40' rows='10'>" . $field_value . "</textarea>";
            echo "<p class='description'>" . $parsley_fields[ $id ]['desc'] . "</p>";
            
            break;

        case 'checkbox':
            echo "<input type='checkbox' name='parsley_options[" . $id . "]' id='" . $id . "' value='1' " . checked( $field_value, 1, false ) . " />";
            echo "<label for='" . $id . "'>" . $parsley_fields[ $id ]['label'] . "</label>";

            break;

        case 'radio': 
            // Generate as many radio buttons as there are children.
            for ( $i = 0; $i < sizeof( $parsley_fields[ $id ]['children'] ); $i++ ) {
                echo "<p>";
                echo "<input type='radio' name='parsley_options[" . $id . "]' id='parsley_options[" . $id . "]-" . $i . "' value='" . $i . "' " . checked( $field_value, $i, false ) . " />";
                echo "<label for='parsley_options[" . $id . "]-" . $i . "'>" . $parsley_fields[ $id ]['children'][ $i ] . "</label>";
                echo "</p>";
            }

            break;

        case 'select': 
            echo "<select name='parsley_options[" . $id . "]'>";
            for ( $i = 0; $i < sizeof( $parsley_fields[ $id ]['children'] ); $i++ ) {
                echo "<option value='" . $i . "' " . selected( $field_value, $i, false ) . ">";
                echo $parsley_fields[ $id ]['children'][ $i ];
                echo "</option>";
            }
            echo "</select>";

            break;
    }
}

/*
 * Renders the theme options page.
 */
function parsley_render_theme_options() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page.' );
    } ?>

    <div class="wrap">
        <h1>Theme Options</h1>

        <?php settings_errors(); ?>

        <form action="options.php" method="post">
            <?php
                settings_fields( "parsley_options" );
                do_settings_sections( "parsley-theme-options" );
                echo "<hr />";
                submit_button();
            ?>
        </form>
    </div>
<?php }



/*
 * 4. SANITIZE FUNCTIONS
 * Sanitizes the settings.
 */
function parsley_options_validate( $input ) {
    // Define a blank array for the output.
    $output = [];

    // Do a general sanitization for every field.
    foreach ( $input as $key => $value ) {
        // Grab the sanitize option for this field.
        $field_sanitize = parsley_get_field_sanitize( $key );

        switch ( $field_sanitize ) {
            case 'default':
                $output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );
                break;
            
            case 'full':
                $output[ $key ] = esc_url_raw( strip_tags( stripslashes( $input[ $key ] ) ) );
                break;

            case 'google-analytics':
                $output[ $key ] = ( preg_match('/^UA-[0-9]+-[0-9]+$/', $input[ $key ] ) ) ? $input[ $key ] : '';
                break;

            default:
                $output[ $key ] = $input[ $key ];
                break;
        }
    }

    return $output;
}



/*
 * 5. CUSTOM SCRIPTS
 * Registers and loads custom JavaScript and CSS.
 */
function parsley_options_custom_scripts() {
    // Get information about the current page.
    $screen = get_current_screen();

    // Register a custom script that depends on jQuery, Media Upload and Thickbox (available from the Core).
    wp_register_script( 'parsley-custom-admin-scripts', get_template_directory_uri() .'/assets/js/parsley-theme-options.js', array( 'jquery' ) );

    // Register custom styles.
    wp_register_style( 'parsley-custom-admin-styles', get_template_directory_uri() .'/assets/css/parsley-theme-options.css' );
    
    // Only load these scripts if we're on the theme options page.
    if ( 'appearance_page_parsley-theme-options' == $screen->id ) {
        // Enqueues all scripts, styles, settings, and templates necessary to use all media JavaScript APIs.
        wp_enqueue_media();
        
        // Load our custom scripts.
        wp_enqueue_script( 'parsley-custom-admin-scripts' );

        // Load our custom styles.
        wp_enqueue_style( 'parsley-custom-admin-styles' );
    }    
}



/*
 * 6. OTHER FUNCTIONS
 * Returns the default value of a field.
 */
function parsley_get_field_default( $id ) {
    global $parsley_fields;

    return $parsley_fields[ $id ]['default'];
}

/*
 * Checks if the options exists in the database.
 */
function parsley_init_option() {
    $options = get_option( 'parsley_options' );

    if ( false === $options ) {
        add_option( 'parsley_options' );
    }
}

/*
 * Creates a sub-menu under Appearance.
 */
function parsley_update_menu() {
    add_theme_page( 'Theme Options', 'Theme Options', 'manage_options', 'parsley-theme-options', 'parsley_render_theme_options' );
}

/*
 * Registers and adds settings, sections and fields.
 */
function parsley_init_settings() {
    // Declare $parsley_sections and $parsley_fields as global.
    global $parsley_fields, $parsley_sections;

    // Register a general setting.
    // The $option_group is the same as $option_name to prevent the "Error: options page not found." problem.
    register_setting( "parsley_options", "parsley_options", "parsley_options_validate" );

    // Add sections as defined in the $parsley_sections array.
    foreach ($parsley_sections as $section_id => $section_value) {
        add_settings_section( $section_id, $section_value['title'], "parsley_render_section", "parsley-theme-options" );
    }

    // Add fields as defined in the $parsley_fields array.
    foreach ($parsley_fields as $field_id => $field_value) {
        add_settings_field( $field_id, $field_value['title'], "parsley_render_field", "parsley-theme-options", $field_value['section'], $field_id );
    }
}

/*
 * Returns the sanitized field value.
 */
function parsley_get_field_sanitize( $id ) {
    global $parsley_fields;

    return $parsley_fields[ $id ]['sanitize'];
}