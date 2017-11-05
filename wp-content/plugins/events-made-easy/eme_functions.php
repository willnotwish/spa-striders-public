<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eme_client_clock_callback() {
   global $eme_timezone;
   eme_session_start();
   // Set php clock values in an array
   $phptime_obj = new ExpressiveDate(null,$eme_timezone);
   // if clock data not set
   if (!isset($_SESSION['eme_client_unixtime'])) {
      // Preset php clock values in client session variables for fall-back if valid client clock data isn't received.
      $_SESSION['eme_client_clock_valid'] = false; // Will be set true if all client clock data passes sanity tests
      $_SESSION['eme_client_php_difference'] = 0; // Client-php clock difference integer seconds
      $_SESSION['eme_client_unixtime'] = (int) $phptime_obj->format('U'); // Integer seconds since 1/1/1970 @ 12:00 AM
      $_SESSION['eme_client_seconds'] = (int) $phptime_obj->format('s'); // Integer second this minute (0-59)
      $_SESSION['eme_client_minutes'] = (int) $phptime_obj->format('i'); // Integer minute this hour (0-59)
      $_SESSION['eme_client_hours'] = (int) $phptime_obj->format('h'); // Integer hour this day (0-23)
      $_SESSION['eme_client_wday'] = (int) $phptime_obj->format('w'); // Integer day this week (0-6), 0 = Sunday, ... , 6 = Saturday
      $_SESSION['eme_client_mday'] = (int) $phptime_obj->format('j'); // Integer day this month 1-31)
      $_SESSION['eme_client_month'] = (int) $phptime_obj->format('n'); // Integer month this year (1-12)
      $_SESSION['eme_client_fullyear'] = (int) $phptime_obj->format('Y'); // Integer year (1970-9999)
      $ret = '1'; // reload from server
   } else {
      $ret = '0';
   }
   
   // Cast client clock values as integers to avoid mathematical errors and set in temporary local variables.
   $client_unixtime = (int) $_POST['client_unixtime'];
   $client_seconds = (int) $_POST['client_seconds'];
   $client_minutes = (int) $_POST['client_minutes'];
   $client_hours = (int) $_POST['client_hours'];
   $client_wday = (int) $_POST['client_wday'];
   $client_mday = (int) $_POST['client_mday'];
   $client_month = (int) $_POST['client_month'];
   $client_fullyear = (int) $_POST['client_fullyear'];
   
   // Client clock sanity tests
   $valid = true;
   if (abs($client_unixtime - $_SESSION['eme_client_unixtime']) > 300) $valid = false; // allow +/-5 min difference
   if (abs($client_seconds - 30) > 30) $valid = false; // Seconds <0 or >60
   if (abs($client_minutes - 30) > 30) $valid = false; // Minutes <0 or >60
   if (abs($client_hours - 12) > 12) $valid = false; // Hours <0 or >24
   if (abs($client_wday - 3) > 3) $valid = false; // Weekday <0 or >6
   if (abs($client_mday - $_SESSION['eme_client_mday']) > 30) $valid = false; // >30 day difference
   if (abs($client_month - $_SESSION['eme_client_month']) > 11) $valid = false; // >11 month difference
   if (abs($client_fullyear - $_SESSION['eme_client_fullyear']) > 1) $valid = false; // >1 year difference

   // To insure mutual consistency, don't use any client values unless they all passed the tests.
   if ($valid) {
      $_SESSION['eme_client_unixtime'] = $client_unixtime;
      $_SESSION['eme_client_seconds'] = $client_seconds;
      $_SESSION['eme_client_minutes'] = $client_minutes;
      $_SESSION['eme_client_hours'] = $client_hours;
      $_SESSION['eme_client_wday'] = $client_wday;
      $_SESSION['eme_client_mday'] = $client_mday;
      $_SESSION['eme_client_month'] = $client_month;
      $_SESSION['eme_client_fullyear'] = $client_fullyear;
      $_SESSION['eme_client_clock_valid'] = true;
      // Set  date & time clock strings
      $client_clock_str = "$client_fullyear-$client_month-$client_mday $client_hours:$client_minutes:$client_seconds";
      $client_clock_obj = new ExpressiveDate($client_clock_str,$eme_timezone);
      $_SESSION['eme_client_php_difference'] = (int) $client_clock_obj->getDifferenceInSeconds($phptime_obj);
   }
   
   // it is an ajax instance: echo the result
   echo $ret;
}

