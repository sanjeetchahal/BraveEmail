<?php
    register_rest_field( 'mails', 'subject',

        array(
            'get_callback'          => 'get_field_mails' ,
            'update_callback'       => 'set_field_mails' ,
            'show_in_rest'          => true,
            'auth_callback'	        => 'permission_check_mails',
        )
    );
    register_rest_field( 'mails', 'message-body',

        array(
            'get_callback'          => 'get_field_mails' ,
            'update_callback'       => 'set_field_mails' ,
            'show_in_rest'          => true,
            'auth_callback'	        => 'permission_check_mails',
        )
    );
    register_rest_field( 'mails', 'email-formatting',

        array(
            'get_callback'          => 'get_field_mails' ,
            'update_callback'       => 'set_field_mails' ,
            'show_in_rest'          => true,
            'auth_callback'	        => 'permission_check_mails',
        )
    );
    register_rest_field( 'mails', 'subject',

        array(
            'get_callback'          => 'get_field_mails' ,
            'update_callback'       => 'set_field_mails' ,
            'show_in_rest'          => true,
            'auth_callback'	        => 'permission_check_mails',
        )
    );


    function permission_check_mails( $request ) {
    return true;
}

function get_field_mails( $post,  $field_name, $request ) {
    $first_name = get_post_meta( $post['id'], $field_name, true );
    $first_name = ! empty( $first_name ) ? $first_name : "";
    return $first_name;
}
function set_field_mails( $value, $post,$fieldname ) {
    $first_name = $value;
    return update_post_meta( $post->ID, $fieldname, $first_name );
}


    ?>