<?php
/*
|--------------------------------------------------------------------------
| Jasny upload widget - IMAGE style
|--------------------------------------------------------------------------
|
| Available variables:
|  - $field: Input file field
|  - $vec_classes: Array with classes for buttons
|  - $vec_labels: Array with labels for buttons
|
*/
?>
<div class="dz-fileinput fileinput fileinput-new <?= !empty($thumbnail) || !empty($thumbnail_url) ? 'thumbnail-exists' : '' ?>" data-provides="fileinput">
  <div class="fileinput-new img-thumbnail" style="width: 200px; height: 200px;">
    <?php if ( !empty($thumbnail) ) : ?>
      <?= $thumbnail; ?>
    <?php elseif ( !empty($thumbnail_url) ) : ?>
      <img src="<?= $thumbnail_url; ?>" class="fileinput-image">
    <?php endif; ?>
  </div>
  <div class="fileinput-preview fileinput-exists img-thumbnail" style="max-width: 200px; max-height: 200px;"></div>
  <div>
    <span class="btn btn-light btn-file btn-block">
      <span class="fileinput-new"><?= $vec_labels['select']; ?></span>
      <span class="fileinput-exists"><?= $vec_labels['change']; ?></span>
      <?= $field; ?>
    </span>
    <a href="#" class="btn btn-light btn-block fileinput-exists" data-dismiss="fileinput">
      <?= $vec_labels['remove']; ?>
    </a>
  </div>
</div>
