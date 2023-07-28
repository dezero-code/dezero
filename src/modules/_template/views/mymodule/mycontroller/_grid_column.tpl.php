<?php
/*
|--------------------------------------------------------------------------
| GridView column partial page for Mymodule model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $model: Mymodule model
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
    <?= Html::a($model->title(), ['update', 'mymodule_id' => $model->mymodule_id]); ?>
    <?php
      // Disabled mymodule?
      if ( $model->isDisabled() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?php if ( !empty($model->disable_date) ) : ?>From <?= $model->disable_date; ?><?php else : ?><?= Yii::t('backend', 'Inactive'); ?><?php endif; ?>"><?= Yii::t('backend', 'DISABLE'); ?></span>
    <?php endif; ?>
  <?php break; ?>
<?php endswitch; ?>