function eme_captcha_generate() {
        eme_session_start();
	if (isset($_GET['sessionvar']))
		$sessionvar=$_GET['sessionvar'];
	else
		$sessionvar='captcha';

	// 23 letters
	$alfabet="abcdefghjkmnpqrstuvwxyz";
	$random1 = substr($alfabet,rand(1,23)-1,1);
	$random2 = rand(2,9);
	$rand=rand(1,23)-1;
	$random3 = substr($alfabet,rand(1,23)-1,1);
	$random4 = rand(2,9);
	$rand=rand(1,23)-1;
	$random5 = substr($alfabet,rand(1,23)-1,1);

	$randomtext=$random1.$random2.$random3.$random4.$random5;
	$_SESSION[$sessionvar] = md5($randomtext);

	$im = imagecreatetruecolor(120, 38);

	// some colors
	$white = imagecolorallocate($im, 255, 255, 255);
	$grey = imagecolorallocate($im, 128, 128, 128);
	$black = imagecolorallocate($im, 0, 0, 0);
	$red = imagecolorallocate($im, 255, 0, 0);
	$blue = imagecolorallocate($im, 0, 0, 255);
	$green = imagecolorallocate($im, 0, 255, 0);
	$background_colors=array($red,$blue,$green,$black);

	// draw rectangle in random color
	$background_color=$background_colors[rand(0,3)];
	imagefilledrectangle($im, 0, 0, 120, 38, $background_color);

	// replace font.ttf with the location of your own ttf font file
	$font = EME_PLUGIN_DIR.'/font.ttf';

	// add shadow
	imagettftext($im, 25, 8, 15, 28, $grey, $font, $random1);
	imagettftext($im, 25, -8, 35, 28, $grey, $font, $random2);
	imagettftext($im, 25, 8, 55, 28, $grey, $font, $random3);
	imagettftext($im, 25, -8, 75, 28, $grey, $font, $random4);
	imagettftext($im, 25, 8, 95, 28, $grey, $font, $random5);

	// add text
	imagettftext($im, 25, 8, 8, 30, $white, $font, $random1);
	imagettftext($im, 25, -8, 28, 30, $white, $font, $random2);
	imagettftext($im, 25, 8, 48, 30, $white, $font, $random3);
	imagettftext($im, 25, -8, 68, 30, $white, $font, $random4);
	imagettftext($im, 25, 8, 88, 30, $white, $font, $random5);

	// give image back
	header ("Content-type: image/gif");
	imagegif($im);
	imagedestroy($im);
	exit;
}

function eme_check_captcha($post_var,$session_var="",$cleanup=1) {
   if (empty($session_var))
      $session_var="captcha";
   $eme_captcha_no_case=get_option('eme_captcha_no_case');
   if (!isset($_POST[$post_var]) || !isset($_SESSION[$session_var]) ||
       (!$eme_captcha_no_case && md5($_POST[$post_var]) != $_SESSION[$session_var]) ||
       ($eme_captcha_no_case && md5(strtolower($_POST[$post_var])) != strtolower($_SESSION[$session_var]))) {
      return __('You entered an incorrect code. Please fill in the correct code.', 'events-made-easy');
   } else {
      if ($cleanup==1) {
         unset($_SESSION[$session_var]);
         unset($_POST[$post_var]);
      }
      return ('');
   }
}

function eme_if_shortcode($atts,$content) {
   extract ( shortcode_atts ( array ('tag' => '', 'value' => '', 'notvalue' => '', 'lt' => '', 'le' => '',  'gt' => '', 'ge' => '', 'contains'=>'', 'notcontains'=>'', 'is_empty'=>0 ), $atts ) );
   if ($is_empty) {
      if (empty($tag)) return do_shortcode($content);
   } elseif (is_numeric($value) || !empty($value)) {
      if ($tag==$value) return do_shortcode($content);
   } elseif (is_numeric($notvalue) || !empty($notvalue)) {
      if ($tag!=$notvalue) return do_shortcode($content);
   } elseif (is_numeric($lt) || !empty($lt)) {
      if ($tag<$lt) return do_shortcode($content);
   } elseif (is_numeric($le) || !empty($le)) {
      if ($tag<=$le) return do_shortcode($content);
   } elseif (is_numeric($gt) || !empty($gt)) {
      if ($tag>$gt) return do_shortcode($content);
   } elseif (is_numeric($ge) || !empty($ge)) {
      if ($tag>=$ge) return do_shortcode($content);
   } elseif (is_numeric($contains) || !empty($contains)) {
      if (strpos($tag,"$contains")!== false) return do_shortcode($content);
   } elseif (is_numeric($notcontains) || !empty($notcontains)) {
      if (strpos($tag,"$notcontains")===false) return do_shortcode($content);
   } else {
      if (!empty($tag)) return do_shortcode($content);
   }
}

function eme_for_shortcode($atts,$content) {
   extract ( shortcode_atts ( array ('min' => 1, 'max' => 0), $atts ) );
   $min = intval($min);
   $max = intval($max);
   $result="";
   while ($min <= $max) {
      $result .= do_shortcode($content);
      $min++;
   }
   return $result;
}


// Returns true if the page in question is the events page
function eme_is_events_page() {
   $events_page_id = eme_get_events_page_id();
   if ($events_page_id) {
      return is_page ( $events_page_id );
   } else {
      return false;
   }
}

function eme_get_events_page_id() {
   return get_option('eme_events_page');
}

function eme_get_events_page($justurl = 1, $text = '') {
   $events_page_id = eme_get_events_page_id();
   $page_link = get_permalink ($events_page_id);
   if ($justurl || empty($text)) {
      $result = $page_link;
   } else {
      $text = eme_sanitize_html($text);
      $result = "<a href='$page_link' title='$text'>$text</a>";
   }
   return $result;
}

function eme_is_single_day_page() {
   return (eme_is_events_page () && get_query_var('calendar_day'));
}

function eme_is_single_event_page() {
   return (eme_is_events_page () && get_query_var('event_id'));
}

