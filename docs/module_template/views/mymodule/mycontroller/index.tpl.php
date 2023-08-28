<?php
/*
|--------------------------------------------------------------------------
| Admin list page for Mymodule model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $data_provider: ActiveDataProvider model
|  - $mymodule_search_model: MymoduleSearch model class
|
*/

  use dezero\helpers\AuthHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\grid\GridView;
  use dezero\widgets\GridViewPjax;

  // Page title
  $this->title = Yii::t('backend', 'Manage MYMODULE');
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
  <div class="page-header-actions">
    <a href="<?= Url::to('/mymodule/mymodule/create'); ?>" class="btn btn-primary"><i class="icon wb-plus"></i><?= Yii::t('backend', 'Add new mymodule'); ?></a>
  </div>
</div>
<div class="page-content">
  <div class="panel">
    <div class="panel-body container-fluid">
      <div class="row row-lg">
        <div class="col-lg-12">
          <?php GridViewPjax::begin(['gridview' => 'mymodule-grid']) ?>
          <?=
            /*
            |----------------------------------------------------------------------------------------
            | GridView widget
            |----------------------------------------------------------------------------------------
            */
            GridView::widget([
              'id' => 'mymodule-grid',
              'dataProvider' => $data_provider,
              'filterModel' => $mymodule_search_model,
              'columns' => [
                [
                  'attribute' => 'name',
                  'header' => Yii::t('mymodule', 'Name'),
                  'value' => function($model) {
                    return $this->render('//mymodule/mymodule/_grid_column', ['column' => 'name', 'model' => $model]);
                  }
                ],
                [
                  'class' => 'dezero\grid\ActionColumn',
                  'template' => '{update} {delete}',
                  'urlCreator' => function($action, $model, $key, $index) {
                    return Url::to([$action, 'mymodule_id' => $key]);
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
</div>
