<?php
/*
|--------------------------------------------------------------------------
| Jasny upload widget - BUTTON style
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
  <div class="fileinput-button-wrapper">
    <span class="btn btn-file <?= $vec_classes['new']; ?>">
      <span class="fileinput-new"><i class="wb-plus"></i> <?= $vec_labels['new']; ?></span>
      <span class="fileinput-exists"><i class="wb-pencil"></i> <?= $vec_labels['change']; ?></span>
      <?= $field; ?>
    </span>
    <span class="fileinput-filename"></span>
    <a href="javascript:void(0);" class="close fileinput-exists <?= $vec_classes['remove']; ?>" data-dismiss="fileinput" style="float: none"><i class="wb-trash"></i></a>
  </div>
</div>
