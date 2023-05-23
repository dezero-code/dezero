<?php
/*
|--------------------------------------------------------------------------
| CREATE page for User models
|--------------------------------------------------------------------------
|
| Available variables:
|  - $user_model: User model
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;
use yii\bootstrap\Alert;

$this->title = Yii::t('backend', 'Create user');
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
  <?=
    // Breadcrumbs
    Html::breadcrumbs([
      [
        'label' => Yii::t('backend', 'Manage Users'),
        'url'   => ['/user/admin/'],
      ],
      $this->title
    ]);
  ?>
</div>

<div class="page-content container-fluid">
  <?=
    // Render form
    $this->render('//user/admin/_form', [
      'user_model'  => $user_model,
    ]);
  ?>
</div>