function eme_is_multiple_events_page() {
   return (eme_is_events_page () && get_query_var('event_id'));
}

function eme_is_single_location_page() {
   return (eme_is_events_page () && get_query_var('location_id'));
}

function eme_is_multiple_locations_page() {
   return (eme_is_events_page () && get_query_var('location_id'));
}

function eme_get_contact($event) {
   if (isset($event['event_contactperson_id']) && $event['event_contactperson_id'] >0 )
      $contact_id = $event['event_contactperson_id'];
   else
      $contact_id = get_option('eme_default_contact_person');
   // suppose the user has been deleted ...
   if (!get_userdata($contact_id)) $contact_id = get_option('eme_default_contact_person');
   if ($contact_id < 1 && isset($event['event_author']))
      $contact_id = $event['event_author'];
   if ($contact_id < 1) {
      if (function_exists('is_multisite') && is_multisite()) {
         $thisblog = get_current_blog_id();
         $userinfo = get_user_by('email', get_blog_option($thisblog, 'admin_email'));
      } else {
         $userinfo = get_user_by('email', get_option('admin_email'));
      }
      #$contact_id = get_current_user_id();
   } else {
      $userinfo=get_userdata($contact_id);
   }
   return $userinfo;
}

function eme_get_author($event) {
   $author_id = $event['event_author'];
   if ($author_id < 1) {
      if (function_exists('is_multisite') && is_multisite()) {
         $thisblog = get_current_blog_id();
         $userinfo = get_user_by('email', get_blog_option($thisblog, 'admin_email'));
      } else {
         $userinfo = get_user_by('email', get_option('admin_email'));
      }
      #$contact_id = get_current_user_id();
   } else {
      $userinfo=get_userdata($author_id);
   }
   return $userinfo;
}

function eme_get_user_phone($user_id) {
   return get_user_meta($user_id, 'eme_phone',true);
}

// got from http://davidwalsh.name/php-email-encode-prevent-spam
function eme_ascii_encode($e) {
    $output = "";
    if (has_filter('eme_email_obfuscate_filter')) {
       $output=apply_filters('eme_email_obfuscate_filter',$e);
    } else {
       for ($i = 0; $i < strlen($e); $i++) { $output .= '&#'.ord($e[$i]).';'; }
    }
    return $output;
}

function eme_permalink_convert ($val) {
   // WP provides a function to convert accents to their ascii counterparts
   // called remove_accents, but we also want to replace spaces with "-"
   // and trim the last space. sanitize_title_with_dashes does all that
   // and then, add a trailing slash
   $val = sanitize_title_with_dashes(remove_accents($val));
   return trailingslashit($val);
}

function eme_event_url($event,$language="") {
   global $wp_rewrite;

   $def_language = eme_detect_lang();
   if (empty($language))
         $language = $def_language;
   if ($event['event_url'] != '' && get_option('eme_use_external_url')) {
      $the_link = $event['event_url'];
      $parsed = parse_url($the_link);
      if (empty($parsed['scheme'])) {
          $the_link = 'http://' . ltrim($the_link, '/');
      }
      $the_link = esc_url($the_link);
   } else {
      if (isset($wp_rewrite) && $wp_rewrite->using_permalinks() && get_option('eme_seo_permalink')) {
         $events_prefix=eme_permalink_convert(get_option ( 'eme_permalink_events_prefix'));
         $slug = $event['event_slug'] ? $event['event_slug'] : $event['event_name'];
         $name=$events_prefix.$event['event_id']."/".eme_permalink_convert($slug);
         $the_link = home_url();
         // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
         $the_link = preg_replace("/\/$def_language$/","",$the_link);
         $the_link = trailingslashit(remove_query_arg('lang',$the_link));
         if (!empty($language)) {
            $url_mode=eme_lang_url_mode();
            if ($url_mode==2) {
               $the_link = $the_link."$language/".user_trailingslashit($name);
            } else {
               $the_link = $the_link.user_trailingslashit($name);
               $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
            }
         } else {
            $the_link = $the_link.user_trailingslashit($name);
         }

      } else {
         $the_link = eme_get_events_page();
         // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
         $the_link = remove_query_arg('lang',$the_link);
         $the_link = add_query_arg( array( 'event_id' => $event['event_id'] ), $the_link );
         if (!empty($language))
            $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
      }
   }
   return $the_link;
}

