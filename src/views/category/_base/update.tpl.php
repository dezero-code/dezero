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

  // Current controller
  $current_controller = Dz::currentController(true);

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
    <a href="<?= Url::to("/category/{$current_controller}"); ?>" class="btn btn-dark"><i class="wb-chevron-left"></i><?= Yii::t('backend', 'Back'); ?></a>
  </div>
  <?=
    // Breadcrumbs
    Html::breadcrumbs([
      [
        'label' => Yii::t('backend', $category_model->config->text('entities_label')),
        'url'   => ["/category/{$current_controller}"],
      ],
      $this->title
    ]);
  ?>
</div>

<div class="page-content container-fluid">
  <div class="row row-lg">
    <?php
      /*
      |----------------------------------------------------------------------------------------
      | SUBCATEGORIES? ---> SHOW TREE WIDGET
      |----------------------------------------------------------------------------------------
      */
      if ( $category_model->getMaxLevels() > 1 ) :
    ?>
      <?php
        // First level category
        $first_level_category_model = $category_model->getFirstLevelCategory();
      ?>
      <div class="col-lg-3">
        <div class="panel panel-tree category-tree-wrapper">
          <header class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('backend', $category_model->config->text('panel_title')); ?></h3>
          </header>
          <div class="panel-body">
            <div id="category-loading-tree" class='dz-loading center hide'></div>
            <div class="dd dd-category-group" id="category-nestable-wrapper" data-name="category" data-max-depth="<?= $category_model->getMaxLevels(); ?>" data-url="<?= Url::to("/category/{$current_controller}/weight"); ?>?category_id=<?= $first_level_category_model->category_id; ?>"<?php if ( ! $category_model->config->isEditable() ) : ?> data-readonly="true"<?php endif; ?>>
              <?=
                // Render tree for subcategories
                $this->render($category_model->config->viewPath('_tree'), [
                  'category_model'  => $category_model
                ]);
              ?>
              <?php
                /*
                |----------------------------------------------------------------------------------------
                | ADD BUTTON(s)
                |----------------------------------------------------------------------------------------
                */
                if ( $category_model->config->isEditable() ) :
              ?>
                <hr>
                <div class="buttons">
                  <?php
                    // Get all parent category models
                    $vec_parent_models = $category_model->getAllParents();

                    // ONLY 1 LEVEL ---> Current category is a first level category (no parents)
                    if ( empty($vec_parent_models) ) :
                  ?>
                    <a href="<?= Url::to("/category/{$current_controller}/create", ['parent_id' => $category_model->category_id]); ?>" class="btn mr-10 mb-10 btn-primary"><i class="icon wb-plus"></i> <?= $category_model->config->text('sub_add_button'); ?></a>
                  <?php
                    // MULTIPLE LEVELS ---> Current category has one or more parent categories
                    else :
                  ?>
                    <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true"><i class="wb-plus"></i> <?= $category_model->config->text('sub_add_button'); ?> ... </a>
                    <ul class="dropdown-menu">
                      <?php
                        // Current category can have children?
                        if ( ($category_model->depth + 1) < $category_model->config->getMaxLevels() ) :
                      ?>
                        <li><a href="<?= Url::to("/category/{$current_controller}/create", ['parent_id' => $category_model->category_id]); ?>" class="dropdown-item"><i class="icon wb-plus"></i> <?= $category_model->config->text('sub_add_button'); ?> <?= Yii::t('backend', 'of'); ?> <strong><?= $category_model->title() ?></strong></a></li>
                      <?php endif; ?>
                      <?php
                        // Add button for all the parent categories
                        foreach ( $vec_parent_models as $category_parent_model ) :
                      ?>
                        <li><a href="<?= Url::to("/category/{$current_controller}/create", ['parent_id' => $category_parent_model->category_id]); ?>" class="dropdown-item"><i class="icon wb-plus"></i> <?= $category_model->config->text('sub_add_button'); ?> <?= Yii::t('backend', 'of'); ?> <strong><?= $category_parent_model->title() ?></strong></a></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
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
          'category_model'    => $category_model
        ]);
      ?>
    </div>
  </div>
</div>
