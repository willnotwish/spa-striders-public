<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eme_holidays_page() {      
   global $wpdb;
   
   if (!current_user_can( get_option('eme_cap_holidays')) && (isset($_GET['eme_admin_action']) || isset($_POST['eme_admin_action']))) {
      $message = __('You have no right to update holidays!','events-made-easy');
      eme_holidays_table_layout($message);
      return;
   }
   
   if (isset($_GET['eme_admin_action']) && $_GET['eme_admin_action'] == "edit_holidays") { 
      // edit holidays  
      check_admin_referer('eme_holidays','eme_admin_nonce');
      eme_holidays_edit_layout();
      return;
   }

   // Insert/Update/Delete Record
   $holidays_table = $wpdb->prefix.HOLIDAYS_TBNAME;
   $message = '';
   if (isset($_POST['eme_admin_action'])) {
      check_admin_referer('eme_holidays','eme_admin_nonce');
      if ($_POST['eme_admin_action'] == "do_editholidays" ) {
         // holidays update required  
         $holidays = array();
         $holidays['name'] =  eme_strip_tags($_POST['name']);
         $holidays['list'] =  eme_strip_tags($_POST['list']);
         $validation_result = $wpdb->update( $holidays_table, $holidays, array('id' => intval($_POST['id'])) );
         if ($validation_result !== false) {
            $message = __("Successfully edited the list of holidays", 'events-made-easy');
         } else {
            $message = __("There was a problem editing the list of holidays, please try again.", 'events-made-easy');
         }
      } elseif ($_POST['eme_admin_action'] == "do_addholidays" ) {
         // Add a new holidays list
         $holidays = array();
         $holidays['name'] =  eme_strip_tags($_POST['name']);
         $holidays['list'] =  eme_strip_tags($_POST['list']);
         $validation_result = $wpdb->insert($holidays_table, $holidays);
         if ($validation_result !== false) {
            $message = __("Successfully added the list of holidays", 'events-made-easy');
         } else {
            $message = __("There was a problem adding the list of holidays, please try again.", 'events-made-easy');
         }
      } elseif ($_POST['eme_admin_action'] == "do_deleteholidays" && isset($_POST['holidays'])) {
         // Delete holidays
         $holidays = $_POST['holidays'];
         if (is_array($holidays) && eme_array_integers($holidays)) {
            //Run the query if we have an array of holidays ids
            if (count($holidays > 0)) {
               $validation_result = $wpdb->query( "DELETE FROM $holidays_table WHERE id IN ( ". implode(",", $holidays) .")" );
               if ($validation_result !== false)
                  $message = __("Successfully deleted the selected holiday lists.", 'events-made-easy');
               else
                  $message = __("There was a problem deleting the selected holiday lists, please try again.", 'events-made-easy');
            } else {
               $message = __("Couldn't delete the holiday lists. Incorrect IDs supplied. Please try again.", 'events-made-easy');
            }
         } else {
            $message = __("Couldn't delete the holiday lists. Incorrect IDs supplied. Please try again.",'events-made-easy');
         }
      }
   }
   eme_holidays_table_layout($message);
} 

