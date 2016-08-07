<?php
function arzte_admin(){

    /**
    * Add additional user profile field. Here
    * we are using a filter becuase we need
    * address, tel etc to be in the same cohort
    */
    function modify_contact_methods($contact_fields) {
        // Add new fields
        $contact_fields['telephone'] = 'Telephone';
        $contact_fields['strasse'] = 'Strasse';
        $contact_fields['plz'] = 'PLZ';
        $contact_fields['klinik'] = 'Klinik';

        return $contact_fields;


    }
    add_filter('user_contactmethods', 'modify_contact_methods');


    add_filter('manage_users_columns', 'pippin_add_user_id_column');
    function pippin_add_user_id_column($columns) {
        $columns['clinic'] = 'clinic';
        return $columns;
    }

    add_action('manage_users_custom_column',  'artze_extra_user_column_contents', 10, 3);
    function artze_extra_user_column_contents($value, $column_name, $user_id) {
        $column_value = get_user_meta( $user_id, 'clinic', true );
        if ( 'clinic' == $column_name )
            return $column_value;

        return '';
    }
}

arzte_admin();
