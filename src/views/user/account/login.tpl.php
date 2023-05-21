<?php
/*
|--------------------------------------------------------------------------
| Login page
|--------------------------------------------------------------------------
|
| Available variables:
|  - $model: LoginForm model class
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;
use yii\bootstrap\Alert;

$this->title = Yii::t('backend', 'Login');

?>
<?php if ( isset($loginas_token) && !empty($loginas_token) ) : ?>
  <div class="loginas-message alert dark alert-success alert-dismissible">
    <h4>"LOG IN AS..." ENABLED JUST FOR ADMINISTRATORS</h4>
    <p>Enter an username and your password to log in as another user.</p>
  </div>
<?php endif; ?>
<div class="panel">
  <div class="panel-body">

    <div class="brand">
      <?php /*<img class="brand-img" src="<?= Url::theme(); ?>/images/logo.png" alt="<?= Yii::$app->name; ?>">*/ ?>
      <h1><?= Yii::$app->name; ?></h1>
      <h2 class="brand-text font-size-18"><?= Yii::t('backend', 'ADMINISTRATION PANEL'); ?></h2>
    </div>

    <div class="login-wrapper">
      <?php /*
      <div class="row">
          <div class="col-xs-12">
              <?php foreach (Yii::$app->session->getAllFlashes(true) as $type => $message): ?>
                  <?php if (in_array($type, ['success', 'danger', 'warning', 'info'], true)): ?>
                      <?= Alert::widget(
                          [
                              'options' => ['class' => 'alert-dismissible alert-' . $type],
                              'body' => $message,
                          ]
                      ) ?>
                  <?php endif ?>
              <?php endforeach ?>
          </div>
      </div> */ ?>

      <?php
        $form = ActiveForm::begin(
          [
            'id'                      => $model->formName(),
            'enableAjaxValidation'    => false, // true
          ]
        );

        echo $form->errorSummary($model, [
          'class' => 'mb-30'
        ]);
      ?>
        <?php
          // ADMIN TOKEN (LOGIN AS...)
          if ( isset($loginas_token) && !empty($loginas_token) ) :
        ?>
          <?php
            $model->loginas_token = $loginas_token;
            echo $form->hiddenField($model, 'loginas_token');
          ?>
        <?php endif; ?>

        <div class="form-group<?php if ( $model->hasErrors('username') ) : ?> has-danger<?php endif; ?>">
          <?=
            $form
              ->field(
                $model,
                'username',
                [
                  'errorOptions' => [
                    'class' => 'help-inline text-help text-danger text-left'
                  ]
                ]
              )
              ->textInput([
                'placeholder' => $model->getAttributeLabel('username'),
                'autofocus' => 'autofocus',
                'class' => 'form-control form-control-lg',
                'tabindex' => '1'
              ])
              ->label(
                $model->getAttributeLabel('username'),
                [
                  'class' => 'sr-only',
                ]
              );
          ?>
          <?php //<?= $form->error($model, 'username'); ?>
        </div>

        <div class="form-group<?php if ( $model->hasErrors('password') ) : ?> has-danger<?php endif; ?>">
          <?=
            $form
              ->field(
                $model,
                'password',
                [
                  'errorOptions' => [
                    'class' => 'help-inline text-help text-danger text-left'
                  ]
                ]
              )
              ->passwordInput([
                'placeholder' => $model->getAttributeLabel('password'),
                'class' => 'form-control form-control-lg',
                'tabindex' => '2'
              ])
              ->label(
               $model->getAttributeLabel('password'),
                [
                  'class' => 'sr-only',
                ]
              );
          ?>
          <p class="help-block"><a href="<?= Url::to('/user/password'); ?>"><?= Yii::t('backend', 'Forgot password?'); ?></a></p>
          <?php //<?= $form->error($model, 'password'); ?>
        </div>

        <?php /*
        <label class="checkbox">
          <?= $form->checkBox($model, 'is_remember'); ?> <i><?php echo Yii::t('backend', 'Remember'); ?></i>
        </label>
        */ ?>

        <?=
          Html::submitButton(
            Yii::t('backend', 'Login'),
            [
              'class' => 'btn btn-block btn-lg mt-40 btn-primary',
              'tabindex' => '3'
            ]
          );
        ?>
      <?php
        // End form widget
        ActiveForm::end();
      ?>
    </div>

    <p><a href="<?= Url::to('/'); ?>"><?= Yii::t('backend', 'Go to public website'); ?></a></p>
  </div>
</div>
