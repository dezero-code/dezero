<?php
/*
|--------------------------------------------------------------------------
| CREATE page for Category models
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;

$this->title = Yii::t('backend', 'Create category');
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
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
      'category_model'  => $category_model
    ]);
  ?>
</div>
