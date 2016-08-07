<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://nlsltd.com
 * @since             1.0.0
 * @package           Arzte_reg
 *
 * @wordpress-plugin
 * Plugin Name:       Arzte registration
 * Plugin URI:        http://nlsltd.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Michael Dyer
 * Author URI:        http://nlsltd.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       arzte_reg
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-arzte_reg-activator.php
 */
function activate_arzte_reg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arzte_reg-activator.php';
	Arzte_reg_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-arzte_reg-deactivator.php
 */
function deactivate_arzte_reg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arzte_reg-deactivator.php';
	Arzte_reg_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_arzte_reg' );
register_deactivation_hook( __FILE__, 'deactivate_arzte_reg' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-arzte_reg.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_arzte_reg() {

	$plugin = new Arzte_reg();
	$plugin->run();

}
run_arzte_reg();


include plugin_dir_path( __FILE__ ) . 'includes/arzte_reg_admin.php';

include plugin_dir_path( __FILE__ ) . 'public/partials/arzte_reg_confirmation_email.php';



function arzte(&$fields, &$errors) {

  // Check args and replace if necessary
  if (!is_array($fields))     $fields = array();
  if (!is_wp_error($errors))  $errors = new WP_Error;

  // Check for form submit
  if (isset($_POST['submit'])) {

    // Get fields from submitted form
    $fields = arzte_get_fields();

    // Validate fields and produce errors
    if (arzte_validate($fields, $errors)) {

      // If successful, register user
      wp_insert_user($fields);



      // And display a message
      $new_user_email = $fields['user_email']; ?>
      <ul class="artze_reg_message">
        <li>Registration complete. You've been sent a verification email at <strong><?php echo  $new_user_email ?> </strong></li>
      </ul>
      <?php $new_user = get_user_by( 'email', $new_user_email );
      arzte_new_user_confirmation($new_user->ID, null, 'both');

      // Clear field data
      $fields = array();
    }
  }

  // Santitize fields
  arzte_sanitize($fields);

  // Generate form
  arzte_display_form($fields, $errors);
}

function arzte_sanitize(&$fields) {
  $fields['user_login']   =  isset($fields['user_login'])  ? sanitize_user($fields['user_login']) : '';
  $fields['agree']   =  isset($fields['agree'])  ? $fields['agree'] : false;
  $fields['user_email']   =  isset($fields['user_email'])  ? sanitize_email($fields['user_email']) : '';
  $fields['user_url']     =  isset($fields['user_url'])    ? esc_url($fields['user_url']) : '';
  $fields['first_name']   =  isset($fields['first_name'])  ? sanitize_text_field($fields['first_name']) : '';
  $fields['last_name']    =  isset($fields['last_name'])   ? sanitize_text_field($fields['last_name']) : '';
  $fields['clinic']     =  isset($fields['clinic'])    ? sanitize_text_field($fields['clinic']) : '';
  $fields['telephone']  =  isset($fields['telephone']) ? esc_textarea($fields['telephone']) : '';
  $fields['address']  =  isset($fields['address']) ? esc_textarea($fields['address']) : '';
  $fields['city']  =  isset($fields['city']) ? esc_textarea($fields['city']) : '';

}

function arzte_display_form($fields = array(), $errors = null) {

  // Check for wp error obj and see if it has any errors
  if (is_wp_error($errors) && count($errors->get_error_messages()) > 0) {

    // Display errors
    ?><ul class="artze_reg_message arzte_errors"><?php
    foreach ($errors->get_error_messages() as $key => $val) {
      ?><li>
        <?php echo $val; ?>
      </li><?php
    }
    ?></ul><?php
  }

  // Display form

  ?><form action="<?php $_SERVER['REQUEST_URI'] ?>" method="post" id="arzte_reg_form">
    <div>
      <input type="checkbox" id="agree" name="agree" />
      <label for="agree"><strong>Ja, bitte schicken Sie mir</strong> die Zugangsdaten zur Webseite <strong>Arzteinfo-Abbott-Medical-Optics.de</strong><br>Als Benutzername wird die unten angegebene E-Mail-Addresse verwendet.<br>
      Alle nicht gekennzeichneten Felder sind Pflichtfelder f&uuml;r die Registrierung.</label>
    </div>

    <div>
      <label for="user_login">Username <strong>*</strong></label>
      <input type="text" name="user_login" value="<?php echo (isset($fields['user_login']) ? $fields['user_login'] : '') ?>" required>
    </div>

    <div>
      <label for="firstname">Titel/Vorname</label>
      <input type="text" name="first_name" value="<?php echo (isset($fields['first_name']) ? $fields['first_name'] : '') ?>" required>
    </div>

    <div>
      <label for="website">Nachname</label>
      <input type="text" name="last_name" value="<?php echo (isset($fields['last_name']) ? $fields['last_name'] : '') ?>" required>
    </div>



    <div>
      <label for="clinic">Klinik/Praxis</label>
      <input type="text" name="clinic" value="<?php echo (isset($fields['clinic']) ? $fields['clinic'] : '') ?>" required>
    </div>

    <div>
      <label for="address">Stra&szlig;e/Nr.</label>
      <input type="text" name="address" value="<?php echo (isset($fields['address']) ? $fields['address'] : '') ?>">
    </div>

    <div>
      <label for="city">PLZ/Stadt</label>
      <input type="text" name="city" value="<?php echo (isset($fields['city']) ? $fields['city'] : '') ?>" required>
    </div>

    <div>
      <label for="email">E-Mail <strong>*</strong></label>
      <input type="text" name="user_email" value="<?php echo (isset($fields['user_email']) ? $fields['user_email'] : '') ?>" required>
    </div>

    <div>
      <label for="telephone">Telefon* </label>
      <input type="text" name="telephone" value="<?php echo (isset($fields['telephone']) ? $fields['telephone'] : '') ?>" required>
    </div>

    <div>
      <label for="website">Webseite*</label>
      <input type="text" name="user_url" value="<?php echo (isset($fields['user_url']) ? $fields['user_url'] : '') ?>" required>
    </div>

    <div>

      <input type="checkbox" id="confirm" name="confirm" <?php echo (isset($fields['confirm']) ? 'checked' : '') ?>/>
      <label for="confirm">Ich bestätige, dass ich einen medizinischen bzw. pharmazeutischen Beruf ausübe, mich in der Ausbildung zu einem medizinischen bzw. pharmazeutischen Beruf befinde oder einen medizinischen bzw. pharmazeutischen Beruf erlernt habe.</label>
    </div>

    <input type="submit" name="submit" value="Register">
    </form><?php
}

function arzte_get_fields() {
  return array(
    'agree' =>  isset($_POST['agree'])   ?  $_POST['agree'] : false,
    'confirm' =>  isset($_POST['confirm'])   ?  $_POST['confirm'] : false,
    'user_login'   =>  isset($_POST['user_login'])   ?  $_POST['user_login']   :  '',
    'user_pass'    =>  isset($_POST['user_pass'])    ?  $_POST['user_pass']    :  '',
    'user_email'   =>  isset($_POST['user_email'])   ?  $_POST['user_email']        :  '',
    'user_url'     =>  isset($_POST['user_url'])     ?  $_POST['user_url']     :  '',
    'first_name'   =>  isset($_POST['first_name'])   ?  $_POST['first_name']        :  '',
    'last_name'    =>  isset($_POST['last_name'])    ?  $_POST['last_name']        :  '',
    'clinic'     =>  isset($_POST['clinic'])     ?  $_POST['clinic']     :  '',
    'telephone'     =>  isset($_POST['telephone'])     ?  $_POST['telephone']     :  '',
    'address'     =>  isset($_POST['address'])     ?  $_POST['address']     :  '',
    'city'  =>  isset($_POST['city'])  ?  $_POST['city']  :  ''
  );
}

function arzte_validate(&$fields, &$errors) {

  // wp error obj

  if (!is_wp_error($errors))  $errors = new WP_Error;

  // Validate form data

  if (username_exists($fields['user_login']))
    $errors->add('user_name', 'Sorry, that username already exists!');

  if (!validate_username($fields['user_login'])) {
    $errors->add('username_invalid', 'Sorry, the username you entered is not valid');
  }

  if (!is_email($fields['user_email'])) {
    $errors->add('email_invalid', 'Email is not valid');
  }

  if (email_exists($fields['user_email'])) {
    $errors->add('email', 'Email Already in use');
  }

  if (!empty($fields['user_url'])) {
    if (!filter_var($fields['user_url'], FILTER_VALIDATE_URL)) {
      $errors->add('user_url', 'Website is not a valid URL');
    }
  }

  if (!($fields['confirm']) ) {
      $errors->add('confirm', 'You must confirm that you a an HCP' );
  }

  // If errors were produced, fail
  if (count($errors->get_error_messages()) > 0) {
    return false;
  }

  // Else, success!
  return true;
}



///////////////
// SHORTCODEs //
///////////////

// The callback function for the [cr] shortcode
function arzte_cb() {
  $fields = array();
  $errors = new WP_Error();

  // Buffer output
  ob_start();

  // Custom registration, go!
  arzte($fields, $errors);

  // Return buffer
  return ob_get_clean();
}
add_shortcode('arzte_reg', 'arzte_cb');


include 'public/partials/arztfinder.php';
// The callback function for the [arztfinder] shortcode
function arzte_fd() {
  $fields = array();
  $errors = new WP_Error();

  // Buffer output
  ob_start();

  // Custom registration, go!
  arztfinder($fields, $errors);

  // Return buffer
  return ob_get_clean();
}
add_shortcode('arzte_finder', 'arzte_fd');

// The function for the [confirmation page] shortcode
function arzte_confirmation(){
  // Buffer output
  ob_start();

  // Custom registration, go!
  arzte_confirm();

  // Return buffer
  return ob_get_clean();
}
add_shortcode('arzte_confirmation', 'arzte_confirmation');