function eme_location_url($location,$language="") {
   global $wp_rewrite;

   $def_language = eme_detect_lang();
   if (empty($language))
         $language = $def_language;
   $the_link = "";
   if ($location['location_url'] != '' && get_option('eme_use_external_url')) {
      $the_link = $location['location_url'];
      $parsed = parse_url($the_link);
      if (empty($parsed['scheme'])) {
          $the_link = 'http://' . ltrim($the_link, '/');
      }
      $the_link = esc_url($the_link);
   } else {
      $url_mode=eme_lang_url_mode();
      if (isset($location['location_id']) && isset($location['location_name'])) {
         if (isset($wp_rewrite) && $wp_rewrite->using_permalinks() && get_option('eme_seo_permalink')) {
            $locations_prefix=eme_permalink_convert(get_option ( 'eme_permalink_locations_prefix'));
            $slug = $location['location_slug'] ? $location['location_slug'] : $location['location_name'];
            $name=$locations_prefix.$location['location_id']."/".eme_permalink_convert($slug);
            $the_link = home_url();
            // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
            $the_link = preg_replace("/\/$def_language$/","",$the_link);
            $the_link = trailingslashit(remove_query_arg('lang',$the_link));
            if (!empty($language)) {
               $url_mode=eme_lang_url_mode();
               if ($url_mode==2) {
                  $the_link = $the_link."$language/".user_trailingslashit($name);
               } else {
                  $the_link = $the_link.user_trailingslashit($name);
                  $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
               }
            } else {
               $the_link = $the_link.user_trailingslashit($name);
            }
         } else {
            $the_link = eme_get_events_page();
            // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
            $the_link = remove_query_arg('lang',$the_link);
            $the_link = add_query_arg( array( 'location_id' => $location['location_id'] ), $the_link );
            if (!empty($language))
               $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
         }
      }
   }
   return $the_link;
}

function eme_calendar_day_url($day) {
   global $wp_rewrite;

   $def_language = eme_detect_lang();
   $language = $def_language;

   if (isset($wp_rewrite) && $wp_rewrite->using_permalinks() && get_option('eme_seo_permalink')) {
      $events_prefix=eme_permalink_convert(get_option ( 'eme_permalink_events_prefix'));
      $name=$events_prefix.eme_permalink_convert($day);
      $the_link = home_url();
      // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
      $the_link = preg_replace("/\/$def_language$/","",$the_link);
      $the_link = trailingslashit(remove_query_arg('lang',$the_link));
      if (!empty($language)) {
         $url_mode=eme_lang_url_mode();
         if ($url_mode==2) {
            $the_link = $the_link."$language/".user_trailingslashit($name);
         } else {
            $the_link = $the_link.user_trailingslashit($name);
            $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
         }
      } else {
         $the_link = $the_link.user_trailingslashit($name);
      }
   } else {
      $the_link = eme_get_events_page();
      // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
      $the_link = remove_query_arg('lang',$the_link);
      $the_link = add_query_arg( array( 'calendar_day' => $day ), $the_link );
      if (!empty($language))
         $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
   }
   return $the_link;
}

function eme_payment_url($payment_id) {
   global $wp_rewrite;

   $def_language = eme_detect_lang();
   $language = $def_language;
   if (isset($wp_rewrite) && $wp_rewrite->using_permalinks() && get_option('eme_seo_permalink')) {
      $events_prefix=eme_permalink_convert(get_option ( 'eme_permalink_events_prefix'));
      $name=$events_prefix."p$payment_id";
      $the_link = home_url();
      // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
      $the_link = preg_replace("/\/$def_language$/","",$the_link);
      $the_link = trailingslashit(remove_query_arg('lang',$the_link));
      if (!empty($language)) {
         $url_mode=eme_lang_url_mode();
         if ($url_mode==2) {
            $the_link = $the_link."$language/".user_trailingslashit($name);
         } else {
            $the_link = $the_link.user_trailingslashit($name);
            $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
         }
      } else {
         $the_link = $the_link.user_trailingslashit($name);
      }
   } else {
      $the_link = eme_get_events_page();
      // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
      $the_link = remove_query_arg('lang',$the_link);
      $the_link = add_query_arg( array( 'eme_pmt_id' => $payment_id ), $the_link );
      if (!empty($language))
         $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
   }
   return $the_link;
}

function eme_category_url($category) {
   global $wp_rewrite;

   $def_language = eme_detect_lang();
   $language = $def_language;
   if (isset($wp_rewrite) && $wp_rewrite->using_permalinks() && get_option('eme_seo_permalink')) {
      $events_prefix=eme_permalink_convert(get_option ( 'eme_permalink_events_prefix'));
      $slug = $category['category_slug'] ? $category['category_slug'] : $category['category_name'];
      $name=$events_prefix."cat/".eme_permalink_convert($slug);
      $the_link = home_url();
      // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
      $the_link = preg_replace("/\/$def_language$/","",$the_link);
      $the_link = trailingslashit(remove_query_arg('lang',$the_link));
      if (!empty($language)) {
         $url_mode=eme_lang_url_mode();
         if ($url_mode==2) {
            $the_link = $the_link."$language/".user_trailingslashit($name);
         } else {
            $the_link = $the_link.user_trailingslashit($name);
            $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
         }
      } else {
         $the_link = $the_link.user_trailingslashit($name);
      }
   } else {
      $the_link = eme_get_events_page();
      // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
      $the_link = remove_query_arg('lang',$the_link);
      $slug = $category['category_slug'] ? $category['category_slug'] : $category['category_name'];
      $the_link = add_query_arg( array( 'eme_event_cat' => $slug ), $the_link );
      if (!empty($language))
         $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
   }
   return $the_link;
}

function eme_payment_return_url($event,$payment,$resultcode) {
   $the_link=eme_event_url($event);
   if (get_option('eme_payment_show_custom_return_page')) {
      if ($resultcode==1) {
         $res="succes";
      } else {
         $res="fail";
      }
      $the_link = add_query_arg( array( 'eme_pmt_result' => $res ), $the_link );
      $the_link = add_query_arg( array( 'event_id' => $event['event_id'] ), $the_link );
      if (get_option('eme_payment_add_bookingid_to_return'))
         $the_link = add_query_arg( array( 'eme_pmt_id' => $payment['id'] ), $the_link );
   }
   return $the_link;
}

