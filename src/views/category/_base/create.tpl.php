<?php
/*
|--------------------------------------------------------------------------
| CREATE page for Category models
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model
|  - $category_parent_model: (Optional) Category parent
|
*/

use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\widgets\ActiveForm;

// Page title
$this->title = Yii::t('backend', $category_model->config->text('create_title'));
if ( $category_parent_model !== null )
{
  $this->title = Yii::t('backend', $category_model->config->text('subcategory_title'), ['subcategory' => $category_parent_model->title()]);
}
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
  <?php
    // Breadcrumbs for a 1st level category
    if ( $category_parent_model === null ) :
  ?>
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
  <?php
    // Breadcrumbs for a children level subcategory
    else :
  ?>
    <?=
      // Breadcrumbs
      Html::breadcrumbs([
        [
          'label' => Yii::t('backend', 'Manage Categories'),
          'url'   => ["/category/{$category_model->category_type}"],
        ],
        [
          'label' => $category_parent_model->title(),
          'url'   => ["/category/{$category_model->category_type}/update", "category_id" => $category_parent_model->category_id],
        ],
        $this->title
      ]);
    ?>
  <?php endif; ?>
</div>

<div class="page-content container-fluid">
  <div class="row row-lg">
    <?php
      /*
      |----------------------------------------------------------------------------------------
      | SUBCATEGORIES? ---> SHOW TREE WIDGET
      |----------------------------------------------------------------------------------------
      */
      if ( $category_parent_model !== null ) :
    ?>
      <div class="col-lg-3">
        <div class="panel panel-tree category-tree-wrapper">
          <header class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('backend', $category_parent_model->config->text('panel_title')); ?></h3>
          </header>
          <div class="panel-body">
            <div id="category-loading-tree" class='dz-loading center hide'></div>
            <div class="dd dd-category-group" id="category-nestable-wrapper" data-name="category" data-max-depth="<?= $category_parent_model->getMaxLevels(); ?>" data-url="<?= Url::to("/category/{$category_parent_model->category_type}/weight"); ?>?category_id=<?= $category_parent_model->category_parent_id; ?>"<?php if ( ! $category_parent_model->config->isEditable() ) : ?> data-readonly="true"<?php endif; ?>>
              <?=
                // Render tree for subcategories
                $this->render($category_parent_model->config->viewPath('_tree'), [
                  'category_model'  => $category_parent_model
                ]);
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-9">
    <?php else : ?>
      <div class="col-lg-12">
    <?php endif; ?>
      <?=
        // Render form
        $this->render($category_model->config->viewPath('_form'), [
          'category_model'        => $category_model,
          'category_parent_model' => $category_parent_model
        ]);
      ?>
    </div>
  </div>
</div>
