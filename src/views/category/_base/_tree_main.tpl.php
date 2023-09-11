<?php
/*
|--------------------------------------------------------------------------
| Partial page for Category tree widget
|--------------------------------------------------------------------------
|
| Available variables:
|  - $category_model: Category model class
|  - $vec_config: Category configuration options
|
*/

  // Get all categories from LEVEL 1
  $vec_category_models = Yii::$app->categoryManager->getAllByDepth($category_model->category_type, 0);
  dd($vec_category_models);
?>
<?php if ( !$is_ajax ) : ?>
  <div id="category-loading-tree" class='dz-loading center hide'></div>
  <div class="dd dd-category-group" id="category-nestable-wrapper" data-name="category" data-url="<?= Url::to("/category/". $current_controller ."/updateWeight"); ?>?category_id=0">
<?php endif; ?>
<?php if ( !empty($vec_categories_models) ) : ?>
  <ol class="dd-list">
    <?php foreach ( $vec_categories_models as $category_model ) : ?>
      <li class="dd-item dd3-item dd-item-group dd-level1" data-rel="level1" data-id="<?= $category_model->category_id; ?>" id="dd-item-<?= $category_model->category_id; ?>">
        <div class="dd-handle dd3-handle"></div>
        <div class="dd3-content">
          <?php
            $vec_html_attributes = [];
            if ( ! $category_model->is_enabled() )
            {
              $vec_html_attributes['class'] = 'text-danger';
            }
            echo Html::link($category_model->name, ['//category/'. $current_controller .'/update', 'id' => $category_model->category_id], $vec_html_attributes);
          ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
<?php else : ?>
  <p><?= $category_model->text('empty_text'); ?></p>
<?php endif; ?>
<?php if ( ! $is_ajax ) : ?>
  </div>
<?php endif; ?>
<?php if ( ! $is_ajax && $vec_config['is_editable'] ) : ?>
  <hr>
  <div class="buttons">
    <?php
      // Add new category
      echo Html::link('<i class="wb-plus"></i> '. $category_model->text('add_button') , ['//category/'. $current_controller .'/create'], [
          'class' => 'btn mr-10 mb-10 btn-primary',
      ]);
    ?>
  </div>
<?php endif; ?>
<?php
  // Custom Javascript nestable code for this page
  if ( ! $is_ajax )
  {
    Yii::app()->clientscript
      ->registerScriptFile(Yii::app()->theme->baseUrl. '/libraries/jquery-nestable/jquery.nestable.js', CClientScript::POS_END)
      ->registerScriptFile(Yii::app()->theme->baseUrl. '/js/dz.nestable.js', CClientScript::POS_END);
      /*
      ->registerScript('category_reset_category_tree_js',
          "// Refresh 'Category Tree' via AJAX
          function commerce_reset_category_tree(que_data_tree){
            var \$category_tree = $('#category-loading-tree');
            var \$category_nestable = $('#category-nestable-wrapper');
            \$category_nestable.nestable('destroy');
            \$category_tree.height(\$category_nestable.height()+'px').removeClass('hide');
            \$category_nestable.html(que_data_tree).removeClass('hide');
            \$category_tree.addClass('hide');
          }", CClientScript::POS_READY);
      */
  }

  if ( ! $vec_config['is_editable'] )
  {
    // Load category nestable
    Yii::app()->clientscript->registerScript('category_tree_js',
      "$('#category-nestable-wrapper').dzNestable({
          maxDepth: 1,
          readOnly: true
      });"
      , CClientScript::POS_READY);
    /*
    Yii::app()->clientscript->registerScript('category_tree_js',
      "// Nestable
      $('#category-nestable-wrapper').nestable({
          maxDepth: 1,
          readOnly: true
      });", CClientScript::POS_READY);
      */
  }
  else
  {
    // Load category nestable
    Yii::app()->clientscript->registerScript('category_tree_js',
      "$('#category-nestable-wrapper').dzNestable({
        maxDepth: 1
      });"
      , CClientScript::POS_READY);
    /*
    Yii::app()->clientscript->registerScript('category_tree_js',
      "// Nestable
      $('#category-nestable-wrapper').nestable({
          maxDepth: 1
      }).on('change', function(){
          var que_nestable = $(this).nestable('serialize');
          var \$this = $(this);
          $('#category-loading-tree').height(\$this.height()+'px').removeClass('hide');
          \$this.addClass('hide');
          $.ajax({
              url: '". $this->createAbsoluteUrl("/category/". $current_controller ."/updateWeight") ."?category_id=0',
              type: 'post',
              dataType: 'json',
              data: {nestable: que_nestable},
              success: function(data) {
                  if ( $('#". $current_controller ."-grid').size() > 0 ) {
                      $.fn.yiiGridView.update('". $current_controller ."-grid');
                  }
                  $('#category-loading-tree').addClass('hide');
                  \$this.removeClass('hide');
              },
              error: function(request, status, error) {
                  alert('ERROR: '+request.responseText);
              },
              cache: false
          });
          // console.log(window.JSON.stringify(que_nestable));
      });", CClientScript::POS_READY);
    */
  }
?>
