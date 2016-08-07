<?php

//this sends an email with link to new user for confirmation that they have //registered to the site.
function arzte_new_user_confirmation( $user_id ){

    global $wpdb;
    $user = get_userdata( $user_id );

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);


    // Generate something random for a password reset key.
    $key = wp_generate_password( 20, false );

    /** This action is documented in wp-login.php */
    do_action( 'retrieve_password_key', $user->user_login, $key );

    // Now insert the key, hashed, into the DB.
    if ( empty( $wp_hasher ) ) {
        require_once ABSPATH . WPINC . '/class-phpass.php';
        $wp_hasher = new PasswordHash( 8, true );
    }
    $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );


    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= __('To confirm your registration, visit the following address:') . "\r\n\r\n";
    $message .= '<' . network_site_url("register/confirmation?action=confirm&email=" . rawurlencode($user->user_email) ) . ">\r\n\r\n";

    $message .= wp_login_url() . "\r\n";

    wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}



// Once user has visited confirmation link this sends another email with key and
// link to set password.
function arzte_new_user_confirmed( $user_id, $deprecated = null, $notify = '' ){
    if ( $deprecated !== null ) {
        _deprecated_argument( __FUNCTION__, '4.3.1' );
    }

    global $wpdb;
    $user = get_userdata( $user_id );

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    //Notify admin that new user has confirmed their email

    $message  = sprintf(__('New user confirmation on your site %s:'), $blogname) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User user confirmed'), $blogname), $message);


    // Generate something random for a password reset key.
    $key = wp_generate_password( 20, false );

    /** This action is documented in wp-login.php */
    do_action( 'retrieve_password_key', $user->user_login, $key );

    // Now insert the key, hashed, into the DB.
    if ( empty( $wp_hasher ) ) {
        require_once ABSPATH . WPINC . '/class-phpass.php';
        $wp_hasher = new PasswordHash( 8, true );
    }
    $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

    $message .= wp_login_url() . "\r\n";

    wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}

//This checks
function arzte_confirm(){
    if(isset($_GET['email']) && isset($_GET['action']) ) :
        // send post confirmation login password
        $confirmed_user = get_user_by( 'email', rawurldecode($_GET['email']) );
        arzte_new_user_confirmed($confirmed_user->ID, null, 'both');
        // print a confirmation message
        echo "Thanks for confirming your email. Check your an email for a password/login link.";
    else:
      echo "Not recognised";
    endif;
}


function arztfinder_new_registration_email( $user_email ){
    //Notify admin that new user has confirmed their email
    $message  = 'New user registered for Arztfinder inclusion' . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User registered for artzefinder'), $blogname), $message);


    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

    $message .= wp_login_url() . "\r\n";

    wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
}


?>