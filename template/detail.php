<?php
/*
Template Name: OER Detail
*/

global $oer_service_url, $oer_plugin_slug, $oer_plugin_title, $oer_texts; $similar_docs_url;

$oer_config = get_option('oer_config');
$resource_id   = $_GET['id'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$oer_addthis_id = $oer_config['addthis_profile_id'];

$oer_service_request = $oer_service_url . 'api/oer/search/?id=' . $resource_id . '&op=related&lang=' . $lang;
$response = @file_get_contents($oer_service_request);

if ($response){
    $response_json = json_decode($response);
    $resource = $response_json->diaServerResponse[0]->match->docs[0];
    $similar_docs_url = $similar_docs_url . '?adhocSimilarDocs='.urlencode($resource->learning_objectives[0]);
    $similar_query = urlencode($similar_docs_url);
}

?>

<?php get_header('oer'); ?>

<div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo real_site_url() ?>"><?php _e('Home','oer'); ?></a> >
                <a href="<?php echo real_site_url($oer_plugin_slug); ?>"><?php echo $oer_plugin_title ?> </a> >
                <?php _e('Resource','oer'); ?>
            </div>

            <section id="conteudo">
                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php echo $resource->title; ?></h1>
                </header>
                <div class="row-fluid">
                    <article class="conteudo-loop">
                        <?php include('metadata.php') ?>

                        <footer class="row-fluid margintop05">
                            <i class="ico-compartilhar"><?php _e('Share','oer'); ?></i>
                            <ul class="conteudo-loop-icons">
                                <li class="conteudo-loop-icons-li">
                                    <!-- AddThis Button BEGIN -->
                                    <div class="addthis_toolbox addthis_default_style">
                                        <a class="addthis_button_facebook"></a>
                                        <a class="addthis_button_delicious"></a>
                                        <a class="addthis_button_google_plusone_share"></a>
                                        <a class="addthis_button_favorites"></a>
                                        <a class="addthis_button_compact"></a>
                                    </div>
                                    <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
                                    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $oer_addthis_id; ?>"></script>
                                    <!-- AddThis Button END -->
                                </li>
                                <li class="conteudo-loop-icons-li">
                                    <!-- AddThisEvent Button BEGIN -->
                                    <script type="text/javascript" src="https://addthisevent.com/libs/1.5.8/ate.min.js"></script>
                                </li>
                            </ul>
                        </footer>
                    </article>
                </div>
            </section>
            <aside id="sidebar">
                <section class="row-fluid marginbottom25 widget_categories">
                    <header class="row-fluid border-bottom marginbottom15">
                        <h1 class="h1-header"><?php _e('Related articles','oer'); ?>
                           <?php //var_dump($similar_docs_url); ?>
                        </h1>
                    </header>
                    <ul id="ajax">

                    </ul>
                </section>


<?php
    $url = OER_PLUGIN_URL;
    $url.='template/similar.php?query='.$similar_query.'&lang='.$lang;
?>
<script type="text/javascript">
    show_similar("<?php echo $url; ?>");
</script>
            </aside>
        </div>
    </div>

<?php get_footer();?>
