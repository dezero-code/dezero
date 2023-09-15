<?php
/*
|--------------------------------------------------------------------------
| Admin list page for Category model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $data_provider: ActiveDataProvider model
|  - $category_search_model: CategorySearch model class
|
*/

  use dezero\helpers\AuthHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\grid\GridView;
  use dezero\widgets\GridViewPjax;
  use Dz;

  // Current controller
  $current_controller = Dz::currentController(true);

  // Page title
  $this->title = Yii::t('backend', $category_search_model->config->text('index_title'));
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
  <div class="page-header-actions">
    <a href="<?= Url::to("/category/{$current_controller}/create"); ?>" class="btn btn-primary"><i class="icon wb-plus"></i><?= Yii::t('backend', 'Add new category'); ?></a>
  </div>
</div>
<div class="page-content">
  <div class="row row-lg">
    <?php
      /*
      |----------------------------------------------------------------------------------------
      | SORT FIRST LEVEL?
      |----------------------------------------------------------------------------------------
      */
      if ( $category_search_model->config->isFirstLevelSortable() === true ) :
    ?>
      <div class="col-lg-3">
        <div class="panel panel-tree category-tree-wrapper">
          <header class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('backend', $category_search_model->config->text('panel_title')); ?></h3>
          </header>
          <div class="panel-body">
            <div id="category-loading-tree" class='dz-loading center hide'></div>
            <div class="dd dd-category-group" id="category-nestable-wrapper" data-name="category" data-max-depth="<?= $category_search_model->getMaxLevels(); ?>" data-url="<?= Url::to("/category/{$current_controller}/weight"); ?>?category_id=0"<?php if ( ! $category_search_model->config->isEditable() ) : ?> data-readonly="true"<?php endif; ?>>
              <?=
                // Render tree main
                $this->render($category_search_model->config->viewPath('_tree_main'), [
                  'category_model'  => $category_search_model
                ]);
              ?>
              <?php if ( $category_search_model->config->isEditable() ) : ?>
                <hr>
                <div class="buttons">
                  <a href="<?= Url::to("/category/{$current_controller}/create"); ?>" class="btn mr-10 mb-10 btn-primary"><i class="icon wb-plus"></i> <?= $category_search_model->config->text('add_button'); ?></a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-9">
    <?php else : ?>
      <div class="col-lg-12">
    <?php endif; ?>
    <div class="panel panel-category-summary panel-top-summary">
      <header class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('app', $category_search_model->config->text('list_title')); ?></h3>
      </header>
      <div class="panel-body container-fluid">
        <?php GridViewPjax::begin(['gridview' => 'category-grid']) ?>
        <?=
          /*
          |----------------------------------------------------------------------------------------
          | GridView widget
          |----------------------------------------------------------------------------------------
          */
          GridView::widget([
            'id' => 'category-grid',
            'dataProvider' => $data_provider,
            'filterModel' => $category_search_model,
            'columns' => [
              [
                'attribute' => 'name',
                'header' => Yii::t('category', 'Name'),
                'value' => function($model) {
                  return $this->render($model->config->viewPath('_grid_column'), ['column' => 'name', 'model' => $model]);
                }
              ],
              [
                'class' => 'dezero\grid\ActionColumn',
                'template' => '{update} {delete}',
                'urlCreator' => function($action, $model, $key, $index) {
                  return Url::to([$action, 'category_id' => $key]);
                },
                /*
                'buttons' => [
                  'custom' => function($url, $model, $key) {
                    return Html::gridButton('Test', $url, [
                      'icon'  => 'briefcase',
                      'title' => Yii::t('backend', 'Flush'),
                      'data-confirm' => Yii::t('backend', 'Are you sure you want to flush this cache?'),
                    ]);
                  }
                ]
                */
              ]
            ]
          ]);
        ?>
        <?php GridViewPjax::end() ?>
      </div>
    </div>
  </div>
</div>