function eme_cancel_url($payment_randomid) {
   $def_language = eme_detect_lang();
   $language = $def_language;

   $the_link = eme_get_events_page();
   // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
   $the_link = remove_query_arg('lang',$the_link);
   $the_link = add_query_arg( array( 'eme_cancel_booking' => $payment_randomid ), $the_link );
   if (!empty($language))
	   $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
   return $the_link;
}

function eme_captcha_url($sessionvar) {
   $the_link = "";
   $the_link = add_query_arg( array( 'eme_captcha' => 'generate','sessionvar' => $sessionvar ), $the_link );
   return $the_link;
}

function eme_attendees_report_link($title,$scope,$category,$notcategory,$event_template_id,$attend_template_id) {
   if (!is_user_logged_in()) return;

   $def_language = eme_detect_lang();
   $language = $def_language;

   $the_link = "";
   // some plugins add the lang info to the home_url, remove it so we don't get into trouble or add it twice
   $the_link = remove_query_arg('lang',$the_link);
   $the_link = add_query_arg( array( 'eme_attendees' => 'report' ), $the_link );
   $the_link = add_query_arg( array( 'scope' => esc_attr($scope) ), $the_link );
   $the_link = add_query_arg( array( 'event_template_id' => esc_attr($event_template_id) ), $the_link );
   $the_link = add_query_arg( array( 'attend_template_id' => esc_attr($attend_template_id) ), $the_link );
   $the_link = add_query_arg( array( 'category' => esc_attr($category) ), $the_link );
   $the_link = add_query_arg( array( 'notcategory' => esc_attr($notcategory) ), $the_link );
   if (!empty($language))
	   $the_link = add_query_arg( array( 'lang' => $language ), $the_link );
   return "<a href='$the_link' title='$title'>".$title."</a>";
}

function eme_check_event_exists($event_id) {
   global $wpdb;
   $events_table = $wpdb->prefix.EVENTS_TBNAME;
   $sql = "SELECT COUNT(*) from $events_table WHERE event_id='".$event_id."'";
   return $wpdb->get_var($sql);
}

function eme_check_location_exists($location_id) {
   global $wpdb;
   $locations_table = $wpdb->prefix.LOCATIONS_TBNAME;
   $sql = "SELECT COUNT(*) from $locations_table WHERE location_id='".$location_id."'";
   return $wpdb->get_var($sql);
}

function _eme_are_dates_valid($date) {
   // if it is a series of dates
   if (strstr($date, ',')) {
	$dates=explode(',',$date);
   	foreach ( $dates as $date ) {
		if (!_eme_is_date_valid($date)) return false;
	}
   }
   return true;
}
	
function _eme_is_date_valid($date) {
   if (strlen($date) != 10)
      return false;
   $year = intval(substr ( $date, 0, 4 ));
   $month = intval(substr ( $date, 5, 2 ));
   $day = intval(substr ( $date, 8 ));
   return (checkdate ( $month, $day, $year ));
}

function eme_is_time_valid($time) {
   $result = preg_match ( "/([01]\d|2[0-3])(:[0-5]\d)/", $time );
   return ($result);
}

function eme_capNamesCB ( $cap ) {
   $cap = str_replace('_', ' ', $cap);
   $cap = ucfirst($cap);
   return $cap;
}
function eme_get_all_caps() {
   global $wp_roles;
   $caps = array();
   $capabilities = array();

   foreach ( $wp_roles->roles as $role ) {
      if ($role['capabilities']) {
         foreach ( $role['capabilities'] as $cap=>$val ) {
           if (!preg_match("/^level/",$cap))
	      $capabilities[$cap]=eme_capNamesCB($cap);
         }
      }
   }

#   $sys_caps = get_option('syscaps');
#   if ( is_array($sys_caps) ) {
#      $capabilities = array_merge($sys_caps, $capabilities);
#   }

   asort($capabilities);
   return $capabilities;
}

function eme_delete_image_files($image_basename) {
   $mime_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
   foreach($mime_types as $type) {
      if (file_exists($image_basename.".".$type))
         unlink($image_basename.".".$type);
   }
}

function eme_status_array() {
   $event_status_array = array();
   $event_status_array[STATUS_PUBLIC] = __ ( 'Public', 'events-made-easy');
   $event_status_array[STATUS_PRIVATE] = __ ( 'Private', 'events-made-easy');
   $event_status_array[STATUS_DRAFT] = __ ( 'Draft', 'events-made-easy');
   return $event_status_array;
}

