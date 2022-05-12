<?php
/*
|--------------------------------------------------------------------------
| Sidebar menu item
|--------------------------------------------------------------------------
|
| Available variables:
|  - $this: dezero\web\View component
|  - $vec_items: Array with menu items structure
|
*/

  use dezero\helpers\Url;
  use yii\helpers\Html;
  use yii\widgets\Spaceless;

?>
<?php if ( !empty($vec_items) ) : ?>
  <?php Spaceless::begin(); ?>
    <ul class="site-menu">
      <?php foreach ( $vec_items as $que_item ) : ?>
        <?php
          // Check if item "is_visible"
          $is_visible = ! isset($que_item['visible']) || $que_item['visible'];
        ?>
        <?php if ( $is_visible ) : ?>
          <?php
            // Check if item has children
            $has_children = false;
            if ( isset($que_item['items']) && !empty($que_item['items']) && is_array($que_item['items']) )
            {
              $has_children = true;
            }

            // Get classes for this <li> item
            $item_classes = 'site-menu-item';
            if ( $has_children )
            {
              $item_classes .= ' has-sub';
            }
            if ( isset($que_item['is_active']) && $que_item['is_active'] )
            {
              $item_classes .= ' active open';
            }
          ?>
          <li class="<?= $item_classes; ?>" >
            <?php
              $que_url = $que_item['url'];
              if ( is_array($que_item['url']) )
              {
                $item_url = $que_item['url'][0];
                $params_url = [];
                if ( count($que_item['url']) > 0 )
                {
                  $params_url = $que_item['url'];
                  unset($params_url[0]);
                }
                $que_url = Url::to($item_url, $params_url);
              }
            ?>
            <a href="<?= $que_url; ?>">
              <?php if ( $que_item['icon'] ) : ?>
                <i class="site-menu-icon <?= $que_item['icon']; ?>" aria-hidden="true"></i>
              <?php endif; ?>
              <span class="site-menu-title"><?= $que_item['label']; ?></span>
              <?php if ( $has_children ) : ?>
                <span class="site-menu-arrow"></span>
              <?php endif; ?>
            </a>
            <?php
              // CHILDREN ITEMS
              if ( $has_children ) :
            ?>
              <ul class="site-menu-sub">
                <?php foreach ( $que_item['items'] as $que_sub_item ) : ?>
                  <?php
                    $is_visible = ! isset($que_sub_item['visible']) || $que_sub_item['visible'];
                  ?>
                  <?php if ( $is_visible ) : ?>
                    <li class="site-menu-item<?php if ( isset($que_sub_item['is_indent']) && $que_sub_item['is_indent'] ) : ?> indent-item<?php endif; ?>">
                      <?php
                        $que_url = $que_sub_item['url'];
                        if ( is_array($que_sub_item['url']) )
                        {
                          $item_url = $que_sub_item['url'][0];
                          $params_url = [];
                          if ( count($que_sub_item['url']) > 0 )
                          {
                            $params_url = $que_sub_item['url'];
                            unset($params_url[0]);
                          }
                          $que_url = Url::to($item_url, $params_url);
                        }
                      ?>
                      <?php
                        // Target "_blank" links
                        if ( isset($que_sub_item['htmlOptions']) && is_array($que_sub_item['htmlOptions']) && isset($que_sub_item['htmlOptions']['target']) ) :
                      ?>
                        <a href="<?= $que_url; ?>" <?= Html::renderTagAttributes($que_sub_item['htmlOptions']); ?>>
                          <span class="site-menu-title"><?= $que_sub_item['label']; ?></span>
                        </a>
                      <?php else : ?>
                        <a class="animsition-link" href="<?= $que_url; ?>">
                          <span class="site-menu-title"><?= $que_sub_item['label']; ?></span>
                        </a>
                      <?php endif; ?>

                    </li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  <?php Spaceless::end(); ?>
<?php endif; ?>
