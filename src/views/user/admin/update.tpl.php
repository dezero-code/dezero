<?php
/*
|--------------------------------------------------------------------------
| UPDATE page for User models
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

$this->title = $user_model->title();
?>
<div class="page-header">
  <h1 class="page-title">
    <?= $this->title; ?>
    <?php
      // Superadmin?
      if ( $user_model->isSuperadmin() ) :
    ?>
      <span class="badge badge-primary ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t('backend', 'Admin - Full Access'); ?>"><?= Yii::t('backend', 'ADMIN'); ?></span>
    <?php endif; ?>
    <?php
      // Disabled user?
      if ( $user_model->isDisabled() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?php if ( !empty($user_model->disable_date) ) : ?>From <?= DateHelper::toFormat($user_model->disable_date); ?><?php else : ?><?= Yii::t('backend', 'Inactive'); ?><?php endif; ?>"><?= Yii::t('backend', 'DISABLED'); ?></span>
    <?php
      // Banned user?
      elseif ( $user_model->isBanned() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t('backend', 'Access is not allowed'); ?>"><?= Yii::t('backend', 'BANNED'); ?></span>
    <?php endif; ?>
    <?php
      // User must change password?
      if ( $user_model->isForceChangePassword() ) :
    ?>
      <span class="badge badge-warning ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t('backend', 'User must change password at next logon'); ?>"><?= Yii::t('backend', 'FORCE CHANGE PASSSWORD'); ?></span>
    <?php endif; ?>
  </h1>
  <div class="page-header-actions">
    <a href="<?= Url::to('/user/admin/'); ?>" class="btn btn-dark"><i class="wb-chevron-left"></i><?= Yii::t('backend', 'Back'); ?></a>
  </div>
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
