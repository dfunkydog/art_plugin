<?php
//require plugin_dir_path( __FILE__ ) . 'arzte_reg_confirmation_email.php';

function add_arztinfo($data){
global $wpdb;
$table = "wp_arztinfo_reg";
$format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s'];

$wpdb->insert( $table, $data, $format );
$wpdb->insert_id;
}

function arztfinder(&$fields, &$errors) {

  // Check args and replace if necessary
  if (!is_array($fields))     $fields = array();
  if (!is_wp_error($errors))  $errors = new WP_Error;

  // Check for form submit
  if (isset($_POST['submit'])) {

    // Get fields from submitted form
    $fields = arztfinder_get_fields();

    // Validate fields and produce errors
    if (arztfinder_validate($fields, $errors)) {

      // If successful, register user
      add_arztinfo($fields);

      // And display a message
      $new_email = $fields['email']; ?>
      <ul class="artze_reg_message">
        <li>Registration complete. You've been sent a verification email at <strong><?php echo $new_email; ?></strong></li>
      </ul>

      <?php //Send out emails
      arztfinder_new_registration_email($fields);

      // Clear field data
      $fields = array();
    }
  }

  // Santitize fields
  arztfinder_sanitize($fields);

  // Generate form
  arztfinder_display_form($fields, $errors);
}

function arztfinder_sanitize(&$fields) {
  $fields['agree']   =  isset($fields['agree'])  ? $fields['agree'] : false;
  $fields['confirm']   =  isset($fields['confirm'])  ? $fields['confirm'] : false;
  $fields['email']   =  isset($fields['email'])  ? sanitize_email($fields['email']) : '';
  $fields['user_url']     =  isset($fields['user_url'])    ? esc_url($fields['user_url']) : '';
  $fields['full_name']   =  isset($fields['full_name'])  ? sanitize_text_field($fields['full_name']) : '';
  $fields['clinic']     =  isset($fields['clinic'])    ? sanitize_text_field($fields['clinic']) : '';
  $fields['telephone']  =  isset($fields['telephone']) ? esc_textarea($fields['telephone']) : '';
  $fields['address']  =  isset($fields['address']) ? esc_textarea($fields['address']) : '';
  $fields['city']  =  isset($fields['city']) ? esc_textarea($fields['city']) : '';

}

function arztfinder_display_form($fields = array(), $errors = null) {

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
        <p>Registrierung für Arztsuche</p>
      <input type="checkbox" id="agree" name="agree" />
      <label for="agree"><strong>Ja, bitte schicken Sie mir</strong> die Zugangsdaten zur Webseite <strong>Arzteinfo-Abbott-Medical-Optics.de</strong><br>Als Benutzername wird die unten angegebene E-Mail-Addresse verwendet.<br>
      Alle nicht gekennzeichneten Felder sind Pflichtfelder f&uuml;r die Registrierung.</label>
    </div>

    <div>
      <label for="clinic">Klinik/Praxis</label>
      <input type="text" name="clinic" value="<?php echo (isset($fields['clinic']) ? $fields['clinic'] : '') ?>" required>
    </div>

    <div>
      <label for="firstname">Name</label>
      <input type="text" name="full_name" value="<?php echo (isset($fields['full_name']) ? $fields['full_name'] : '') ?>" required>
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
      <input type="text" name="email" value="<?php echo (isset($fields['email']) ? $fields['email'] : '') ?>" required>
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

function arztfinder_get_fields() {
  return array(
    'confirm' =>  isset($_POST['confirm'])   ?  true : false,
    'email'   =>  isset($_POST['email'])   ?  $_POST['email']        :  '',
    'user_url'     =>  isset($_POST['user_url'])     ?  $_POST['user_url']     :  '',
    'full_name'   =>  isset($_POST['full_name'])   ?  $_POST['full_name']        :  '',
    'clinic'     =>  isset($_POST['clinic'])     ?  $_POST['clinic']     :  '',
    'telephone'     =>  isset($_POST['telephone'])     ?  $_POST['telephone']     :  '',
    'address'     =>  isset($_POST['address'])     ?  $_POST['address']     :  '',
    'city'  =>  isset($_POST['city'])  ?  $_POST['city']  :  ''
  );
}

function arztfinder_validate(&$fields, &$errors) {

  // wp error obj
  if (!($fields['full_name']) ) {
      $errors->add('full_name', 'You enter your full name' );
  }

  if (!is_wp_error($errors))  $errors = new WP_Error;

  if (!is_email($fields['email'])) {
    $errors->add('email_invalid', 'Email is not valid');
  }


  if (!empty($fields['user_url'])) {
    if (!filter_var($fields['user_url'], FILTER_VALIDATE_URL)) {
      $errors->add('user_url', 'Website is not a valid URL');
    }
  }

  if (($fields['confirm'] != true) ) {
      $errors->add('confirm', 'You must confirm that you a an HCP' );
  }

  // If errors were produced, fail
  if (count($errors->get_error_messages()) > 0) {
    return false;
  }

  // Else, success!
  return true;
}