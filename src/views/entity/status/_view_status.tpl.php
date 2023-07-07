<?php
/*
|--------------------------------------------------------------------------
| Status type (fragment HTML) for an entity model
|--------------------------------------------------------------------------
|
| Available variables:
|  - $vec_status_labels: Array with all the status labels
|  - $vec_status_color: Optional. Array with all the status colors
|  - $status_type: Current status type
|  - $options: Optional. Array with options
|
*/
  use dezero\helpers\ArrayHelper;
  use dezero\helpers\Html;

  // Wrapper HTML options
  $options = $options ?? [];
  $options['class'] = $options['class'] ?? '';
  $options['class'] = 'status-type-wrapper inline-block '. $options['class'];
?>
<?= Html::beginTag('div', $options); ?>
  <?php if ( isset($vec_status_labels[$status_type]) ) : ?>
    <?php if ( isset($vec_status_colors) && isset($vec_status_colors[$status_type]) ) : ?>
      <span class="<?= $vec_status_colors[$status_type]; ?>"><i class="wb-medium-point <?= $vec_status_colors[$status_type]; ?>" aria-hidden="true"></i> <?= $vec_status_labels[$status_type]; ?></span>
    <?php else : ?>
      <span><i class="wb-medium-point"></i> <?= $vec_status_labels[$status_type]; ?></span>
    <?php endif; ?>
  <?php else : ?>
    <span><?= $status_type; ?></span>
  <?php endif; ?>
<?= Html::endTag('div'); ?>
