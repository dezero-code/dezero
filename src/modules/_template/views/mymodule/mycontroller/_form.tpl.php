<?php
/*
|--------------------------------------------------------------------------
| Form partial page for Mymodule model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $mymodule_model: Mymodule model
|
*/

  use dezero\helpers\DateHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\widgets\ActiveForm;

  // Controller and action names in lowercase
  $current_action = \Dz::currentAction(true);

  $form_id = 'mymodule-form'; // $mymodule_model->formName();

  // Create form object
  $form = ActiveForm::begin([
    'id'                    => $form_id,
    'enableAjaxValidation'  => true,
    'layout'                => ActiveForm::LAYOUT_HORIZONTAL,
    'options'               => [
      // 'class' => 'form-horizontal mymodule-form-wrapper',
      'enctype' => 'multipart/form-data',
    ]
  ]);

  // Error summary
  echo $form->errorSummary($mymodule_model, [
    'class' => 'mb-30'
  ]);
?>
<input name="StatusChange" id="status-change" class="form-control" type="hidden" value="">

<?php
  /*
  |--------------------------------------------------------------------------
  | CATEGORY INFORMATION
  |--------------------------------------------------------------------------
  */
?>
<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title"><?= Yii::t('mymodule', 'Mymodule Information'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-7">
        <?php if ($current_action !== 'create') : ?>
          <div class="form-group row">
            <?= Html::activeLabel($mymodule_model, 'mymodule_id', ['class' => 'col-sm-3 form-control-label']); ?>
            <div class="col-sm-9">
              <p class="form-control-static"><?= $mymodule_model->mymodule_id; ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?=
          $form->field(
              $mymodule_model,
              'name',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($mymodule_model->getAttributeLabel('name'))
            ->textInput([
              'maxlength' => true
            ]);
        ?>

        <?=
          $form->field(
              $mymodule_model,
              'description',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($mymodule_model->getAttributeLabel('description'))
            ->textArea([
              'rows' => 3
            ])
            ->hint(Yii::t('backend', 'Optional'));
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
          $current_action === 'create' ? Yii::t('backend', 'Create') : Yii::t('backend', 'Save'),
          [
            'class' => 'btn btn-primary'
          ]
        );
      ?>
      <?php
        // Cancel
        echo Html::a(Yii::t('backend', 'Cancel'), ['/mymodule/mymodule'], ['class' => 'btn btn-dark']);

        // Delete, disable and enable buttons
        if ( $current_action !== 'create' )
        {
          // Disable button
          if ( $mymodule_model->isEnabled() )
          {
            echo Html::a(Yii::t('backend', 'Disable'), ['#'], [
              'id'                => 'disable-mymodule-btn',
              'class'             => 'btn btn-danger right',
              'data-dialog'       => '<h3>Are you sure you want to <span class=\'text-danger\'>DISABLE</span> this mymodule?</h3>',
              'data-form-submit'  => $form_id,
              'data-value'        => 'disable',
              'data-plugin'       => 'dz-status-button'
            ]);
          }

          // Enable button
          else
          {
            echo Html::a(Yii::t('backend', 'Enable'), ['#'], [
              'id'                => 'enable-mymodule-btn',
              'class'             => 'btn btn-success right',
              'data-dialog'       => '<h3>Are you sure you want to <span class=\'text-success\'>ENABLE</span> this mymodule?</h3>',
              'data-form-submit'  => $form_id,
              'data-value'        => 'enable',
              'data-plugin'       => 'dz-status-button'
            ]);
          }

          // Delete button
          echo Html::a(Yii::t('backend', 'Delete'), ['#'], [
            'id'                => 'delete-mymodule-btn',
            'class'             => 'btn btn-delete right',
            'data-dialog'       => '<h3>Are you sure you want to <span class=\'text-danger\'>DELETE</span> this mymodule?</h3><p><strong>WARNING:</strong> All mymodule data will be removed permanently. Please consider disabling the mymodule.</p>',
            'data-form-submit'  => $form_id,
            'data-value'        => 'delete',
            'data-plugin'       => 'dz-status-button'
          ]);
        }
      ?>
    </div><!-- form-actions -->
  </div>
<?php
  // End form widget
  ActiveForm::end();
?>
