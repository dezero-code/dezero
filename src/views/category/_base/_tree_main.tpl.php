<?php
/*
|--------------------------------------------------------------------------
| Partial page for Category tree widget
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model class
|  - $vec_config: Category configuration options
|
*/
  use dezero\helpers\Url;

  // Get all categories from the first level (depth = 0)
  $vec_category_models = Yii::$app->categoryManager->getAllByDepth($category_model->category_type, 0);
  if ( !empty($vec_category_models) ) :
?>
  <ol class="dd-list">
    <?php foreach ( $vec_category_models as $category_model ) : ?>
      <li class="dd-item dd3-item dd-item-group dd-level1" data-rel="level1" data-id="<?= $category_model->category_id; ?>" id="dd-item-<?= $category_model->category_id; ?>">
        <div class="dd-handle dd3-handle"></div>
        <div class="dd3-content">
          <a href="<?= Url::to("/category/{$category_model->category_type}/update", ["id" => $category_model->category_id]); ?>"<?php if ( $category_model->isDisabled() ) : ?> class="text-danger"<?php endif; ?>><?= $category_model->title(); ?></a>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
<?php else : ?>
  <p><?= $category_model->text('empty_text'); ?></p>
<?php endif; ?>
<?php
  /*
  // Custom Javascript nestable code for this page
  if ( ! $is_ajax )
  {
    Yii::app()->clientscript
      ->registerScriptFile(Yii::app()->theme->baseUrl. '/libraries/jquery-nestable/jquery.nestable.js', CClientScript::POS_END)
      ->registerScriptFile(Yii::app()->theme->baseUrl. '/js/dz.nestable.js', CClientScript::POS_END);
  }

  if ( ! $vec_config['is_editable'] )
  {
    // Load category nestable
    Yii::app()->clientscript->registerScript('category_tree_js',
      "$('#category-nestable-wrapper').dzNestable({
          maxDepth: 1,
          readOnly: true
      });"
      , CClientScript::POS_READY);
  }
  else
  {
    // Load category nestable
    Yii::app()->clientscript->registerScript('category_tree_js',
      "$('#category-nestable-wrapper').dzNestable({
        maxDepth: 1
      });"
      , CClientScript::POS_READY);
  }
  */
?>
