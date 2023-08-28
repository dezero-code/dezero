<?php
/*
|--------------------------------------------------------------------------
| UPDATE page for Mymodule models
|--------------------------------------------------------------------------
|
| Available variables:
|  - $mymodule_model: Mymodule model
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;

$this->title = $mymodule_model->title();
?>
<div class="page-header">
  <h1 class="page-title">
    <?= $this->title; ?>
    <?php
      // Disabled mymodule?
      if ( $mymodule_model->isDisabled() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?php if ( !empty($mymodule_model->disable_date) ) : ?>From <?= DateHelper::toFormat($mymodule_model->disable_date); ?><?php else : ?><?= Yii::t('backend', 'Inactive'); ?><?php endif; ?>"><?= Yii::t('backend', 'DISABLED'); ?></span>
    <?php endif; ?>
  </h1>
  <div class="page-header-actions">
    <a href="<?= Url::to('/mymodule/mymodule/'); ?>" class="btn btn-dark"><i class="wb-chevron-left"></i><?= Yii::t('backend', 'Back'); ?></a>
  </div>
  <?=
    // Breadcrumbs
    Html::breadcrumbs([
      [
        'label' => Yii::t('backend', 'Manage MYMODULE'),
        'url'   => ['/mymodule/mymodule/'],
      ],
      $this->title
    ]);
  ?>
</div>

<div class="page-content container-fluid">
  <?=
    // Render form
    $this->render('//mymodule/mymodule/_form', [
      'mymodule_model'  => $mymodule_model
    ]);
  ?>
</div>
