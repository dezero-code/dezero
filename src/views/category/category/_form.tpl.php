<?php
/*
|--------------------------------------------------------------------------
| Form partial page for Category model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model
|
*/

  use dezero\helpers\DateHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;
  use dezero\widgets\ActiveForm;
  use dezero\widgets\KrajeeFileInput;

  // Controller and action names in lowercase
  $current_action = \Dz::currentAction(true);

  $form_id = 'category-form'; // $category_model->formName();

  // Create form object
  $form = ActiveForm::begin([
    'id'                    => $form_id,
    'enableAjaxValidation'  => true,
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
    <h3 class="panel-title"><?= Yii::t('category', 'Category Information'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-7">
        <?php if ($current_action !== 'create') : ?>
          <div class="form-group row">
            <?= Html::activeLabel($category_model, 'category_id', ['class' => 'col-sm-3 form-control-label']); ?>
            <div class="col-sm-9">
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
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($category_model->getAttributeLabel('name'))
            ->textInput([
              'maxlength' => true
            ]);
        ?>

        <?=
          $form->field(
              $category_model,
              'description',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
                ]
              ]
            )
            ->label($category_model->getAttributeLabel('description'))
            ->textArea([
              'rows' => 3
            ])
            ->hint(Yii::t('backend', 'Optional'));
        ?>
      </div>
    </div>
  </div>
</div>


<div class="panel">
  <header class="panel-heading">
    <h3 class="panel-title"><?= Yii::t('category', 'Image'); ?></h3>
  </header>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-7">
        <?=
          $form->field(
              $category_model,
              'imageFile',
              [
                'columns' => [
                  'wrapper' => 'col-sm-9',
                  'label'   => 'col-sm-3',
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
        echo Html::a(Yii::t('backend', 'Cancel'), ['/category/category'], ['class' => 'btn btn-dark']);

        // Delete, disable and enable buttons
        if ( $current_action !== 'create' )
        {
          // Disable button
          if ( $category_model->isEnabled() )
          {
            echo Html::a(Yii::t('backend', 'Disable'), ['#'], [
              'id'                => 'disable-category-btn',
              'class'             => 'btn btn-danger right',
              'data-dialog'       => '<h3>Are you sure you want to <span class=\'text-danger\'>DISABLE</span> this category?</h3>',
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
              'data-dialog'       => '<h3>Are you sure you want to <span class=\'text-success\'>ENABLE</span> this category?</h3>',
              'data-form-submit'  => $form_id,
              'data-value'        => 'enable',
              'data-plugin'       => 'dz-status-button'
            ]);
          }

          // Delete button
          echo Html::a(Yii::t('backend', 'Delete'), ['#'], [
            'id'                => 'delete-category-btn',
            'class'             => 'btn btn-delete right',
            'data-dialog'       => '<h3>Are you sure you want to <span class=\'text-danger\'>DELETE</span> this category?</h3><p><strong>WARNING:</strong> All category data will be removed permanently. Please consider disabling the category.</p>',
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
