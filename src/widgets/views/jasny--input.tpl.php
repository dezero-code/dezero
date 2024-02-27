<?php
/*
|--------------------------------------------------------------------------
| Jasny upload widget - INPUT style
|--------------------------------------------------------------------------
|
| Available variables:
|  - $field: Input file field
|  - $vec_classes: Array with classes for buttons
|  - $vec_labels: Array with labels for buttons
|
*/
?>
<div class="dz-fileinput fileinput fileinput-new" data-provides="fileinput">
  <div class="input-append">
    <div class="uneditable-input col-sm-12">
      <i class="file-icon-append wb-file"></i>
      <i class="icon-file fileinput-exists"></i>
      <span class="fileinput-preview"></span>
    </div>
  </div>
  <div class="fileinput-button-wrapper">
    <span class="btn btn-file <?= $vec_classes['new']; ?>">
      <span class="fileinput-new"><i class="icon wb-plus"></i><?= $vec_labels['news']; ?></span>
      <span class="fileinput-exists"><i class="icon wb-pencil"></i><?= $vec_labels['change']; ?></span>
      <?= $field; ?>
    </span>
    <a href="javascript:void(0);" class="btn <?= $vec_classes['remove']; ?> fileinput-exists" data-dismiss="fileinput">
      <i class="wb-trash"></i><?= $vec_labels['remove']; ?>
    </a>
  </div>
</div>
