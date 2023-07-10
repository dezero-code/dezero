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

  use dezero\helpers\AuthHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\grid\GridView;
  use dezero\widgets\GridViewPjax;

  // Page title
  $this->title = Yii::t('backend', 'Manage users');
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
          <?php GridViewPjax::begin(['gridview' => 'user-grid']) ?>
          <?=
            /*
            |----------------------------------------------------------------------------------------
            | GridView widget
            |----------------------------------------------------------------------------------------
            */
            GridView::widget([
              'id' => 'user-grid',
              'dataProvider' => $data_provider,
              'filterModel' => $user_search_model,
              'columns' => [
                [
                  'attribute' => 'user_id',
                  'value' => function($model) {
                    return $this->render('//user/admin/_grid_column', ['column' => 'user_id', 'model' => $model]);
                  }
                ],
                [
                  'attribute' => 'name_filter',
                  'header' => Yii::t('user', 'Name'),
                  'value' => function($model) {
                    return $this->render('//user/admin/_grid_column', ['column' => 'name_filter', 'model' => $model]);
                  }
                ],
                [
                  'attribute' => 'email',
                  'value' => function($model) {
                    return $this->render('//user/admin/_grid_column', ['column' => 'email', 'model' => $model]);
                  }
                ],
                [
                  'attribute' => 'role_filter',
                  'header' => Yii::t('user', 'Roles'),
                  'filter' => AuthHelper::getRolesList(),
                  'value' => function($model) {
                    return $this->render('//user/admin/_grid_column', ['column' => 'roles', 'model' => $model]);
                  }
                ],
                [
                  'attribute' => 'last_login_date',
                  'filter' => false,
                  'value' => function($model) {
                    return $this->render('//user/admin/_grid_column', ['column' => 'last_login_date', 'model' => $model]);
                  }
                ],
                [
                  'attribute' => 'last_change_password_date',
                  'filter' => false,
                  'value' => function($model) {
                    return $this->render('//user/admin/_grid_column', ['column' => 'last_change_password_date', 'model' => $model]);
                  }
                ],
                [
                  'class' => 'dezero\grid\ActionColumn',
                  'template' => '{update} {delete}',
                  'urlCreator' => function($action, $model, $key, $index) {
                    return Url::to([$action, 'user_id' => $key]);
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