function eme_holidays_table_layout($message = "") {
   $holidays = eme_get_holiday_lists();
   $destination = admin_url("admin.php?page=eme-holidays"); 
   $nonce_field = wp_nonce_field('eme_holidays','eme_admin_nonce',false,false);
   $table = "
      <div class='wrap nosubsub'>\n
         <div id='icon-edit' class='icon32'>
            <br />
         </div>
         <h1>".__('Holidays', 'events-made-easy')."</h1>\n ";   
         
         if($message != "") {
            $table .= "
            <div id='message' class='updated fade below-h1' style='background-color: rgb(255, 251, 204);'>
               <p>$message</p>
            </div>";
         }
         
         $table .= "
         <div id='col-container'>
         
            <?-- begin col-right -->
            <div id='col-right'>
             <div class='col-wrap'>
                <form id='bookings-filter' method='post' action='$destination'>
                  <input type='hidden' name='eme_admin_action' value='do_deleteholidays' />";
                  $table .= $nonce_field;
                  if (count($holidays)>0) {
                     $table .= "<table class='widefat'>
                        <thead>
                           <tr>
                              <th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1' /></th>
                              <th>".__('ID', 'events-made-easy')."</th>
                              <th>".__('Name', 'events-made-easy')."</th>
                           </tr>
                        </thead>
                        <tfoot>
                           <tr>
                              <th class='manage-column column-cb check-column' scope='col'><input type='checkbox' class='select-all' value='1' /></th>
                              <th>".__('ID', 'events-made-easy')."</th>
                              <th>".__('Name', 'events-made-easy')."</th>
                           </tr>
                        </tfoot>
                        <tbody>";
                     foreach ($holidays as $this_holidays) {
                        $table .= "    
                           <tr>
                           <td><input type='checkbox' class ='row-selector' value='".$this_holidays['id']."' name='holidays[]' /></td>
                           <td><a href='".wp_nonce_url(admin_url("admin.php?page=eme-holidays&amp;eme_admin_action=edit_holidays&amp;id=".$this_holidays['id']),'eme_holidays','eme_admin_nonce')."'>".$this_holidays['id']."</a></td>
                           <td><a href='".wp_nonce_url(admin_url("admin.php?page=eme-holidays&amp;eme_admin_action=edit_holidays&amp;id=".$this_holidays['id']),'eme_holidays','eme_admin_nonce')."'>".eme_trans_sanitize_html($this_holidays['name'])."</a></td>
                           </tr>
                        ";
                     }
                     $delete_text=__("Are you sure you want to delete these holiday lists?",'events-made-easy');
                     $delete_button_text=__("Delete",'events-made-easy');
                     $table .= <<<EOT
                        </tbody>
                     </table>
   
                     <div class='tablenav'>
                        <div class='alignleft actions'>
                        <input class='button-primary action' type='submit' name='doaction' value='$delete_button_text' onclick="return areyousure('$delete_text');" />
                        <br class='clear'/>
                        </div>
                        <br class='clear'/>
                     </div>
EOT;
                  } else {
                        $table .= "<p>".__('No holiday lists have been inserted yet!', 'events-made-easy');
                  }
                   $table .= "
                  </form>
               </div>
            </div> 
            <?-- end col-right -->
            
            <?-- begin col-left -->
            <div id='col-left'>
            <div class='col-wrap'>
                  <div class='form-wrap'>
                     <div id='ajax-response'/>
                  <h3>".__('Add holidays list', 'events-made-easy')."</h3>
                      <form name='add' id='add' method='post' action='$destination' class='add:the-list: validate'>
                        <input type='hidden' name='eme_admin_action' value='do_addholidays' />
                        $nonce_field
                         <div class='form-field form-required'>
                           <label for='name'>".__('Holidays listname', 'events-made-easy')."</label>
                           <input name='name' id='name' type='text' value='' size='40' />
                            <p>".__('The name of the holidays list', 'events-made-easy')."</p>
                            <label for='list'>".__('List of holidays', 'events-made-easy')."</label>
                            <textarea name='list' id='list' rows='5' /></textarea>
                 <p>".__('Basic format: YYYY-MM-DD, one per line','events-made-easy').'<br/>'.__('For more information about holidays, see ', 'events-made-easy')."<a target='_blank' href='http://www.e-dynamics.be/wordpress/?cat=6086'>".__('the documentation', 'events-made-easy')."</a></p>
                         </div>
                         <p class='submit'><input type='submit' class='button-primary' name='submit' value='".__('Add list of holidays', 'events-made-easy')."' /></p>
                      </form>
                 </div>
               </div>
            </div>
            <?-- end col-left -->
         </div>
   </div>";
   echo $table;  
}

