<?php 
function add_custom_field_in_bulk_edit_quick_edit(){    

   ?>
   <br/>
   <br/>
   
   <div class="container-fluid">
   <div class="row">
        <div class="col-lg-6">
            <?php 
                woocommerce_wp_checkbox( 
                    array(
                    'id' => 'woobox_deal_of_the_day',
                    'class' => 'alignleft',
                    'label' => 'Deal Of The Day'
                    )       
                   
                    
                );
            ?>
        </div>
         <div class="col-lg-6">
            <?php 
               woocommerce_wp_checkbox( array(
                'id' => 'woobox_suggested_for_you',
                'class' => '',
                'label' => 'Suggested for you'
                )
            );
            ?>
        </div>

        <div class="col-lg-6">
            <?php 
            woocommerce_wp_checkbox( array(
                'id' => 'woobox_offer',
                'class' => '',
                'label' => 'Offers'
                )
            );
            ?>
        </div>

        <div class="col-lg-6">
            <?php 
                 woocommerce_wp_checkbox( 
                array(
                'id' => 'woobox_you_may_like',
                'class' => '',
                'label' => 'You may like'
                ) 
            );
            ?>
        </div>
   </div>
</div>
<?php


    
}
add_action( 'woocommerce_product_quick_edit_end', 'add_custom_field_in_bulk_edit_quick_edit', 99 );


function save_custom_field_in_bulk_edit_quick_edit( $post_id, $post ){
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    if ( 'product' !== $post->post_type ) return $post_id;

    if (isset($_REQUEST['woobox_deal_of_the_day'])) {
        update_post_meta( $post_id, 'woobox_deal_of_the_day', $_REQUEST['woobox_deal_of_the_day'] );
    } else {
        delete_post_meta( $post_id, 'woobox_deal_of_the_day' );
    }
    if (isset($_REQUEST['woobox_suggested_for_you'])) {
        update_post_meta( $post_id, 'woobox_suggested_for_you', $_REQUEST['woobox_suggested_for_you'] );
    } else {
        delete_post_meta( $post_id, 'woobox_suggested_for_you' );
    }
    if (isset($_REQUEST['woobox_offer'])) {
        update_post_meta( $post_id, 'woobox_offer', $_REQUEST['woobox_offer'] );
    } else {
        delete_post_meta( $post_id, 'woobox_offer' );
    }
    if (isset($_REQUEST['woobox_you_may_like'])) {
        update_post_meta( $post_id, 'woobox_you_may_like', $_REQUEST['woobox_you_may_like'] );
    } else {
        delete_post_meta( $post_id, 'woobox_you_may_like' );
    }
   

}
add_action( 'woocommerce_product_bulk_and_quick_edit', 'save_custom_field_in_bulk_edit_quick_edit', 99, 2 );

//Product Cat creation page
function woobox_taxonomy_add_new_meta_field() {
    ?>
    <div class="form-field">
        <label for="term_meta[enable]"><?php _e('Enable', 'woobox'); ?></label>
        <input type="checkbox" value="check" name="term_meta[enable]" id="term_meta[enable]" checked>
        
    </div>
   
    <?php
}

add_action('product_cat_add_form_fields', 'woobox_taxonomy_add_new_meta_field', 10, 2);

//Product Cat Edit page
function woobox_taxonomy_edit_meta_field($term) {

    //getting term ID
     $term_id = $term->term_id;

    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option("enable_" . $term_id);
    
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[enable]"><?php _e('Enable', 'woobox'); ?></label></th>
        <td>
            <?php
            
                if((isset($term_meta['enable']) && $term_meta['enable'] == 'check')) 
                {
                    $chk = 'checked';
                }
                else
                {
                    $chk = '';
                }
            ?>

            <input type="checkbox" value="check" name="term_meta[enable]" id="term_meta[enable]" <?php echo esc_attr( $chk ); ?>>
            
        </td>
    </tr>
   
    <?php
}

add_action('product_cat_edit_form_fields', 'woobox_taxonomy_edit_meta_field', 10, 2);

// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta($term_id) {
    $term_meta = array();
    if (isset($_POST['term_meta']) && !empty($_POST['term_meta'])) {
        $term_meta = get_option("enable_" . $term_id);
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.

        
    }
    update_option("enable_" . $term_id, $term_meta);
}

add_action('edited_product_cat', 'save_taxonomy_custom_meta', 10, 2);
add_action('create_product_cat', 'save_taxonomy_custom_meta', 10, 2);

?>