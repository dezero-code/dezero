<?php
/*
|--------------------------------------------------------------------------
| Partial page for Category tree widget
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model class
|
*/
  use dezero\helpers\Url;

  $first_level_category_model = $category_model->getFirstLevelCategory();
?>
<ol class="dd-list">
  <?php
    /*
    |----------------------------------------------------------------------------------------
    | 1st LEVEL CATEGORY
    |----------------------------------------------------------------------------------------
    */
  ?>
  <li class="dd-item dd3-item dd-item-group dd-level1<?php if ( $category_model->category_id == $first_level_category_model->category_id ) : ?> active<?php endif; ?>" data-rel="level1" data-id="<?= $first_level_category_model->category_id; ?>" id="dd-item-<?= $first_level_category_model->category_id; ?>">
    <div class="dd-handle dd3-handle"></div>
    <div class="dd3-content">
      <a href="<?= Url::to("/category/{$first_level_category_model->category_type}/update", ["category_id" => $first_level_category_model->category_id]); ?>"<?php if ( $first_level_category_model->isDisabled() ) : ?> class="text-danger"<?php endif; ?>>
        <?= $first_level_category_model->title(); ?> (<?= $first_level_category_model->totalSubcategories(); ?>)
      </a>
    </div>

    <?php
      /*
      |----------------------------------------------------------------------------------------
      | 2nd LEVEL - SUBCATEGORIES
      |----------------------------------------------------------------------------------------
      */
    ?>
    <ol class="dd-list">
    <?php
      // Get all subcategories
      if ( $first_level_category_model->subCategories ) :
    ?>
      <?php foreach ( $first_level_category_model->subCategories as $subcategory_model ) : ?>
        <li class="dd-item dd3-item dd-item-subcategory<?php if ( $category_model->category_id == $subcategory_model->category_id ) : ?> active<?php endif; ?>" data-rel="level2" data-id="q-<?= $subcategory_model->category_id; ?>" id="dd-item-q-<?= $subcategory_model->category_id; ?>">
          <div class="dd-handle dd3-handle"></div>
          <div class="dd3-content">
            <a href="<?= Url::to("/category/{$subcategory_model->category_type}/update", ["category_id" => $subcategory_model->category_id]); ?>"<?php if ( $subcategory_model->isDisabled() ) : ?> class="text-danger"<?php endif; ?>>
              <?= $subcategory_model->title(); ?>
            </a>
          </div>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </li>
</ol>
