<?php
/*
|--------------------------------------------------------------------------
| Partial page for Category tree widget (CHILDREN)
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model class
|
*/

  use dezero\helpers\Url;

  // Current controller
  $current_controller = Dz::currentController(true);

  $first_level_category_model = $category_model->getFirstLevelCategory();
  $max_levels = $category_model->getMaxLevels();
?>
<ol class="dd-list">
  <?php
    /*
    |----------------------------------------------------------------------------------------
    | 1st LEVEL CATEGORY
    |----------------------------------------------------------------------------------------
    */
  ?>
  <li class="dd-item dd3-item dd-item-group dd-level1<?php if ( $category_model->category_id === $first_level_category_model->category_id ) : ?> active<?php endif; ?>" data-rel="level1" data-id="<?= $first_level_category_model->category_id; ?>" id="dd-item-<?= $first_level_category_model->category_id; ?>">
    <div class="dd-handle dd3-handle"></div>
    <div class="dd3-content">
      <a href="<?= Url::to("/category/{$current_controller}/update", ["category_id" => $first_level_category_model->category_id]); ?>"<?php if ( $first_level_category_model->isDisabled() ) : ?> class="text-danger"<?php endif; ?>>
        <?= $first_level_category_model->title(); ?><?php if ( $max_levels > 1 && $first_level_category_model->totalSubcategories > 0 ) : ?> (<?= $first_level_category_model->totalSubcategories; ?>)<?php endif; ?>
      </a>
    </div>

    <?php
      /*
      |----------------------------------------------------------------------------------------
      | 2nd LEVEL - SUBCATEGORIES
      |----------------------------------------------------------------------------------------
      */
    ?>
    <ol class="dd-list">
    <?php
      if ( $first_level_category_model->subCategories ) :
    ?>
      <?php foreach ( $first_level_category_model->subCategories as $second_level_category_model ) : ?>
        <li class="dd-item dd3-item dd-item-subcategory<?php if ( $category_model->category_id === $second_level_category_model->category_id ) : ?> active<?php endif; ?>" data-rel="level2" data-id="q-<?= $second_level_category_model->category_id; ?>" id="dd-item-q-<?= $second_level_category_model->category_id; ?>">
          <div class="dd-handle dd3-handle"></div>
          <div class="dd3-content">
            <a href="<?= Url::to("/category/{$current_controller}/update", ["category_id" => $second_level_category_model->category_id]); ?>"<?php if ( $second_level_category_model->isDisabled() ) : ?> class="text-danger"<?php endif; ?>>
              <?= $second_level_category_model->title(); ?><?php if ( $max_levels > 2 && $second_level_category_model->totalSubcategories > 0 ) : ?> (<?= $second_level_category_model->totalSubcategories; ?>)<?php endif; ?>
            </a>
          </div>

          <?php
            /*
            |----------------------------------------------------------------------------------------
            | From 3rd LEVEL to Nth LEVELS
            |----------------------------------------------------------------------------------------
            */
            if ( $max_levels > 2 ) :
          ?>
            <?=
              $this->render($category_model->config->viewPath('_tree_sublevels'), [
                'category_parent_model' => $second_level_category_model,
                'category_model'        => $category_model,
                'num_level'             => 3,
                'max_levels'            => $max_levels
              ]);
            ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </li>
</ol>
