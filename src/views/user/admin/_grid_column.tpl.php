<?php
/*
|--------------------------------------------------------------------------
| GridView column partial page for User model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $model: User model
|  - $column: Column name
|
*/

  use dezero\helpers\DateHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;
?>
<?php switch ( $column ) :
    /*
    |--------------------------------------------------------------------------
    | COLUMN "user_id"
    |--------------------------------------------------------------------------
    */
    case 'user_id':
  ?>
    <?= Html::a($model->id, ['update', 'user_id' => $model->user_id]); ?>
    <?php
      // Superadmin?
      if ( $model->is_superadmin == 1 ) :
    ?>
      <span class="badge badge-primary ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t('backend', 'Admin - Full Access'); ?>"><?= Yii::t('backend', 'ADMIN'); ?></span>
    <?php endif; ?>
    <?php
      // Disabled user?
      if ( $model->isDisabled() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?php if ( !empty($model->disable_date) ) : ?>From <?= $model->disable_date; ?><?php else : ?><?= Yii::t('backend', 'Inactive'); ?><?php endif; ?>"><?= Yii::t('backend', 'DISABLE'); ?></span>
    <?php
      // Banned user?
      elseif ( $model->isBanned() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t('backend', 'Access is not allowed'); ?>"><?= Yii::t('backend', 'BANNED'); ?></span>
    <?php endif; ?>
    <?php
      // User must change password?
      if ( $model->is_force_change_password == 1 ) :
    ?>
      <span class="badge badge-warning ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?= Yii::t('backend', 'User must change password at next logon'); ?>"><?= Yii::t('backend', 'FORCE PASSWORD CHANGE'); ?></span>
    <?php endif; ?>
  <?php break; ?>
  <?php
    /*
    |--------------------------------------------------------------------------
    | COLUMN "name_filter"
    |--------------------------------------------------------------------------
    */
    case 'name_filter':
  ?>
   <?= $model->title(); ?>
  <?php break; ?>
  <?php
    /*
    |--------------------------------------------------------------------------
    | COLUMN "email"
    |--------------------------------------------------------------------------
    */
    case 'email':
  ?>
   <a href="mailto:<?= $model->email; ?>" target="_blank"><?= $model->email; ?></a>
  <?php break; ?>
  <?php
    /*
    |--------------------------------------------------------------------------
    | COLUMN "roles"
    |--------------------------------------------------------------------------
    */
    case 'roles':
  ?>
    <?php
      $vec_roles = array();
      if ( $model->getRoles() )
      {
        foreach ( $model->getRoles() as $role_item)
        {
          $vec_roles[$role_item->name] = $role_item->description;
        }
        echo Html::ul($vec_roles);
      }
    ?>
  <?php break; ?>
  <?php
    /*
    |--------------------------------------------------------------------------
    | COLUMN "last_login_date"
    |--------------------------------------------------------------------------
    */
    case 'last_login_date':
  ?>
    <?= !empty($model->last_login_date) ? DateHelper::toFormat($model->last_login_date) : Yii::t('user', 'Never'); ?>
  <?php break; ?>
  <?php
    /*
    |--------------------------------------------------------------------------
    | COLUMN "last_change_password_date"
    |--------------------------------------------------------------------------
    */
    case 'last_change_password_date':
  ?>
    <?= ( !empty($model->last_change_password_date) && $model->last_change_password_date !== $model->created_date ) ? DateHelper::toFormat($model->last_change_password_date) : Yii::t('backend', 'Never'); ?>
  <?php break; ?>
<?php endswitch; ?>
