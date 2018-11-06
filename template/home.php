<?php
/*
Template Name: OER Home
*/
global $oer_service_url, $oer_plugin_slug, $oer_plugin_title, $oer_texts;

require_once(OER_PLUGIN_PATH . '/lib/Paginator.php');

$oer_config = get_option('oer_config');
$oer_initial_filter = $oer_config['initial_filter'];
$count_page = intval($oer_config['count_page']);
$count_filter = intval($oer_config['count_filter']);

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? $_GET['s'] : $_GET['q'] );
$query = stripslashes($query);
$user_filter = stripslashes($_GET['filter']);
$page = ( isset($_GET['page']) ? $_GET['page'] : 1 );
$total = 0;
$count = ( $count_page > 0 ? $count_page : 10 );
$filter = '';

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

$oer_search = $oer_service_url . 'api/oer/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang . '&count=' . $count;

if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $filter){
        preg_match('/([a-z_]+):(.+)/',$filter, $filter_parts);
        if ($filter_parts){
            // convert to internal format
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
    }
}

$response = @file_get_contents($oer_search);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $resource_list = $response_json->diaServerResponse[0]->response->docs;

    $descriptor_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->descriptor_filter;
    $type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->type;
    $language_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->language;
    $course_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->course_type;
    $tec_resource_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->tec_resource_type;
    $format_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->format;
    $learning_resource_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->learning_resource_type;
    $learning_context_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->learning_context;
    $aggregation_level_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->aggregation_level;
    $audience_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->audience;
    $license_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->license;

    if ($count_filter > 0){
        $descriptor_list = array_slice($descriptor_list, 0, $count_filter);
        $type_list = array_slice($type_list, 0, $count_filter);
        $language_list = array_slice($language_list, 0, $count_filter);
    }

}

$page_url_params = real_site_url($oer_plugin_slug) . '?q=' . urlencode($query)  . '&filter=' . urlencode($user_filter);
$feed_url = real_site_url($oer_plugin_slug) . 'oer-feed?q=' . urlencode($query) . '&filter=' . urlencode($user_filter);

$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);
?>

