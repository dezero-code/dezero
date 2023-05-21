<?php
/*
|--------------------------------------------------------------------------
| Admin list page for User model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $data_provider: ActiveDataProvider model
|  - $user_search_model: UserSearch model class
|
*/
  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\grid\GridView;
  use yii\widgets\Pjax;

  // Page title
  $this->title = Yii::t('backend', 'Manage Users');
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
  <div class="page-header-actions">
    <a href="<?= Url::to('/user/admin/create'); ?>" class="btn btn-primary"><i class="icon wb-plus"></i><?= Yii::t('app', 'Add new user'); ?></a>
  </div>
</div>
<div class="page-content">
  <div class="panel">
    <div class="panel-body container-fluid">
      <div class="row row-lg">
        <div class="col-lg-12">
          <?php Pjax::begin() ?>
          <div class="table-responsive">
            <?=
              /*
              |----------------------------------------------------------------------------------------
              | GridView widget
              |----------------------------------------------------------------------------------------
              */
              GridView::widget([
                'dataProvider' => $data_provider,
                'filterModel' => $user_search_model,
                'layout' => "{items}\n{pager}",
                'columns' => [
                  'username',
                  [
                    'attribute' => 'email',
                    'value' => function($model) {
                      return $this->render('//user/admin/_grid_column', ['column' => 'email', 'model' => $model]);
                    },
                    'format' => 'html',
                  ],
                  [
                    'class' => 'dezero\grid\ActionColumn',
                    'template' => '{update} {delete} {custom}',
                    'urlCreator' => function($action, $model, $key, $index) {
                      return Url::to([$action, 'user_id' => $key]);
                    },
                    'buttons' => [
                      'custom' => function($url, $model, $key) {
                        return Html::renderGridButton('<i class="wb-briefcase"></i>', $url, [
                          'title' => Yii::t('backend', 'Flush'),
                          'data-confirm' => Yii::t('backend', 'Are you sure you want to flush this cache?'),
                        ]);
                      }
                    ]
                  ]
                ]
              ]);
            ?>
          </div>
          <?php Pjax::end() ?>
        </div>
      </div>
    </div>
  </div>
</div>
