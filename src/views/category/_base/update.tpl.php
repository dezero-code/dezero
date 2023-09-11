<?php
/*
|--------------------------------------------------------------------------
| UPDATE page for Category models
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;

$this->title = $category_model->title();
?>
<div class="page-header">
  <h1 class="page-title">
    <?= $this->title; ?>
    <?php
      // Disabled category?
      if ( $category_model->isDisabled() ) :
    ?>
      <span class="badge badge-danger ml-5 mr-5" data-toggle="tooltip" data-placement="top" data-original-title="<?php if ( !empty($category_model->disable_date) ) : ?>From <?= DateHelper::toFormat($category_model->disable_date); ?><?php else : ?><?= Yii::t('backend', 'Inactive'); ?><?php endif; ?>"><?= Yii::t('backend', 'DISABLED'); ?></span>
    <?php endif; ?>
  </h1>
  <div class="page-header-actions">
    <a href="<?= Url::to("/category/{$category_model->category_type}"); ?>" class="btn btn-dark"><i class="wb-chevron-left"></i><?= Yii::t('backend', 'Back'); ?></a>
  </div>
  <?=
    // Breadcrumbs
    Html::breadcrumbs([
      [
        'label' => Yii::t('backend', 'Manage Categories'),
        'url'   => ["/category/{$category_model->category_type}"],
      ],
      $this->title
    ]);
  ?>
</div>

<div class="page-content container-fluid">
  <?=
    // Render form
    $this->render($category_model->viewPath('_form'), [
      'category_model'    => $category_model
    ]);
  ?>
</div>
