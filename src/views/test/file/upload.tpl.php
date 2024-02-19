<?php
/*
|--------------------------------------------------------------------------
| UPLOAD page
|--------------------------------------------------------------------------
|
| Available variables:
|  - $user_model: User model
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;

  $this->title = 'UPLOAD FILE TESTING';
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
</div>
<div class="page-content container-fluid">
  <?php
    // Create form object
    $form = ActiveForm::begin([
      'id'                    => 'upload-form',
      'enableAjaxValidation'  => false,
      'layout'                => ActiveForm::LAYOUT_HORIZONTAL,
      'options'               => [
        'enctype' => 'multipart/form-data',
      ]
    ]);

    // Error summary
    echo $form->errorSummary($user_model, [
      'class' => 'mb-30'
    ]);
  ?>

  <div class="panel">
    <header class="panel-heading">
      <h3 class="panel-title"><?= Yii::t('user', 'Upload 1 file'); ?></h3>
    </header>
    <div class="panel-body">
      <div class="row">
        <div class="col-lg-7">
          <?=
            $form->field(
                $user_model,
                'avatarFile',
                [
                  'columns' => [
                    'wrapper' => 'col-sm-9',
                    'label'   => 'col-sm-3',
                  ]
                ]
              )
              ->label('Avatar')
              ->fileInput();
          ?>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-7">
          <?=
            $form->field(
                $user_model,
                'documentFiles',
                [
                  'columns' => [
                    'wrapper' => 'col-sm-9',
                    'label'   => 'col-sm-3',
                  ]
                ]
              )
              ->label('Documents')
              ->fileInput([
                'multiple' => true,
                'name' => 'User[documentFiles][]'
                // 'accept' => 'image/*'
              ]);
          ?>
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
            Yii::t('backend', 'Save'),
            [
              'class' => 'btn btn-primary'
            ]
          );
        ?>
      </div>
    </div>

  <?php
    // End form widget
    ActiveForm::end();
  ?>
</div>
