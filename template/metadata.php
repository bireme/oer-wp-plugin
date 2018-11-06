<?php
$detail_page = (isset($resource_id) ? true: false);
?>

<div class="row-fluid">
    <h2 class="h2-loop-tit">
        <a href="<?php echo real_site_url($oer_plugin_slug); ?>resource/?id=<?php echo $resource->id; ?>">
            <?php echo $resource->title ?>
        </a>
    </h2>
</div>

<?php if ($resource->author ) : ?>
    <div class="row-fluid authors">
        <?php foreach ( $resource->author as $index => $author ):
            echo "<a href='" . real_site_url($oer_plugin_slug) . "?filter=author:\"" . $author . "\"'>" . $author . "</a>";
            echo count($resource->author)-1 != $index ? '; ' : '.';
        endforeach; ?>
    </div>
<?php endif; ?>
<?php if ($resource->learning_objectives ) : ?>
    <div id="conteudo-loop-tags" <?php if($detail_page){echo 'class="destak"'; } else { echo 'class="row-fluid margintop10"'; } ?> > <!--  -->
        <?php if ($detail_page) : ?>
            <h2><?php _e('Objectives','oer'); ?></h2>
        <?php endif; ?>
        <?php
        $ob = $resource->learning_objectives[0];
        $ob_clean = str_replace(array("\\r\\n", "\\t", "\\r", "\\n"), '' , $ob);
        // mark abstract sections
        $ob_mark = preg_replace("/(\A|\.)([\w{Lu}\s]+:)/u", "$1<strong>$2</strong><br/>", $ob_clean);
        echo $ob_mark;
        ?>
    </div>
<?php endif; ?>
<?php if ($resource->description && $detail_page) : ?>
    <div class="destak"> <!-- class="row-fluid" -->
        <h2><?php _e('Description','oer'); ?></h2>
        <?php
        $ab = $resource->description[0];
        $ab_clean = str_replace(array("\\r\\n", "\\t", "\\r", "\\n"), '' , $ab);
        // mark abstract sections
        $ab_mark = preg_replace("/(\A|\.)([\w{Lu}\s]+:)/u", "$1<strong>$2</strong><br/>", $ab_clean);
        echo $ab_mark;
        ?>
    </div>
<?php endif; ?>
<?php if ($resource && $detail_page) : ?>
  <div class="destak">
    <h2><?php _e('Details', 'oer'); ?></h2>

<?php if ($resource->course_type && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Course type','oer'); ?>: <strong><?php echo print_lang_value($resource->course_type, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->structure && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Structure','oer'); ?>: <strong><?php echo print_lang_value($resource->structure, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->tec_resource_type && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Technical resource type','oer'); ?>: <strong><?php echo print_lang_value($resource->tec_resource_type, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->format && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Format','oer'); ?>: <strong><?php echo print_lang_value($resource->format, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->learning_resource_type && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Learning resource type','oer'); ?>: <strong><?php echo print_lang_value($resource->learning_resource_type, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->aggregation_level && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Aggregation level','oer'); ?>: <strong><?php echo print_lang_value($resource->aggregation_level, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->learning_context && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Learning context','oer'); ?>: <strong><?php echo print_lang_value($resource->learning_context, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->audience && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Audience','oer'); ?>: <strong><?php echo print_lang_value($resource->audience, $lang) ?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->license && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('License','oer'); ?>: <strong><?php echo print_lang_value($resource->license, $lang) ?></strong>
    </div>
<?php endif; ?>
</div>

<?php if ($resource->descriptor || $resource->keywords ) : ?>
  <div class="destak">
    <h2><?php _e('Descriptors', 'oer'); ?> </h2>
    <div id="conteudo-loop-tags" class="row-fluid margintop10">
        <i class="ico-tags"> </i>
        <?php
            if ($resource->descriptor){
                foreach ( $resource->descriptor as $index => $subject ):
                    echo "<a href='" . real_site_url($oer_plugin_slug) . "?q=descriptor:\"" . $subject . "\"'>" . $subject . "</a>";
                    echo $index != count($resource->descriptor)-1 ? ', ' : '';
                endforeach;
            }
            if ($resource->keywords){
                echo $resource->descriptor ? ', ' : '';
                foreach ( $resource->keywords as $index => $keyword ):
                    echo "<a href='" . real_site_url($oer_plugin_slug) . "?q=keywords:\"" . $keyword . "\"'>" . ucwords($keyword) . "</a>";
                    echo $index != count($resource->keywords)-1 ? ', ' : '';
                endforeach;
            }
        ?>
    </div>
  </div>
<?php endif; ?>

<?php endif; ?>



<!-- Relationship area -->

<?php if ($resource->relationship_active): ?>
  <div class="destak">
    <h2><?php _e('Related','oer'); ?></h2>
    <?php foreach ( $resource->relationship_active as $rel) { ?>
        <div class="row-fluid">
            <?php
                $rel_parts = explode("@", $rel);
                $rel_relation = $rel_parts[0];
                $rel_act_title = $rel_parts[1];
                $rel_act_link = $rel_parts[2];
            ?>
            <?php
                print_lang_value($rel_relation, $lang);
                echo '&nbsp';
                if ($rel_act_link != ''){
                    echo '<a href="' . real_site_url($oer_plugin_slug) . 'resource/?id=' . $rel_act_link . '">';
                }
                echo $rel_act_title;
                if ($rel_act_link != ''){
                    echo '</a>';
                }
            ?>
        </div>
    <?php } ?>
  </div>
<?php endif; ?>

<?php if ($resource->relationship_passive): ?>
  <div class="destak">
    <h2><?php _e('Related', 'oer'); ?></h2>
    <?php foreach ( $resource->relationship_passive as $rel) { ?>
        <div class="row-fluid">
            <?php
                $rel_parts = explode("@", $rel);
                $rel_relation = $rel_parts[0];
                $rel_act_title = $rel_parts[1];
                $rel_act_link = $rel_parts[2];
            ?>
            <?php
                print_lang_value($rel_relation, $lang);
                echo '&nbsp';
                if ($rel_act_link != ''){
                    echo '<a href="' . real_site_url($oer_plugin_slug) . 'resource/?id=' . $rel_act_link . '">';
                }
                echo $rel_act_title;
                if ($rel_act_link != ''){
                    echo '</a>';
                }
            ?>
        </div>
    <?php } ?>
  </div>
<?php endif; ?>

<!-- Link Resource -->
<?php if ( $resource->link ) : ?>
    <div class="row-fluid video">
      <?php foreach ($resource->link as $url): ?>
          <?php if (preg_match('/vimeo\.com|youtube\.com|flickr\.com/', $url)) :?>
              <?php display_thumbnail($url); ?>
          <?php endif; ?>
      <?php endforeach; ?>
    <div>
    <div class="row-fluid margintop10">
      <?php foreach ($resource->link as $index => $url): ?>
            <span class="more">
                <a href="<?php echo $url ?>" target="_blank">
                    <i class="fa fa-file" aria-hidden="true"> </i> <?php _e('Resource','oer'); ?>
                    <?php
                        if (count($resource->link) > 1){
                            echo __('link','oer') . ' (' . intval($index+1) . ')';
                        }
                    ?>
                </a>
            </span>&nbsp;&nbsp;&nbsp;
      <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row-fluid">
    <span>
    </span>
</div>
