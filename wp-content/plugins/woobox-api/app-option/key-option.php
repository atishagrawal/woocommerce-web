<?php
/*
 * key Options
 */
$app_opt_name;
Redux::setSection( $app_opt_name, array(
    'title' => esc_html__( 'Key', 'woobox' ),
    'id'    => 'key-editor',
    'icon'  => 'el el-arrow-up',
    'customizer_width' => '500px',
) );


Redux::setSection( $app_opt_name, array(
    'title' => esc_html__( 'Consumer Keys', 'woobox' ),
    'id'    => 'key',
    'icon'  => 'ion-ios-list-outline',
    'subsection' => true,
    'desc'  => esc_html__( '', 'woobox' ),
    'fields'           => array(
        
        // array(
        //     'id'        => 'client_key',
        //     'type'      => 'text',
        //     'title'     => esc_html__( 'Client Key','woobox'),
        //     'subtitle'  => wp_kses( __( '<br />Put You Client Key Here','woobox' ), array( 'br' => array() ) ),
            
        // ),

        // array(
        //     'id'        => 'client_secret',
        //     'type'      => 'text',
        //     'title'     => esc_html__( ' Client Secret ','woobox'),
        //     'subtitle'  => wp_kses( __( '<br />Put You Client Secret Here','woobox' ), array( 'br' => array() ) ),
            
        // ),

        array(
            'id'        => 'opt-blank-text',
            'type'      => 'custom_field',
            
        ),
        
     
        
                    
    )
    
) );