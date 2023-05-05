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
  use dezero\helpers\Url;
?>
<?php switch ( $column ) :
    /*
    |--------------------------------------------------------------------------
    | COLUMN "id"
    |--------------------------------------------------------------------------
    */
    case 'id':
  ?>
    <?= Html::link($model->id, ['update', 'id' => $model->id]); ?>
    <?php
      // Superadmin?
      if ( $model->is_superadmin == 1 ) :
    ?>
      <span class="badge badge-primary ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="Admin - Full Access">ADMIN</span>
    <?php endif; ?>
    <?php
      // Disabled user?
      if ( $model->is_disabled() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?php if ( !empty($model->disable_date) ) : ?>From <?= $model->disable_date; ?><?php else : ?>Inactivo<?php endif; ?>">DISABLE</span>
    <?php
      // Banned user?
      elseif ( $model->is_banned() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="Baneado">BANNED</span>
    <?php endif; ?>
    <?php
      // User must change password?
      if ( $model->is_force_change_password == 1 ) :
    ?>
      <span class="badge badge-warning ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="User must change password at next logon">FORCE PASSWORD CHANGE</span>
    <?php endif; ?>
  <?php break; ?>
  <?php
    /*
    |--------------------------------------------------------------------------
    | COLUMN "firstname"
    |--------------------------------------------------------------------------
    */
    case 'firstname':
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
      if ( $model->roles() )
      {
        foreach ( $model->roles() as $que_rol)
        {
          $vec_roles[$que_rol->name] = $que_rol->description;
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
    <?= !empty($model->last_login_date) ? $model->last_login_date : Yii::t('app', 'Never'); ?>
  <?php break; ?>
  <?php
    /*
    |--------------------------------------------------------------------------
    | COLUMN "last_change_password_date"
    |--------------------------------------------------------------------------
    */
    case 'last_change_password_date':
  ?>
    <?= ( !empty($model->last_change_password_date) && $model->last_change_password_date !== $model->created_date ) ? $model->last_change_password_date : Yii::t('app', 'Never'); ?>
  <?php break; ?>
<?php endswitch; ?>
