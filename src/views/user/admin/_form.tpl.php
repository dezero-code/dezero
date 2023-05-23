<?php
/*
|--------------------------------------------------------------------------
| Form partial page for User model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $user_model: User model
|
*/

  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\widgets\ActiveForm;

  // Controller and action names in lowercase
  $current_action = \Dz::currentAction(true);

  // Create form object
  $form = ActiveForm::begin([
    'id'                    => $user_model->formName(),
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
          $form
            ->field(
              $user_model,
              'email',
              [
                // 'horizontalCssClasses' => [
                //   'wrapper' => 'col-sm-9',
                //   'label'   => 'col-sm-3 form-control-label',
                // ]
              ]
            )
            ->label($user_model->getAttributeLabel('email'))
            ->textInput()
            ->hint('Required. User can use email or username for login');
        ?>
        <p class="text-help"></p>
      </div>
    </div>

    <?php /*
    <div class="row">
      <div class="col-lg-8">
        <div class="form-group row<?php if ( $user_model->hasErrors('username') ) : ?> has-danger<?php endif; ?>">
          <?= $form->label($user_model, 'username', ['class' => 'col-lg-4 col-sm-4 form-control-label']); ?>
          <div class="col-lg-6">
            <?php if ( $current_action === 'create' ) : ?>
              <?=
                $form->textField($user_model, 'username', [
                  'maxlength' => 60,
                  'placeholder' => ''
                ]);
              ?>
              <?= $form->error($user_model, 'username'); ?>
              <p class="text-help"><u>Optional.</u> If empty, it will be generated automatically from the email</p>
            <?php else : ?>
              <div class="form-control view-field"><?= $user_model->username; ?></div>
              <p class="text-help">Username cannot be changed in Wordpress</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row password-row<?php if ( $current_action === 'update' ) : ?> hide<?php endif; ?>">
      <div class="col-lg-8">
        <div class="form-group row<?php if ( $user_model->hasErrors('password') ) : ?> has-danger<?php endif; ?>">
          <?= $form->label($user_model, 'password', ['class' => 'col-lg-4 col-sm-4 form-control-label']); ?>
          <div class="col-lg-6">
            <?=
              // $form->passwordField($user_model, 'password', [
              $form->textField($user_model, 'password', [
                'maxlength' => 128,
                'placeholder' => ''
              ]);
            ?>
            <?= $form->error($user_model, 'password'); ?>
            <p class="text-help">Minimal length 6 characters</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row password-row<?php if ( $current_action === 'update' ) : ?> hide<?php endif; ?>">
      <div class="col-lg-8">
        <div class="form-group row<?php if ( $user_model->hasErrors('verify_password') ) : ?> has-danger<?php endif; ?>">
          <?= $form->label($user_model, 'verify_password', ['class' => 'col-lg-4 col-sm-4 form-control-label']); ?>
          <div class="col-lg-6">
            <?=
              // $form->passwordField($user_model, 'verify_password', [
              $form->textField($user_model, 'verify_password', [
                'maxlength' => 128,
                'placeholder' => ''
              ]);
            ?>
            <?= $form->error($user_model, 'verify_password'); ?>
            <p class="text-help">Retype password</p>
          </div>
        </div>
      </div>
    </div>

    <?php
      // Set new password
      if ( $current_action === 'update' ) :
    ?>
      <div class="row password-row">
        <div class="col-lg-8">
          <div class="form-group row">
            <div class="col-lg-4 col-sm-4 form-control-label"></div>
            <div class="col-lg-6">
              <a href="#" id="change-password-btn" class="btn btn-dark btn-outline">Set new password</a>
              <input type="hidden" id="is-password-changed" name="is-password-changed" value="0">
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    */ ?>
  </div>
</div>

<?php endif; ?>

<?php
  /*
  |--------------------------------------------------------------------------
  | PERSONAL DATA
  |--------------------------------------------------------------------------
  *
?>
<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title"><?= Yii::t('app', 'Personal Data'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-8">
        <div class="form-group row<?php if ( $user_model->hasErrors('wp_firstname') ) : ?> has-danger<?php endif; ?>">
          <?= $form->label($user_model, 'wp_firstname', ['class' => 'col-lg-4 form-control-label']); ?>
          <div class="col-lg-6">
            <?=
              $form->textField($user_model, 'wp_firstname', [
                'maxlength' => 128,
                'placeholder' => ''
              ]);
            ?>
            <?= $form->error($user_model, 'wp_firstname'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="form-group row<?php if ( $user_model->hasErrors('wp_lastname') ) : ?> has-danger<?php endif; ?>">
          <?= $form->label($user_model, 'wp_lastname', ['class' => 'col-lg-4 form-control-label']); ?>
          <div class="col-lg-6">
            <?=
              $form->textField($user_model, 'wp_lastname', [
                'maxlength' => 128,
                'placeholder' => ''
              ]);
            ?>
            <?= $form->error($user_model, 'wp_lastname'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="form-group row<?php if ( $user_model->hasErrors('wp_company') ) : ?> has-danger<?php endif; ?>">
          <?= $form->label($user_model, 'wp_company', ['class' => 'col-lg-4 form-control-label']); ?>
          <div class="col-lg-6">
            <?=
              $form->textField($user_model, 'wp_company', [
                'maxlength' => 128,
                'placeholder' => ''
              ]);
            ?>
            <?= $form->error($user_model, 'wp_company'); ?>
          </div>
        </div>
      </div>
    </div>

    <?php if ( $current_action === 'create' || $user_model->is_enabled() ) : ?>
      <div class="row">
        <div class="col-lg-8">
          <div class="form-group row<?php if ( $user_model->hasErrors('wp_is_auto_registration') ) : ?> has-danger<?php endif; ?>">
            <?= $form->label($user_model, 'wp_is_auto_registration', ['class' => 'col-lg-4 col-sm-4 form-control-label']); ?>
            <div class="col-lg-6">
              <div class="form-group form-radio-group">
                <div class="radio-custom radio-default radio-inline">
                  <input type="radio" id="wp_is_auto_registration-1" name="User[wp_is_auto_registration]" value="1"<?php if ( $user_model->wp_is_auto_registration == 1 ) : ?> checked<?php endif; ?>>
                  <label for="wp_is_auto_registration-1">Yes</label>
                </div>
                <div class="radio-custom radio-default radio-inline">
                  <input type="radio" id="wp_is_auto_registration-0" name="User[wp_is_auto_registration]" value="0"<?php if ( $user_model->wp_is_auto_registration == 0 ) : ?> checked<?php endif; ?>>
                  <label for="wp_is_auto_registration-0">No</label>
                </div>
              </div>
              <?= $form->error($user_model,'wp_is_auto_registration'); ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
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
