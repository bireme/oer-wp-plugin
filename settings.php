<?php
function oer_page_admin() {
    $config = get_option('oer_config');

?>
    <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('Open Educational Resources Settings', 'oer'); ?></h2>

            <form method="post" action="options.php">

                <?php settings_fields('oer-settings-group'); ?>

                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><?php _e('Plugin page', 'oer'); ?>:</th>
                            <td><input type="text" name="oer_config[plugin_slug]" value="<?php echo ($config['plugin_slug'] != '' ? $config['plugin_slug'] : 'oer'); ?>" class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Filter query', 'oer'); ?>:</th>
                            <td><input type="text" name="oer_config[initial_filter]" value='<?php echo $config['initial_filter'] ?>' class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('AddThis profile ID', 'oer'); ?>:</th>
                            <td><input type="text" name="oer_config[addthis_profile_id]" value="<?php echo $config['addthis_profile_id'] ?>" class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Google Analytics code', 'oer'); ?>:</th>
                            <td><input type="text" name="oer_config[google_analytics_code]" value="<?php echo $config['google_analytics_code'] ?>" class="regular-text code"></td>
                        </tr>

                        <?php
                        if ( function_exists( 'pll_the_languages' ) ) {
                            $available_languages = pll_languages_list();
                            $available_languages_name = pll_languages_list(array('fields' => 'name'));
                            $count = 0;
                            foreach ($available_languages as $lang) {
                                $key_name = 'plugin_title_' . $lang;
                                $home_url = 'home_url_' . $lang;

                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Home URL", "oer") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="oer_config[' . $home_url . ']" value="' . $config[$home_url] . '" class="regular-text code"></td>';
                                echo '</tr>';

                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Page title", "oer") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="oer_config[' . $key_name . ']" value="' . $config[$key_name] . '" class="regular-text code"></td>';
                                echo '</tr>';
                                $count++;
                            }
                        }else{
                            echo '<tr valign="top">';
                            echo '   <th scope="row">' . __("Page title", "oer") . ':</th>';
                            echo '   <td><input type="text" name="oer_config[plugin_title]" value="' . $config["plugin_title"] . '" class="regular-text code"></td>';
                            echo '</tr>';
                        }

                        ?>
                        <tr valign="top">
                            <th scope="row"><?php _e('Search filters', 'oer');?>:</th>

                            <?php
                              if(!isset($config['available_filter'])){
                                $config['available_filter'] = 'Descriptor;Type;Language;Course type;Technical resource type;Format;Learning resource type;Learning context;Aggregation level;Audience;License';
                                $order = explode(';', $config['available_filter'] );

                              }else {
                                $order = explode(';', $config['available_filter'] );
                            }

                            ?>

                            <td>

                              <table border=0>
                                <tr>
                                <td >
                                    <p align="left"><?php _e('Available', 'oer');?><br>
                                      <ul id="sortable1" class="droptrue">
                                      <?php
                                      if(!in_array('Descriptor', $order) && !in_array('Descriptor ', $order) ){
                                        echo '<li class="ui-state-default" id="Descriptor">'.__('Descriptor','oer').'</li>';
                                      }
                                      if(!in_array('Type', $order) && !in_array('Type ', $order) ){
                                        echo '<li class="ui-state-default" id="Type">'.__('Type','oer').'</li>';
                                      }
                                      if(!in_array('Language', $order) && !in_array('Language ', $order) ){
                                        echo '<li class="ui-state-default" id="Language">'.__('Language','oer').'</li>';
                                      }
                                      if(!in_array('Course type', $order) && !in_array('Course type ', $order) ){
                                        echo '<li class="ui-state-default" id="Course type">'.__('Course type','oer').'</li>';
                                      }
                                      if(!in_array('Technical resource type', $order) && !in_array('Technical resource type ', $order) ){
                                        echo '<li class="ui-state-default" id="Technical resource type">'.__('Technical resource type','oer').'</li>';
                                      }
                                      if(!in_array('Format', $order) && !in_array('Format ', $order) ){
                                        echo '<li class="ui-state-default" id="Format">'.__('Format','oer').'</li>';
                                      }
                                      if(!in_array('Learning resource type', $order) && !in_array('Learning resource type ', $order) ){
                                        echo '<li class="ui-state-default" id="Learning resource type">'.__('Learning resource type','oer').'</li>';
                                      }
                                      if(!in_array('Learning context', $order) && !in_array('Learning context ', $order) ){
                                        echo '<li class="ui-state-default" id="Learning context">'.__('Learning context','oer').'</li>';
                                      }
                                      if(!in_array('Aggregation level', $order) && !in_array('Aggregation level ', $order) ){
                                        echo '<li class="ui-state-default" id="Aggregation level">'.__('Aggregation level','oer').'</li>';
                                      }
                                      if(!in_array('Audience', $order) && !in_array('Audience ', $order) ){
                                        echo '<li class="ui-state-default" id="Audience">'.__('Audience','oer').'</li>';
                                      }
                                      if(!in_array('License', $order) && !in_array('License ', $order) ){
                                        echo '<li class="ui-state-default" id="License">'.__('License','oer').'</li>';
                                      }

                                      ?>
                                      </ul>

                                    </p>
                                </td>

                                <td >
                                    <p align="left"><?php _e('Selected', 'oer');?> <br>
                                      <ul id="sortable2" class="sortable-list">
                                      <?php
                                      foreach ($order as $index => $item) {
                                        $item = trim($item); // Important
                                        echo '<li class="ui-state-default" id="'.$item.'">'.__($item ,'oer').'</li>';
                                      }
                                      ?>
                                      </ul>
                                      <input type="hidden" id="order_aux" name="oer_config[available_filter]" value="<?php echo trim($config['available_filter']); ?> " >

                                    </p>
                                </td>
                                </tr>
                                </table>

                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Records per page', 'oer'); ?>:</th>
                            <td><input type="number" name="oer_config[count_page]" value="<?php echo ($config['count_page'] ? $config['count_page'] : 10) ?>" class="small-text" step="1" min="5"></td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php _e('Items per filter', 'oer'); ?>:</th>
                            <td><input type="number" name="oer_config[count_filter]" value="<?php echo ($config['count_filter'] ? $config['count_filter'] : 10) ?>" class="small-text" step="1" min="1"></td>
                        </tr>

                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save changes') ?>" />
                </p>
            </form>
        </div>
        <script type="text/javascript">
            $j( function() {
              $j( "ul.droptrue" ).sortable({
                connectWith: "ul"
              });

              $j('.sortable-list').sortable({

                connectWith: 'ul',
                update: function(event, ui) {
                  var changedList = this.id;
                  var order = $j(this).sortable('toArray');
                  var positions = order.join(';');
                  $j('#order_aux').val(positions);

                }
              });
            } );
        </script>
<?php
}
?>
