<?php

function bonus()
{
    $CI =& get_instance();
    return $CI->session->userdata('logged_in');
}

function ie_lte_8(){
    $CI =& get_instance();
    $CI->load->library('user_agent');
    if($CI->agent->is_browser('MSIE') && $CI->agent->version()<=8)
        return true;
    else return false;
}

function chromeless(){
    $CI =& get_instance();
    $i=1;
    while($CI->uri->segment($i)!=false){
        if($CI->uri->segment($i)=="chromeless")
            return true;
        $i++;
    }
    return false;
}

function currentuser()
{
    $CI =& get_instance();
    return $CI->session->userdata('logged_in');
}

function username()
{
    $CI =& get_instance();
    $session_data = $CI->session->userdata('logged_in');
    return $session_data['username'];
}

function userid()
{
    $CI =& get_instance();
    $session_data = $CI->session->userdata('logged_in');
    return $session_data['id'];
}

function error()
{
    $CI =& get_instance();
    $CI->load->view('error');
}

/* Intelligently return the issue_id given either 
   the issue date, 
   volume and number, or 
   issue_id */
function issue($p1, $p2=false)
{
    if($p2) {
        // it's vol/no
    }
    
    // if it's ####-##-##
    
    // else, it's issue_id
    
    return true;
}

// from http://snipplr.com/view/35635/
function relativedate($secs) {
    $second = 1;
    $minute = 60;
    $hour = 60*60;
    $day = 60*60*24;
    $week = 60*60*24*7;
    $month = 60*60*24*7*30;
    $year = 60*60*24*7*30*365;
    
    if ($secs <= 0) { $output = "now";
    }elseif ($secs > $second && $secs < $minute) { $output = round($secs/$second)." second";
    }elseif ($secs >= $minute && $secs < $hour) { $output = round($secs/$minute)." minute";
    }elseif ($secs >= $hour && $secs < $day) { $output = round($secs/$hour)." hour";
    }elseif ($secs >= $day && $secs < $week) { $output = round($secs/$day)." day";
    }elseif ($secs >= $week && $secs < $month) { $output = round($secs/$week)." week";
    }elseif ($secs >= $month && $secs < $year) { $output = round($secs/$month)." month";
    }elseif ($secs >= $year && $secs < $year*10) { $output = round($secs/$year)." year";
    }else{ $output = " more than a decade ago"; }
    
    if ($output <> "now"){
        $output = (substr($output,0,2)<>"1 ") ? $output."s" : $output;
    }
    return $output;
}

// from http://snipplr.com/view/35635/

function dateify($date, $epoch='') {
    
    if(empty($epoch)) $epoch = date("Y-m-d");
    
    $secs = strtotime($epoch)-strtotime($date);
    $date_formatted = date("F j",strtotime($date));
    $output = '';
    
    $second = 1;
    $minute = 60;
    $hour = 60*60;
    $day = 60*60*24;
    $week = 60*60*24*7;
    $month = 60*60*24*7*30;
    $year = 60*60*24*7*30*365;
    
    if ($secs <= 0) { return "<span class='today'>today</span>";
    }elseif ($secs > $second && $secs < $minute) { $output = "<span class='recent'>".round($secs/$second)." second";
    }elseif ($secs >= $minute && $secs < $hour) { $output = "<span class='recent'>".round($secs/$minute)." minute";
    }elseif ($secs >= $hour && $secs < $day) { $output = "<span class='recent'>".round($secs/$hour)." hour";
    }elseif ($secs >= $day && $secs < $week) { $output = "<span class='recent'>".round($secs/$day)." day";
    }elseif ($secs >= $week && $secs < $month) { return "<span class='old'>".$date_formatted."</span>"; }
    
    $output = (substr($output,0,2)<>"1 ") ? $output."s" : $output;
    $output .= " ago</span>";
    return $output;
}

// adapted from http://detectmobilebrowsers.com/
function isMobile() {
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
        return true;
    }
    return false;
}

// strnposr() - Find the position of nth needle in haystack.
// http://www.php.net/manual/en/function.strpos.php#106407
function strnposr($haystack, $needle, $occurrence, $pos = 0) {
    return ($occurrence<2)?strpos($haystack, $needle, $pos):strnposr($haystack,$needle,$occurrence-1,strpos($haystack, $needle, $pos) + 1);
}

// from http://stackoverflow.com/a/6556662/120290
function youtube_id_from_url($url) {
    $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        return $matches[1];
    }
    return false;
}

function vimeo_id_from_url($url) {
    $pattern = 
        '%^# Match any Vimeo URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          vimeo\.com/   # Either youtu.be,
        )               # End host alternatives.
        ([\w-]{7,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        return $matches[1];
    }
    return false;}

?>