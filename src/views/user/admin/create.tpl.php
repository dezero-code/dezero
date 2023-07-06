<?php
/*
|--------------------------------------------------------------------------
| CREATE page for User models
|--------------------------------------------------------------------------
|
| Available variables:
|  - $user_model: User model
|  - $vec_roles: Array with all the roles
|  - $vec_assigned_roles: Array with assigned roles
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;

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
      'user_model'          => $user_model,
      'vec_roles'           => $vec_roles,
      'vec_assigned_roles'  => $vec_assigned_roles
    ]);
  ?>
</div>
