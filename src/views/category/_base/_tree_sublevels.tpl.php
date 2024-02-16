<?php
/*
|--------------------------------------------------------------------------
| Partial page for Category tree widget (FROM 3rd LEVEL to Nth LEVEL)
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_parent_model: Category model class
|  - $category_model: Category model class
|  - $num_level: Current level
|  - $max_levels: Max levels allowed
|
*/

  use dezero\helpers\Url;

  // Current controller
  $current_controller = Dz::currentController(true);

  if ( $category_parent_model->category_id === $category_model->category_id || $category_model->isParent($category_parent_model->category_id) ) :
?>
  <ol class="dd-list">
    <?php foreach ( $category_parent_model->subCategories as $sub_level_category_model ) : ?>
      <li class="dd-item dd3-item dd-item-subcategory<?php if ( $category_model->category_id === $sub_level_category_model->category_id ) : ?> active<?php endif; ?>" data-rel="level<?= $num_level; ?>" data-id="q-<?= $sub_level_category_model->category_id; ?>" id="dd-item-q-<?= $sub_level_category_model->category_id; ?>">
        <div class="dd-handle dd3-handle"></div>
        <div class="dd3-content">
          <a href="<?= Url::to("/category/{$current_controller}/update", ["category_id" => $sub_level_category_model->category_id]); ?>"<?php if ( $sub_level_category_model->isDisabled() ) : ?> class="text-danger"<?php endif; ?>>
            <?= $sub_level_category_model->title(); ?><?php if ( $max_levels > $num_level && $sub_level_category_model->totalSubcategories > 0 ) : ?> (<?= $sub_level_category_model->totalSubcategories; ?>)<?php endif; ?>
          </a>
        </div>
          <?php
            /*
            |----------------------------------------------------------------------------------------
            | Nth LEVELS
            |----------------------------------------------------------------------------------------
            */
          if ( $max_levels > $num_level ) :
          ?>
            <?=
              $this->render($category_model->config->viewPath('_tree_sublevels'), [
                'category_parent_model' => $sub_level_category_model,
                'category_model'        => $category_model,
                'num_level'             => $num_level + 1,
                'max_levels'            => $max_levels
              ]);
            ?>
          <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
<?php endif; ?>
