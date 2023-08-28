<?php
/*
|--------------------------------------------------------------------------
| CREATE page for Mymodule models
|--------------------------------------------------------------------------
|
| Available variables:
|  - $mymodule_model: Mymodule model
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;

$this->title = Yii::t('backend', 'Create mymodule');
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
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
