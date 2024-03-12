<?php
/*
|--------------------------------------------------------------------------
| GridView column partial page for Category model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $model: Category model
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
    | COLUMN "name"
    |--------------------------------------------------------------------------
    */
    case 'name':
  ?>
    <?= Html::a($model->title(), ['update', 'category_id' => $model->category_id], ['data-pjax' => 0]); ?>
    <?php
      // Multiple levels?
      if ( $model->getMaxLevels() > 1 && $model->totalSubcategories > 0 ) :
    ?>
      <?= Html::a($model->totalSubcategories .' '. $model->config->text('subcategories'), ['update', 'category_id' => $model->category_id], ['class' => 'btn btn-dark btn-outline btn-sm ml-5', 'data-pjax' => 0]); ?>
    <?php endif; ?>
    <?php
      // Disabled category?
      if ( $model->isDisabled() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?php if ( !empty($model->disable_date) ) : ?>From <?= $model->disable_date; ?><?php else : ?><?= Yii::t('backend', 'Inactive'); ?><?php endif; ?>"><?= Yii::t('backend', 'DISABLE'); ?></span>
    <?php endif; ?>
  <?php break; ?>
<?php endswitch; ?>