function eme_holidays_edit_layout($message = "") {
   $holidays_id = intval($_GET['id']);
   $holidays = eme_get_holiday_list($holidays_id);
   $nonce_field = wp_nonce_field('eme_holidays','eme_admin_nonce',false,false);
   $layout = "
   <div class='wrap'>
      <div id='icon-edit' class='icon32'>
         <br />
      </div>
         
      <h1>".__('Edit holidays list', 'events-made-easy')."</h1>";   
      
      if($message != "") {
         $layout .= "
      <div id='message' class='updated fade below-h1' style='background-color: rgb(255, 251, 204);'>
         <p>$message</p>
      </div>";
      }
      $layout .= "
      <div id='ajax-response'></div>

      <form name='edit_holidays' id='edit_holidays' method='post' action='".admin_url("admin.php?page=eme-holidays")."' class='validate'>
      <input type='hidden' name='eme_admin_action' value='do_editholidays' />
      <input type='hidden' name='id' value='".$holidays['id']."' />
      $nonce_field
      <table class='form-table'>
            <tr class='form-field form-required'>
               <th scope='row' valign='top'><label for='name'>".__('Holidays listname', 'events-made-easy')."</label></th>
               <td><input name='name' id='name' type='text' value='".eme_sanitize_html($holidays['name'])."' size='40' /><br />
                 ".__('The name of the holidays list', 'events-made-easy')."</td>
            </tr>
            <tr class='form-field form-required'>
               <th scope='row' valign='top'><label for='description'>".__('Holidays list', 'events-made-easy')."</label></th>
               <td><textarea name='list' id='description' rows='5' />".eme_sanitize_html($holidays['list'])."</textarea><br />
                 ".__('Basic format: YYYY-MM-DD, one per line','events-made-easy').'<br/>'.__('For more information about holidays, see ', 'events-made-easy')."<a target='_blank' href='http://www.e-dynamics.be/wordpress/?cat=6086'>".__('the documentation', 'events-made-easy')."</a></td>
            </tr>
         </table>
      <p class='submit'><input type='submit' class='button-primary' name='submit' value='".__('Update list of holidays', 'events-made-easy')."' /></p>
      </form>
   </div>
   ";  
   echo $layout;
}

function eme_get_holiday_lists() { 
   global $wpdb;
   $holidays_table = $wpdb->prefix.HOLIDAYS_TBNAME; 
   $sql = "SELECT id,name FROM $holidays_table";
   return $wpdb->get_results($sql, ARRAY_A);
}
function eme_get_holiday_list($id) { 
   global $wpdb;
   $holidays_table = $wpdb->prefix.HOLIDAYS_TBNAME; 
   $sql = $wpdb->prepare("SELECT * FROM $holidays_table WHERE id = %d",$id);
   return $wpdb->get_row($sql, ARRAY_A);
}

function eme_get_holiday_listinfo($id) {
   $holiday_list = eme_get_holiday_list($id);
   $res_days = array();
   $days=explode("\n", str_replace("\r","\n",$holiday_list['list']));
   foreach ($days as $day_info) {
      //$info=explode(',',$day_info);
      list($day,$name,$class)=array_pad(explode(',',$day_info),3,'');
      $res_days[$day]['name']=$name;
      $res_days[$day]['class']=$class;
   }
   return $res_days;
}

function eme_get_holidays_array_by_id() {
   $holidays = eme_get_holiday_lists();
   $holidays_by_id=array();
   if (!empty($holidays)) {
      $holidays_by_id[]='';
      foreach ($holidays as $holiday_list) {
         $holidays_by_id[$holiday_list['id']]=$holiday_list['name'];
      }
   }
   return $holidays_by_id;
}

# return number of days until next event or until the specified event
function eme_holidays_shortcode($atts) {
   global $eme_timezone;
   extract ( shortcode_atts ( array ('id'=>''), $atts ) );

   if ($id!="") {
      $holidays_list=eme_get_holiday_list(intval($id));
   } else {
      return;
   }

   $days=explode("\n", str_replace("\r","\n",$holiday_list['list']));
   print '<div id="eme_holidays_list">';
   foreach ($days as $day_info) {
      list($day,$name,$class)=array_pad(explode(',',$day_info),3,'');
      print '<span id="eme_holidays_date">'.eme_localised_date($day." ".$eme_timezone).'</span>';
      print '&nbsp; <span id="eme_holidays_name">'.eme_trans_sanitize_html($name).'</span><br />';
   }
   print '</div>';
}


?>