function eme_localised_date($mydate,$date_format='') {
   global $eme_date_format, $eme_timezone;
   if (empty($date_format))
      $date_format = $eme_date_format;
   // $mydate contains the timezone, but in case it doesn't we provide it
   $eme_date_obj = new ExpressiveDate($mydate,$eme_timezone);
   // Currently in the backend, the timezone is UTC, but maybe that changes in future wp versions
   //   so we search for the current timezone using date_default_timezone_get
   // Since DateTime::format doesn't respect the locale, we use date_i18n here
   //   but date_i18n uses the WP backend timezone, so we need to account for the timezone difference
   // All this because we don't want to use date_default_timezone_set() and wp doesn't set the backend
   //   timezone correctly ...
   $wp_date = new ExpressiveDate($eme_date_obj->getDateTime(),date_default_timezone_get());
   $tz_diff=$eme_date_obj->getOffset()-$wp_date->getOffset();
   $result = date_i18n($date_format, $eme_date_obj->getTimestamp()+$tz_diff);
   return $result;
}

function eme_localised_time($mydate) {
   global $eme_time_format;
   $result = eme_localised_date($mydate,$eme_time_format);
   if (get_option('eme_time_remove_leading_zeros')) {
      $result = str_replace(":00","",$result);
      $result = str_replace(":0",":",$result);
   }
   return $result;
}

function eme_localised_price($price,$target="html") {
   // number_format needs a floating point, so if price is empty (for e.g. discounts), make it 0
   if (empty($price)) $price=0;
   $decimals=intval(get_option('eme_decimals'));
   $result = number_format_i18n($price,$decimals);
   // the result can contain html entities, for e.g. text mails we don't want that of course
   if ($target == "html") {
      return $result;
   } else {
      return html_entity_decode($result);
   }
}

function eme_currency_array() {
   $currency_array = array ();
   $currency_array ['AUD'] = __ ( 'Australian Dollar', 'events-made-easy');
   $currency_array ['CAD'] = __ ( 'Canadian Dollar', 'events-made-easy');
   $currency_array ['CZK'] = __ ( 'Czech Koruna', 'events-made-easy');
   $currency_array ['DKK'] = __ ( 'Danish Krone', 'events-made-easy');
   $currency_array ['EUR'] = __ ( 'Euro', 'events-made-easy');
   $currency_array ['HKD'] = __ ( 'Hong Kong Dollar', 'events-made-easy');
   $currency_array ['HUF'] = __ ( 'Hungarian Forint', 'events-made-easy');
   $currency_array ['ILS'] = __ ( 'Israeli New Sheqel', 'events-made-easy');
   $currency_array ['JPY'] = __ ( 'Japanese Yen', 'events-made-easy');
   $currency_array ['MXN'] = __ ( 'Mexican Peso', 'events-made-easy');
   $currency_array ['NOK'] = __ ( 'Norwegian Krone', 'events-made-easy');
   $currency_array ['NZD'] = __ ( 'New Zealand Dollar', 'events-made-easy');
   $currency_array ['PHP'] = __ ( 'Philippine Peso', 'events-made-easy');
   $currency_array ['PLN'] = __ ( 'Polish Zloty', 'events-made-easy');
   $currency_array ['GBP'] = __ ( 'Pound Sterling', 'events-made-easy');
   $currency_array ['SGD'] = __ ( 'Singapore Dollar', 'events-made-easy');
   $currency_array ['SEK'] = __ ( 'Swedish Krona', 'events-made-easy');
   $currency_array ['CHF'] = __ ( 'Swiss Franc', 'events-made-easy');
   $currency_array ['THB'] = __ ( 'Thai Baht', 'events-made-easy');
   $currency_array ['USD'] = __ ( 'U.S. Dollar', 'events-made-easy');
   $currency_array ['CNY'] = __ ( 'Chinese Yuan Renminbi', 'events-made-easy');

   # the next filter allows people to add extra currencies:
   if (has_filter('eme_add_currencies')) $currency_array=apply_filters('eme_add_currencies',$currency_array);
   return $currency_array;
}

function eme_thumbnail_sizes() {
   global $_wp_additional_image_sizes;
   $sizes = array();
   foreach ( get_intermediate_image_sizes() as $s ) {
      $sizes[ $s ] = $s;
   }
   return $sizes;
}

function eme_transfer_nbr_be97($my_nbr) {
   $transfer_nbr_be97_main=sprintf("%010d",$my_nbr);
   // the control number is the %97 result, or 97 in case %97=0
   $transfer_nbr_be97_check=$transfer_nbr_be97_main % 97;
   if ($transfer_nbr_be97_check==0)
      $transfer_nbr_be97_check = 97 ;
   $transfer_nbr_be97_check=sprintf("%02d",$transfer_nbr_be97_check);
   $transfer_nbr_be97 = $transfer_nbr_be97_main.$transfer_nbr_be97_check;
   $transfer_nbr_be97 = substr($transfer_nbr_be97,0,3)."/".substr($transfer_nbr_be97,3,4)."/".substr($transfer_nbr_be97,7,5);
   return $transfer_nbr_be97_main.$transfer_nbr_be97_check;
}

function eme_redefine_locale($locale) {
   if (function_exists('pll_current_language') && function_exists('pll_languages_list')) {
      $languages=pll_languages_list();
      if (!$languages) return $locale;
      $locale="";
      foreach ($languages as $tmp_lang) {
         if (preg_match("/^$tmp_lang\/|\/$tmp_lang\//",$_SERVER['REQUEST_URI']))
               $locale=$tmp_lang.'_'.strtoupper($tmp_lang);
      }
      if (empty($locale))
         $locale=pll_current_language('locale');
   }
   return $locale;
}

