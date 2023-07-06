<?php
/*
|--------------------------------------------------------------------------
| Form partial page for User model
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

  // Controller and action names in lowercase
  $current_action = \Dz::currentAction(true);

  $form_id = 'user-form'; // $user_model->formName();

  // Create form object
  $form = ActiveForm::begin([
    'id'                    => $form_id,
    'enableAjaxValidation'  => true,
    'layout'                => ActiveForm::LAYOUT_HORIZONTAL,
    'options'               => [
      // 'class' => 'form-horizontal user-form-wrapper',
      'enctype' => 'multipart/form-data',
    ]
  ]);

  // Error summary
  echo $form->errorSummary($user_model, [
    'class' => 'mb-30'
  ]);
?>
<input name="StatusChange" id="status-change" class="form-control" type="hidden" value="">

<?php
  /*
  |--------------------------------------------------------------------------
  | USER ACCESS INFORMATION
  |--------------------------------------------------------------------------
  */
  if ( $current_action === 'create' || $user_model->isEnabled() ) :
?>
<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title"><?= Yii::t('app', 'User Access'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-8">
        <?=
          $form->field(
              $user_model,
              'email',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($user_model->getAttributeLabel('email'))
            ->emailInput([
              'maxlength' => true
            ])
            ->hint(Yii::t('backend', 'Required. User can access via email address or username'));
        ?>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <?=
          $form->field(
              $user_model,
              'username',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($user_model->getAttributeLabel('username'))
            ->textInput([
              'maxlength' => true
            ])
            ->hint(Yii::t('backend', 'Only lowercase characteres or numbers is allowed. Do not enter white spaces'));
        ?>
      </div>
    </div>

    <div class="row<?php if ( $current_action === 'update' ) : ?> password-row hide<?php endif; ?>">
      <div class="col-lg-8">
        <?=
          $form->field(
              $user_model,
              'password',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($user_model->getAttributeLabel('password'))
            ->passwordInput([
              'maxlength' => true
            ])
            ->hint(Yii::t('backend', 'Minimal length 6 characters'));
        ?>
      </div>
    </div>

    <?php
      // Set new password
      if ( $current_action === 'update' ) :
    ?>
      <div class="row password-row hide">
        <div class="col-lg-8">
          <?=
            $form->field(
                $user_model,
                'verify_password',
                [
                  'columns' => [
                    'wrapper' => 'col-sm-9',
                    'label'   => 'col-sm-3',
                  ]
                ]
              )
              ->label($user_model->getAttributeLabel('verify_password'))
              ->passwordInput([
                'maxlength' => true
              ])
              ->hint(Yii::t('backend', 'Retype password'));
          ?>
        </div>
      </div>

      <div class="row password-row">
        <div class="col-lg-8">
          <div class="form-group row">
            <div class="col-sm-3 form-control-label"></div>
            <div class="col-sm-9">
              <a href="#" id="change-password-btn" class="btn btn-dark btn-outline"><?= Yii::t('backend', 'Set new password'); ?></a>
              <input type="hidden" id="is-password-changed" name="IsPasswordChanged" value="0">
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-lg-8">
        <?=
          $form->field(
              $user_model,
              'is_force_change_password',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($user_model->getAttributeLabel('is_force_change_password'))
            ->inline(true)
            ->radioList([
              1 => Yii::t('app', 'Yes'),
              0 => Yii::t('app', 'No'),
            ])
            ->hint(Yii::t('backend', 'If enabled, the user must change the password on the next login'));
        ?>
      </div>
    </div>
  </div>
</div>

<?php endif; ?>

<?php
  /*
  |--------------------------------------------------------------------------
  | PERSONAL DATA
  |--------------------------------------------------------------------------
  */
?>
<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title"><?= Yii::t('app', 'Personal Data'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-7">
        <?=
          $form->field(
              $user_model,
              'first_name',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label(Yii::t('app', 'Name'))
            ->textInput([
              'maxlength' => true,
              'placeholder' => true
            ]);
        ?>
      </div>

      <div class="col-lg-5">
        <?=
          $form->field(
              $user_model,
              'last_name',
              [
                'columns' => [
                  'wrapper' => 'col-sm-12',
                  'offset' => '',
                ]
              ]
            )
            ->label(false)
            ->textInput([
              'maxlength' => true,
              'placeholder' => true
            ]);
        ?>
      </div>
    </div>
  </div>
</div>

<?php
  /*
  |--------------------------------------------------------------------------
  | ROLES and PERMISSIONS
  |--------------------------------------------------------------------------
  */
?>
<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title"><?= Yii::t('app', 'Roles and permissions'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-8">
        <div class="form-group row field-user-roles">
          <?= Html::label(Yii::t('app', 'Roles'), 'user-roles', ['class' => 'col-sm-3 form-control-label']); ?>
          <?= Html::checkboxList('UserRoles', $vec_assigned_roles, $vec_roles, ['class' => 'col-sm-9']); ?>
        </div>
        <?php /*
        <?=
          $form->field(
            $user_model,
            'roles',
            [
              'columns' => [
                'wrapper' => 'col-sm-9',
                'label'   => 'col-sm-3',
              ]
            ]
          )
          ->label(Yii::t('app', 'Roles'))
          ->checkboxList($vec_roles);
        ?>
        */ ?>
      </div>
    </div>
  </div>
</div>

<?php
  /*
  |--------------------------------------------------------------------------
  | ACTIONS
  |--------------------------------------------------------------------------
  */
?>
  <div class="form-group row">
    <div class="col-lg-12 form-actions buttons">
      <?=
        Html::submitButton(
          $current_action === 'create' ? Yii::t('backend', 'Create User') : Yii::t('backend', 'Save'),
          [
            'class' => 'btn btn-primary'
          ]
        );
      ?>
      <?php
        // Cancel
        if ( $current_action === 'create' || $user_model->isEnabled() )
        {
          echo Html::a(Yii::t('backend', 'Cancel'), ['/admin/user'], ['class' => 'btn btn-dark']);
        }
        else
        {
          echo Html::a(Yii::t('backend', 'Cancel'), ['/user/disabled'], ['class' => 'btn btn-dark']);
        }

        // Delete, disable and enable buttons
        if ( $current_action !== 'create' )
        {
          // Disable button
          if ( $user_model->isEnabled() )
          {
            echo Html::a(Yii::t('backend', 'Delete'), ['#'], [
              'id'            => 'delete-user-btn',
              'class'         => 'btn btn-delete right',
              'data-confirm'  => '<h3>Are you sure you want to <span class=\'text-danger\'>DELETE</span> this user?</h3><p><strong>WARNING:</strong> User won\'t be able to access to the platform!</p>',
              'data-form'     => $form_id,
              'data-value'    => 'delete',
              'data-plugin'   => 'dz-status-button'
            ]);
          }
        }
      ?>
    </div><!-- form-actions -->
  </div>
<?php
  // End form widget
  ActiveForm::end();
?>