<?php get_header('oer');?>

		<div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo real_site_url() ?>"><?php _e('Home','oer'); ?></a> >
                <?php if ($query == '' && $filter == ''): ?>
                    <?php echo $oer_plugin_title ?>
                <?php else: ?>
                    <a href="<?php echo real_site_url($oer_plugin_slug); ?>"><?php echo $oer_plugin_title ?> </a> >
                    <?php _e('Search result', 'oer') ?>
                <?php endif; ?>
            </div>

            <section class="header-search">
                <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($oer_plugin_slug); ?>">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                    <input type="hidden" name="sort" id="sort" value="<?php echo $_GET['sort']; ?>">
                    <input type="hidden" name="format" id="format" value="<?php echo $format ? $format : 'summary'; ?>">
                    <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
                    <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Enter one or more words', 'oer'); ?>">
                    <input id="searchsubmit" value="<?php _e('Search', 'oer'); ?>" type="submit">
                </form>
                <div class="pull-right rss">
                    <a href="<?php echo $feed_url ?>" target="blank"><img src="<?php echo OER_PLUGIN_URL; ?>template/images/icon_rss.png" ></a>
                </div>
            </section>

            <div class="content-area result-list">
    			<section id="conteudo">
                    <?php if ( isset($total) && strval($total) == 0) :?>
                        <h1 class="h1-header"><?php _e('No results found','oer'); ?></h1>
                    <?php else :?>
        				<header class="row-fluid border-bottom">
    					   <h1 class="h1-header"><?php _e('Total','oer'); ?>: <?php echo $total; ?></h1>
        				</header>
        				<div class="row-fluid">
                            <?php foreach ( $resource_list as $resource) { ?>
        					    <article class="conteudo-loop">
                                    <?php include('metadata.php') ?>
            					</article>
                            <?php } ?>
        				</div>
                        <div class="row-fluid">
                            <?php echo $pages->display_pages(); ?>
                        </div>
                    <?php endif; ?>
    			</section>
    			<aside id="sidebar">

                    <?php dynamic_sidebar('oer-home');?>

                    <?php if (strval($total) > 0) :?>
                        <div id="filter-link" style="display: none">
                            <div class="mobile-menu" onclick="animateMenu(this)">
                                <a href="javascript:showHideFilters()">
                                    <div class="menu-bar">
                                        <div class="bar1"></div>
                                        <div class="bar2"></div>
                                        <div class="bar3"></div>
                                    </div>
                                    <div class="menu-item">
                                        <?php _e('Filters','oer') ?>
                                    </div>
                                </a>
                           </div>
                        </div>

                        <div id="filters">
                            <?php if ($applied_filter_list) :?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid marginbottom15">
                                        <h1 class="h1-header"><?php echo _e('Selected filters', 'oer') ?></h1>
                                    </header>
                                    <form method="get" name="searchFilter" id="formFilters" action="<?php echo real_site_url($oer_plugin_slug); ?>">
                                        <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                                        <input type="hidden" name="sort" id="sort" value="<?php echo $sort; ?>">
                                        <input type="hidden" name="format" id="format" value="<?php echo $format; ?>">
                                        <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                                        <input type="hidden" name="q" id="query" value="<?php echo $query; ?>" >
                                        <input type="hidden" name="filter" id="filter" value="" >

                                        <?php foreach ( $applied_filter_list as $filter => $filter_values ) :?>
                                            <h2><?php echo translate_label($oer_texts, $filter, 'filter') ?></h2>
                                            <ul>
                                            <?php foreach ( $filter_values as $value ) :?>
                                                <input type="hidden" name="apply_filter" class="apply_filter"
                                                        id="<?php echo md5($value) ?>" value='<?php echo $filter . ':"' . $value . '"'; ?>' >
                                                <li>
                                                    <span class="filter-item">
                                                        <?php
                                                            if ($filter != 'descriptor' && $filter != 'publication_year' && $filter != 'aggregation_level'){
                                                                echo print_lang_value($value, $site_language);
                                                            }else{
                                                                echo $value;
                                                            }
                                                        ?>
                                                    </span>
                                                    <span class="filter-item-del">
                                                        <a href="javascript:remove_filter('<?php echo md5($value) ?>')">
                                                            <img src="<?php echo OER_PLUGIN_URL; ?>template/images/del.png">
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                            </ul>
                                        <?php endforeach; ?>
                                    </form>
                                </section>
                            <?php endif; ?>

                            <?php
                              $order = explode(';', $oer_config['available_filter']);
                              foreach($order as $index=>$content) {
                            ?>

                            <?php if ( $content == 'Descriptor' ): ?>
                			    <section class="row-fluid marginbottom25 widget_categories">
                					<header class="row-fluid border-bottom marginbottom15">
                						<h1 class="h1-header"><?php echo translate_label($oer_texts, 'descriptor', 'filter') ?></h1>
                					</header>
                					<ul>
                                        <?php foreach ( $descriptor_list as $descriptor) { ?>
                                            <?php
                                                $filter_link = '?';
                                                if ($query != ''){
                                                    $filter_link .= 'q=' . $query . '&';
                                                }
                                                $filter_link .= 'filter=descriptor:"' . $descriptor[0] . '"';
                                                if ($user_filter != ''){
                                                    $filter_link .= ' AND ' . $user_filter ;
                                                }
                                            ?>
                                            <li class="cat-item">
                                                <a href='<?php echo $filter_link; ?>'><?php echo $descriptor[0] ?></a>
                                                <span class="cat-item-count"><?php echo $descriptor[1] ?></span>
                                            </li>
                                        <?php } ?>
                					</ul>
                				</section>
                            <?php endif; ?>

                            <?php if ($content == 'Type' ): ?>
                                <section class="row-fluid marginbottom25 widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'type', 'filter') ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $type_list as $type) { ?>
                                            <?php
                                                $filter_link = '?';
                                                if ($query != ''){
                                                    $filter_link .= 'q=' . $query . '&';
                                                }
                                                $filter_link .= 'filter=type:"' . $type[0] . '"';
                                                if ($user_filter != ''){
                                                    $filter_link .= ' AND ' . $user_filter ;
                                                }
                                            ?>
                                            <li class="cat-item">
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($type[0], $site_language)?></a>
                                                <span class="cat-item-count"><?php echo $type[1] ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Language' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'language', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $language_list as $lang ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=language:"' . $lang[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($lang[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $lang[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Course type' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'course_type', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $course_type_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=course_type:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($item[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Technical resource type' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'technical_resource_type', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $tec_resource_type_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=tec_resource_type:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($item[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Format' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'format', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $format_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=format:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($item[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Learning resource type' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'learning_resource_type', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $learning_resource_type_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=learning_resource_type:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($item[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Learning context' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'learning_context', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $learning_context_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=learning_context:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($item[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Aggregation level' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'aggregation_level', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $aggregation_level_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=aggregation_level:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php echo $item[0]; ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'Audience' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'audience', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $audience_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=audience:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($item[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( $content == 'License' ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($oer_texts, 'license', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $license_list as $item ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=license:"' . $item[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($item[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $item[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                        <?php } ?>
                    <?php endif; ?>



                </aside>
    			<div class="spacer"></div>
            </div> <!-- close DIV.result-area -->
		</div> <!-- close DIV.ajusta2 -->
	</div>
<?php get_footer();?>