function eme_detect_lang_js_trans_function() {
   if (function_exists('ppqtrans_use')) {
      $function_name="pqtrans_use";
   } elseif (function_exists('qtrans_use')) {
      $function_name="qtrans_use";
   } else {
      $function_name="";
   }
   return $function_name;
}

function eme_detect_lang() {
   $language="";
   if (function_exists('qtrans_getLanguage')) {
      // if permalinks are on, $_GET doesn't contain lang as a parameter
      // so we get it like this to be sure
      $language=qtrans_getLanguage();
   } elseif (function_exists('ppqtrans_getLanguage')) {
      $language=ppqtrans_getLanguage();
   } elseif (function_exists('qtranxf_getLanguage')) {
      $language=qtranxf_getLanguage();
   } elseif (function_exists('pll_current_language') && function_exists('pll_languages_list')) {
      $languages=pll_languages_list();
      if (is_array($languages)) {
          foreach ($languages as $tmp_lang) {
             if (preg_match("/^$tmp_lang\/|\/$tmp_lang\//",$_SERVER['REQUEST_URI']))
                   $language=$tmp_lang;
          }
      }
      if (empty($language))
         $language=pll_current_language('slug');
   } elseif (defined('ICL_LANGUAGE_CODE')) {
      // Both polylang and wpml define this constant, so check polylang first (above)
      // if permalinks are on, $_GET doesn't contain lang as a parameter
      // so we get it like this to be sure
      $language=ICL_LANGUAGE_CODE;
   } elseif (isset($_GET['lang'])) {
      $language=eme_strip_tags($_GET['lang']);
   } else {
      $language="";
   }
   return $language;
}

function eme_lang_url_mode() {
   $url_mode=1;
   if (function_exists('mqtranslate_conf')) {
      // only some functions in mqtrans are different, but the options are named the same as for qtranslate
      $url_mode=get_option('mqtranslate_url_mode');
   } elseif (function_exists('qtrans_getLanguage')) {
      $url_mode=get_option('qtranslate_url_mode');
   } elseif (function_exists('ppqtrans_getLanguage')) {
      $url_mode=get_option('pqtranslate_url_mode');
   } elseif (function_exists('qtranxf_getLanguage')) {
      $url_mode=get_option('qtranslate_url_mode');
   } elseif (function_exists('pll_current_language')) {
      $url_mode=2;
   }
   return $url_mode;
}

# support older php version for array_replace_recursive
if (!function_exists('array_replace_recursive')) {
   function array_replace_recursive($array, $array1) {
      function recurse($array, $array1) {
         foreach ($array1 as $key => $value) {
            // create new key in $array, if it is empty or not an array
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
               $array[$key] = array();
            }

            // overwrite the value in the base array
            if (is_array($value)) {
               $value = recurse($array[$key], $value);
            }
            $array[$key] = $value;
         }
         return $array;
      }

      // handle the arguments, merge one by one
      $args = func_get_args();
      $array = $args[0];
      if (!is_array($array)) {
         return $array;
      }
      for ($i = 1; $i < count($args); $i++) {
         if (is_array($args[$i])) {
            $array = recurse($array, $args[$i]);
         }
      }
      return $array;
   }
}

// returns 1 if each element of array1 is > than the correspondig element of array2 
function eme_array_gt($array1, $array2) {
   if (count($array1) != count($array2))
      return false;
   foreach ($array1 as $key => $value) {
      if ($array1[$key]<=$array2[$key])
         return 0;
   }
   return 1;
}

// returns 1 if each element of array1 is >= the correspondig element of array2 
function eme_array_ge($array1, $array2) {
   if (count($array1) != count($array2))
      return false;
   foreach ($array1 as $key => $value) {
      if ($array1[$key]<$array2[$key])
         return 0;
   }
   return 1;
}

// returns 1 if each element of array1 is < than the correspondig element of array2 
function eme_array_lt($array1, $array2) {
   if (count($array1) != count($array2))
      return false;
   foreach ($array1 as $key => $value) {
      if ($array1[$key]>=$array2[$key])
         return 0;
   }
   return 1;
}

// returns 1 if each elements of array1 is <= than the correspondig element of array2 
function eme_array_le($array1, $array2) {
   if (count($array1) != count($array2))
      return false;
   foreach ($array1 as $key => $value) {
      if ($array1[$key]>$array2[$key])
         return 0;
   }
   return 1;
}

function eme_get_query_arg($arg) {
   if (isset($_GET[$arg]))
      return eme_strip_tags($_GET[$arg]);
   else
      return false;
}

// returns true if the array values are all integers
function eme_array_integers($only_integers) {
   return array_filter($only_integers,'is_numeric') === $only_integers;
}

function eme_nl2br($arg) {
   return preg_replace("/\r\n?|\n\r?/","<br />",$arg);
}

function eme_br2nl($arg) {
   return preg_replace("/<br ?\/?>/", "\n", $arg);
}

function eme_is_multi($element) {
   if (preg_match("/\|\|/",$element))
      return 1;
   else
      return 0;
}

function eme_convert_multi2array($multistring) {
   return explode("||",$multistring);
}

function eme_convert_array2multi($multiarr) {
   return join("||",$multiarr);
}

