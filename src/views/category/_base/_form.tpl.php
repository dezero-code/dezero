<?php
/*
|--------------------------------------------------------------------------
| Form partial page for Category model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model
|  - $category_parent_model: (Optional) Category parent
|
*/

  use dezero\helpers\DateHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\widgets\ActiveForm;
  use dezero\widgets\KrajeeFileInput;

  // Controller and action names in lowercase
  $current_controller = \Dz::currentController(true);
  $current_action = \Dz::currentAction(true);

  $form_id = 'category-form'; // $category_model->formName();

  // Create form object
  $form = ActiveForm::begin([
    'id'                    => $form_id,
    'enableAjaxValidation'  => false, // true,
    'layout'                => ActiveForm::LAYOUT_HORIZONTAL,
    'options'               => [
      // 'class' => 'form-horizontal category-form-wrapper',
      'enctype' => 'multipart/form-data',
    ]
  ]);

  // Error summary
  echo $form->errorSummary($category_model, [
    'class' => 'mb-30'
  ]);

  // Editable?
  $is_editable = $category_model->config->isEditable();
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
    <h3 class="panel-title"><?php if ( $category_model->depth > 0 || $category_model->category_parent_id !== null ) : ?><?= $category_model->config->text('subentity_label'); ?><?php else : ?><?= $category_model->config->text('entity_label'); ?><?php endif; ?> <?= Yii::t('backend', 'Information'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-11">
        <?php if ($current_action !== 'create') : ?>
          <div class="form-group row">
            <?= Html::activeLabel($category_model, 'category_id', ['class' => 'col-sm-2 form-control-label']); ?>
            <div class="col-sm-10">
              <p class="form-control-static"><?= $category_model->category_id; ?></p>
            </div>
          </div>
        <?php endif; ?>

        <?=
          $form->field(
              $category_model,
              'name',
              [
                'columns' => [
                  'wrapper' => 'col-sm-10',
                  'label'   => 'col-sm-2',
                ]
              ]
            )
            ->label($category_model->getAttributeLabel('name'))
            ->textInput([
              'maxlength' => true,
              'disabled' => ! $is_editable
            ]);
        ?>

        <?php if ( $category_model->config->isDescription() ) : ?>
          <?=
            $form->field(
                $category_model,
                'description',
                [
                  'columns' => [
                    'wrapper' => 'col-sm-10',
                    'label'   => 'col-sm-2',
                  ]
                ]
              )
              ->label($category_model->getAttributeLabel('description'))
              ->textArea([
                'rows' => 3,
                'disabled' => ! $is_editable
              ])
              ->hint(Yii::t('backend', 'Optional'));
          ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
  /*
  |--------------------------------------------------------------------------
  | IMAGE
  |--------------------------------------------------------------------------
  */
    if ( $category_model->config->isImage() ) :
  ?>
  <div class="panel">
    <header class="panel-heading">
      <h3 class="panel-title"><?= Yii::t('backend', 'Image'); ?></h3>
    </header>
    <div class="panel-body">
      <div class="row">
        <div class="col-lg-11">
          <?=
            $form->field(
                $category_model,
                // 'imageFile',
                'image_file_id',
                [
                  'columns' => [
                    'wrapper' => 'col-sm-10',
                    'label'   => 'col-sm-2',
                  ]
                ]
              )
              ->label($category_model->getAttributeLabel('imageFile'))
              ->widget(KrajeeFileInput::class, [
                'options' => [
                  // 'accept' => 'image/*',
                  'multiple' => false,
                ],
                'pluginOptions' => [
                  // 'showPreview' => true,
                  'dropZoneEnabled' => false,
                  'showCaption' => true,
                  'showRemove' => true,
                  'showUpload' => false,
                  'showClose' => false,
                ]
              ]);
          ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php
  /*
  |--------------------------------------------------------------------------
  | ACTIONS
  |--------------------------------------------------------------------------
  */
  if ( $is_editable ) :
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
        echo Html::a(Yii::t('backend', 'Cancel'), ['/category/'. $current_controller], ['class' => 'btn btn-dark']);

        // Delete, disable and enable buttons
        if ( $current_action !== 'create' )
        {
          // Disable button
          if ( $category_model->isEnabled() )
          {
            echo Html::a(Yii::t('backend', 'Disable'), ['#'], [
              'id'                => 'disable-category-btn',
              'class'             => 'btn btn-danger right',
              'data-dialog'       => Yii::t('backend', $category_model->config->text('disable_confirm')),
              'data-form-submit'  => $form_id,
              'data-value'        => 'disable',
              'data-plugin'       => 'dz-status-button'
            ]);
          }

          // Enable button
          else
          {
            echo Html::a(Yii::t('backend', 'Enable'), ['#'], [
              'id'                => 'enable-category-btn',
              'class'             => 'btn btn-success right',
              'data-dialog'       => Yii::t('backend', $category_model->config->text('enable_confirm')),
              'data-form-submit'  => $form_id,
              'data-value'        => 'enable',
              'data-plugin'       => 'dz-status-button'
            ]);
          }

          // Delete button
          echo Html::a(Yii::t('backend', 'Delete'), ['#'], [
            'id'                => 'delete-category-btn',
            'class'             => 'btn btn-delete right',
            'data-dialog'       => Yii::t('backend', $category_model->config->text('delete_confirm')),
            'data-form-submit'  => $form_id,
            'data-value'        => 'delete',
            'data-plugin'       => 'dz-status-button'
          ]);
        }
      ?>
    </div><!-- form-actions -->
  </div>
<?php endif; ?>
<?php
  // End form widget
  ActiveForm::end();
?>
