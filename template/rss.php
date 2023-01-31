<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";

/*
Template Name: Bibliographic record RSS
*/

global $oer_service_url, $oer_plugin_slug;

$oer_config = get_option('oer_config');
$oer_initial_filter = $oer_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang_dir = substr($site_language,0,2);

$query       = ( isset($_GET['s']) ? sanitize_text_field($_GET['s']) : sanitize_text_field($_GET['q']) );
$sanitize_user_filter = sanitize_text_field($_GET['filter']);
$user_filter = stripslashes($sanitize_user_filter);
$page        = ( isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 1 );
$total       = 0;
$count       = 10;
$filter      = '';

if ($oer_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $oer_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $oer_initial_filter;
    }
}else{
    $filter = $user_filter;
}
$start = ($page * $count) - $count;

$service_request = $oer_service_url . 'api/oer/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang_dir;

$response = @file_get_contents($service_request);
if ($response){
    $response_json = json_decode($response);
    //echo "<pre>"; print_r($response_json); echo "</pre>";
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $docs_list = $response_json->diaServerResponse[0]->response->docs;
}

?>
<rss version="2.0">
    <channel>
        <title><?php _e('Open Educational Resources', 'oer') ?> <?php echo ($query != '' ? '|' . $query : '') ?></title>
        <link><?php echo htmlspecialchars($page_url_params) ?></link>
        <description><?php echo $query ?></description>
        <?php
            foreach ( $docs_list as $doc ) {
                echo "<item>\n";
                echo "   <title><![CDATA[" . $doc->title . "]]></title>\n";
                if ( $doc->author ){
                    echo "   <author><![CDATA[" . implode(", ", $doc->author) . "]]></author>\n";
                }
                echo "   <link>" . home_url($oer_plugin_slug) .'/resource/?id=' . $doc->id . "</link>\n";
                if ( $doc->learning_objectives ) {
                    echo "   <description><![CDATA[" . $doc->learning_objectives[0] . "]]></description>\n";
                }
                echo "   <guid isPermaLink=\"false\">" . $doc->django_id . "</guid>\n";
                echo "</item>\n";
            }
        ?>
    </channel>
</rss>
