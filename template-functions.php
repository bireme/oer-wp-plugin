<?php

if ( !function_exists('print_lang_value') ) {
    function print_lang_value($value, $lang_code){
        $lang_code = substr($lang_code,0,2);
        if ( is_array($value) ){
            foreach($value as $current_value){
                $print_values[] = get_lang_value($current_value, $lang_code);
            }
            echo implode(', ', $print_values);
        }else{
            echo get_lang_value($value, $lang_code);
        }
        return;
    }
}

if ( !function_exists('get_lang_value') ) {
    function get_lang_value($string, $lang_code, $default_lang_code = 'en'){
        $lang_value = array();
        $occs = preg_split('/\|/', $string);

        foreach ($occs as $occ){
            $re_sep = (strpos($occ, '~') !== false ? '/\~/' : '/\^/');
            $lv = preg_split($re_sep, $occ);
            $lang = substr($lv[0],0,2);
            $value = $lv[1];
            $lang_value[$lang] = $value;
        }
        if ( isset($lang_value[$lang_code]) ){
            $translated = $lang_value[$lang_code];
        }else{
            $translated = $lang_value[$default_lang_code];
        }

        return $translated;
    }
}

if ( !function_exists('format_date') ) {
    function format_date($string){
        $date_formated = '';
        if (strpos($string,'-') !== false) {
            $date_formated = substr($string,8,2)  . '/' . substr($string,5,2) . '/' . substr($string,0,4);
        }else{
            $date_formated =  substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }

        return $date_formated;
    }
}

if ( !function_exists('format_act_date') ) {
    function format_act_date($string, $lang){
        $months = array();
        $months['pt'] = array('Janeiro','Feveiro', 'Março', 'Abril', 'Maio', 'Junho',
                              'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

        $months['es'] = array('Enero','Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');


        $date_formated = '';
        if (strpos($string,'-') !== false) {
            if ($lang != 'en'){
                $month_val = intval(substr($string,5,2));
                $month_name = $months[$lang][$month_val-1];
            } else {
                $month_name = strftime("%B", strtotime($string));
            }
            $date_formated = substr($string,8,2) . ' ' . __('of','leisref') . ' ' . $month_name . ' ' . __('of', 'leisref') . ' ' . substr($string,0,4);
        }else{
            $date_formated =  substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }

        return $date_formated;
    }
}

if ( !function_exists('isUTF8') ) {
    function isUTF8($string){
        return (utf8_encode(utf8_decode($string)) == $string);
    }
}

if ( !function_exists('translate_label') ) {
    function translate_label($texts, $label, $group=NULL) {
        // labels on texts.ini must be array key without spaces
        $label_norm = preg_replace('/[&,\'\s]+/', '_', $label);
        if($group == NULL) {
            if(isset($texts[$label_norm]) and $texts[$label_norm] != "") {
                return $texts[$label_norm];
            }
        } else {
            if(isset($texts[$group][$label_norm]) and $texts[$group][$label_norm] != "") {
                return $texts[$group][$label_norm];
            }
        }
        // case translation not found return original label ucfirst
        return ucfirst($label);
    }
}

if ( !function_exists('get_site_meta_tags') ) {
    function get_site_meta_tags($url){

        $site_title = array();

        $fp = @file_get_contents($url);

        if ($fp) {
            $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
            if ($res) {
                $site_title = preg_replace('/\s+/', ' ', $title_matches[1]);
                $site_title = trim($site_title);
            }

            $site_meta_tags = get_meta_tags($url);
            $site_meta_tags['title'] = $site_title;

            foreach ($site_meta_tags as $key => $value) {
                if (!isUTF8($value)){
                    $site_meta_tags[$key] = utf8_encode($value);
                }
            }
        }
        return $site_meta_tags;
    }
}

if ( !function_exists('real_site_url') ) {
    function real_site_url($path = ''){

        $site_url = get_site_url();

        // check for multi-language-framework plugin
        if ( function_exists('mlf_parseURL') ) {
            global $mlf_config;

            $current_language = substr( strtolower(get_bloginfo('language')),0,2 );

            if ( $mlf_config['default_language'] != $current_language ){
                $site_url .= '/' . $current_language;
            }
        }
        if ($path != ''){
            $site_url .= '/' . $path;
        }
        $site_url .= '/';


        return $site_url;
    }
}

if ( !function_exists('display_thumbnail') ) {
    function display_thumbnail($link){
        $service = '';
        $link_data = parse_url($link);
        $ext = pathinfo($link, PATHINFO_EXTENSION);
        $img_ext = array('jpg', 'jpeg', 'png', 'gif');

        if (strpos($link_data['host'],'youtube.com') !== false) {
            $service = 'youtube';
            parse_str($link_data['query'], $params);
            $video_id = $params['v'];
        } elseif (strpos($link_data['host'],'vimeo.com') !== false) {
            $service = 'vimeo';
            $video_id = $link_data['path'];
        } elseif (strpos($link_data['host'],'flickr.com') !== false) {
            $service = 'flicker';
        } elseif (strpos($link_data['host'],'slideshare.net') !== false) {
            $service = 'slideshare';
        }

        if ($service == 'youtube') {
            echo '<iframe width="474" height="356" src="//www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>';
        } elseif ($service == 'vimeo') {
            echo '<iframe src="//player.vimeo.com/video' . $video_id . '" width="500" height="281" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>';
        } elseif ($service == 'flicker') {
            echo '<iframe src="' . $link . '/player/" width="320" height="211" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>';
        } elseif ($service == 'slideshare') {
            $embed_service_url = 'https://www.slideshare.net/api/oembed/2?url=' . $link . '&format=json';
            $embed_service_response = file_get_contents($embed_service_url);
            $embed_service_data = json_decode($embed_service_response, true);
            echo $embed_service_data['html'];
        } elseif ( 'pdf' == $ext ) {
            echo '<iframe src="https://drive.google.com/viewerng/viewer?embedded=true&url=' . $link . '" width="500" height="400" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>';
        } elseif ( in_array(strtolower($ext), $img_ext) ) {
            echo '<img src="' . $link . '" alt="thumbnail" style="max-width: 500px; max-height: 400px;"></img>';
        }
    }
}

?>
