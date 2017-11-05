<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eme_attributes_form($eme_array) {
   $eme_data = array();
   if (isset($eme_array['event_attributes'])) {
	   $eme_data = $eme_array['event_attributes'];
   } elseif (isset($eme_array['location_attributes'])) {
	   $eme_data = $eme_array['location_attributes'];
   }
   //We also get a list of attribute names and create a ddm list (since placeholders are fixed)
   $formats = 
      get_option('eme_event_list_item_format' ).
      get_option('eme_event_page_title_format' ).
      get_option('eme_full_calendar_event_format' ).
      get_option('eme_location_event_list_item_format' ).
      get_option('eme_ical_title_format' ).
      get_option('eme_ical_description_format' ).
      get_option('eme_rss_description_format' ).
      get_option('eme_rss_title_format' ).
      get_option('eme_single_event_format' ).
      get_option('eme_small_calendar_event_title_format' ).
      get_option('eme_single_location_format' ).
      get_option('eme_contactperson_email_body' ).
      get_option('eme_contactperson_cancelled_email_body' ).
      get_option('eme_contactperson_pending_email_body' ).
      get_option('eme_respondent_email_body' ).
      get_option('eme_registration_pending_email_body' ).
      get_option('eme_registration_denied_email_body' ).
      get_option('eme_registration_cancelled_email_body' ).
      get_option('eme_registration_form_format' ).
      get_option('eme_attendees_list_format' ).
      get_option('eme_payment_form_header_format' ).
      get_option('eme_payment_form_footer_format' ).
      get_option('eme_multipayment_form_header_format' ).
      get_option('eme_multipayment_form_footer_format' ).
      get_option('eme_bookings_list_format' );
      #get_option('eme_location_baloon_format' ).
      #get_option('eme_location_page_title_format' ).

   // include all templates as well
   $templates = eme_get_templates();
   foreach ($templates as $template) {
      $formats .= $template['format'];
   }

   if (isset($eme_array['event_properties'])) {
      foreach ($eme_array['event_properties'] as $key=>$val) {
         if (strstr($key,"_tpl"))
            $formats .= $val;
      }
   }
   if (isset($eme_array['location_properties'])) {
      foreach ($eme_array['location_properties'] as $key=>$val) {
         if (strstr($key,"_tpl"))
            $formats .= $val;
      }
   }

   //We now have one long string of formats
   preg_match_all("/#(ESC|URL)?_ATT\{.+?\}(\{.+?\})?/", $formats, $placeholders);

   $attributes = array();
   //Now grab all the unique attributes we can use in our event or location.
   foreach($placeholders[0] as $result) {
      $result = str_replace("#ESC","#",$result);
      $result = str_replace("#URL","#",$result);
      $attribute = substr( substr($result, 0, strpos($result, '}')), 6 );
      if( !in_array($attribute, $attributes) ){       
         $attributes[] = $attribute ;
      }
   }
   ?>
   <div class="wrap">
   <?php if( count( $attributes ) > 0 ) { ?> 
      <p><?php _e('Add attributes here','events-made-easy'); ?></p>
      <table class="form-table">
         <thead>
            <tr valign="top">
               <td><strong><?php _e('Attribute Name','events-made-easy'); ?></strong></td>
               <td><strong><?php _e('Value','events-made-easy'); ?></strong></td>
            </tr>
         </thead>    
         <tfoot>
            <tr valign="top">
               <td colspan="3"><a href="#" id="mtm_add_tag"><?php _e('Add new tag','events-made-easy'); ?></a></td>
            </tr>
         </tfoot>
         <tbody id="mtm_body">
            <?php
            $count = 1;
            if( is_array($eme_data) and count($eme_data) > 0){
               foreach( $eme_data as $name => $value){
                  ?>
                  <tr valign="top" id="mtm_<?php echo $count ?>">
                     <td scope="row">
                        <select name="mtm_<?php echo $count ?>_ref">
                           <?php
                           if( !in_array($name, $attributes) ){
                              echo "<option value='$name'>$name (".__('Not defined in templates', 'events-made-easy').")</option>";
                           }
                           foreach( $attributes as $attribute ){
                              if( $attribute == $name ) {
                                 echo "<option selected='selected'>$attribute</option>";
                              }else{
                                 echo "<option>$attribute</option>";
                              }
                           }
                           ?>
                        </select>
                        <a href="#" rel="<?php echo $count ?>"><?php _e('Remove','events-made-easy'); ?></a>
                     </td>
                     <td>
			<textarea rows="2" cols="40" id="mtm_<?php echo $count; ?>_id" name="mtm_<?php echo $count; ?>_name"><?php echo eme_sanitize_html($value); ?></textarea>
                     </td>
                  </tr>
                  <?php
                  $count++;
               }
            }else{
               if( count( $attributes ) > 0 ){
                  ?>
                  <tr valign="top" id="mtm_<?php echo $count ?>">
                     <td scope="row">
                        <select name="mtm_<?php echo $count ?>_ref">
                           <?php
                           foreach( $attributes as $attribute ){
                              echo "<option>$attribute</option>";
                           }
                           ?>
                        </select>
                        <a href="#" rel="<?php echo $count ?>"><?php _e('Remove','events-made-easy'); ?></a>
                     </td>
                     <td>
			<textarea rows="2" cols="40" id="mtm_<?php echo $count; ?>_id" name="mtm_<?php echo $count; ?>_name"></textarea>
                     </td>
                  </tr>
                  <?php
               }else{
                  ?>
                  <tr valign="top">
                     <td scope="row" colspan='2'>
                     <?php _e('In order to use attributes, you must define some in your templates, otherwise they\'ll never show. Go to Events > Settings to add attribute placeholders.', 'events-made-easy'); ?>
                     </td>
                  </tr>
                  <?php
                  
               }
            }
            ?>
         </tbody>
      </table>
   <?php
   } else {
   ?>
      <p><?php _e('No attributes defined yet. If you want attributes, you first need to define/use some in the Settings page. See the section about custom attributes on the documention site for more info.','events-made-easy'); ?></p>
   <?php
   } //endif count attributes
   ?>
   </div>
<?php
}
?>
