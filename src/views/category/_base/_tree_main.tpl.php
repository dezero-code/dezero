<?php
/*
|--------------------------------------------------------------------------
| Partial page for Category tree widget
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model class
|
*/

  use dezero\helpers\Url;

  // Current controller
  $current_controller = Dz::currentController(true);

  // Get all categories from the first level (depth = 0)
  $vec_category_models = Yii::$app->categoryManager->getAllByDepth($category_model->category_type, 0);
  if ( !empty($vec_category_models) ) :
?>
  <ol class="dd-list">
    <?php foreach ( $vec_category_models as $category_model ) : ?>
      <li class="dd-item dd3-item dd-item-group dd-level1" data-rel="level1" data-id="<?= $category_model->category_id; ?>" id="dd-item-<?= $category_model->category_id; ?>">
        <div class="dd-handle dd3-handle"></div>
        <div class="dd3-content">
          <a href="<?= Url::to("/category/{$current_controller}/update", ["category_id" => $category_model->category_id]); ?>"<?php if ( $category_model->isDisabled() ) : ?> class="text-danger"<?php endif; ?>><?= $category_model->title(); ?></a>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
<?php else : ?>
  <p><?= $category_model->config->text('empty_text'); ?></p>
<?php endif; ?>