function eme_session_start() {
   if (!session_id()) session_start();
}

function eme_session_destroy() {
   if (session_id()) {
      // Unset all of the session variables.
      $_SESSION = array();

      // If it's desired to kill the session, also delete the session cookie.
      // Note: This will destroy the session, and not just the session data!
      if (ini_get("session.use_cookies")) {
         $params = session_get_cookie_params();
         setcookie(session_name(), '', time() - 42000,
               $params["path"], $params["domain"],
               $params["secure"], $params["httponly"]
               );
      }

      // Finally, destroy the session.
      session_destroy();
   }
}

function eme_get_client_ip() {
   // Just get the headers if we can or else use the SERVER global
   if (function_exists('apache_request_headers')) {
      $headers = apache_request_headers();
   } else {
      $headers = $_SERVER;
   }
   // Get the forwarded IP if it exists
   if (array_key_exists('X-Forwarded-For',$headers) && filter_var($headers['X-Forwarded-For'],FILTER_VALIDATE_IP)) {
      $the_ip = $headers['X-Forwarded-For'];
   } elseif (array_key_exists('HTTP_X_FORWARDED_FOR',$headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'],FILTER_VALIDATE_IP)) {
      $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
   } else {
      $the_ip = filter_var($_SERVER['REMOTE_ADDR'],FILTER_VALIDATE_IP);
   }
   if (has_filter('eme_get_client_ip')) $the_ip=apply_filters('eme_get_client_ip',$the_ip);
   return $the_ip;
}

// add this function so people can call this from there theme's search.php
function eme_wordpress_search() {
   global $wpdb;
   $table = $wpdb->prefix.EVENTS_TBNAME;
   // the LIKE needs "%", but for prepare to work, we need to escape % using %%
   // and then the prepare is a sprintf, so we need %s for the search string too
   // This results in 3 %-signs, but it is what it is :-)
   $query = "SELECT * FROM $table WHERE (event_name LIKE '%%%s%%') OR
	   (event_notes LIKE '%%%s%%') ORDER BY event_start_date";
   $sql=$wpdb->prepare($query,$_REQUEST['s'],$_REQUEST['s']);
   return $wpdb->get_results ( $sql, ARRAY_A );
}

function eme_calc_price_ajax() {
   header("Content-type: application/json; charset=utf-8");
   // first detect multibooking
   $event_ids=array();
   if (!isset($_POST['bookings'])) {
     echo json_encode(array());
   } else {
      foreach($_POST['bookings'] as $key=>$val) {
         $event_ids[]=intval($key);
      }
   }
   $total=0;
   foreach ($event_ids as $event_id) {
      $event=eme_get_event($event_id);
      $booking=eme_new_booking();
      $bookedSeats = 0;
      $bookedSeats_mp = array();
      if (!eme_is_multi($event['price'])) {
         if (isset($_POST['bookings'][$event_id]['bookedSeats']))
            $bookedSeats = intval($_POST['bookings'][$event_id]['bookedSeats']);
         else
            $bookedSeats = 0;
      } else {
         // for multiple prices, we have multiple booked Seats as well
         // the next foreach is only valid when called from the frontend

         // make sure the array contains the correct keys already, since
         // later on in the function eme_record_booking we do a join
         $booking_prices_mp=eme_convert_multi2array($event['price']);
         foreach ($booking_prices_mp as $key=>$value) {
            $bookedSeats_mp[$key] = 0;
         }
         foreach($_POST['bookings'][$event_id] as $key=>$value) {
            if (preg_match('/bookedSeats(\d+)/', $key, $matches)) {
               $field_id = intval($matches[1])-1;
               $bookedSeats += $value;
               $bookedSeats_mp[$field_id]=$value;
            }
         }
      }

      $booking['event_id']=$event['event_id'];
      $booking['booking_seats']=$bookedSeats;
      $booking['booking_seats_mp']=eme_convert_array2multi($bookedSeats_mp);
      $booking['booking_price']=$event['price'];

      $price = eme_get_total_booking_price($booking,1);
      $discount=eme_booking_discount($event,$booking,0);
      if ($discount>$price) $discount=$price;
      $total=$total+$price-$discount;
   }
   $decimals=intval(get_option('eme_decimals'));
   echo json_encode(array('total'=>number_format_i18n($total,$decimals)));
}

function eme_strip_js_single($html) {
   // first brute-force remove script tags
   $html=trim(stripslashes($html));
   $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
   return $html;
   // then try to catch what was left ...
   if (class_exists('DOMDocument')) {
	   $doc = new DOMDocument();

	   // load the HTML string we want to strip
	   @$doc->loadHTML($html);

	   // for each tag, remove it from the DOM
	   while (($r = $doc->getElementsByTagName("script")) && $r->length) {
		   $r->item(0)->parentNode->removeChild($r->item(0));
	   }   

	   // get the HTML string back
	   $no_script_html_string = $doc->saveHTML();
   }
   return $no_script_html_string;
}

function eme_strip_js( $value ) {
   if (!is_array($value)) {
      $value=eme_strip_js_single($value);
   } else {
      foreach ($value as $key=>$val) {
         $value[$key]=eme_strip_js_single($val);
      }
   }
   return $value;
}

?>
