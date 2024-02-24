<?php
/*
|--------------------------------------------------------------------------
| View page for log file
|--------------------------------------------------------------------------
|
| Available variables:
|  - $log_file: Log file object
|
*/
  use dezero\helpers\DateHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;

  // Page title
  $this->title = Yii::t('backend', $log_file->basename());
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
  <div class="page-header-actions">
    <a href="<?= Url::to("/system/log"); ?>" class="btn btn-dark"><i class="wb-chevron-left"></i><?= Yii::t('backend', 'Back'); ?></a>
  </div>
  <?=
    // Breadcrumbs
    Html::breadcrumbs([
      [
        'label' => Yii::t('backend', 'View Logs'),
        'url' => ['/system/log'],
      ],
      $this->title
    ]);
  ?>
</div>

<div class="page-content container-fluid">
  <div class="panel">
    <div class="panel-body panel-view-content">
      <div class="row">
        <div class="col-sm-3">
          <h5><?= Yii::t('backend', 'Last modification date'); ?></h5>
          <div class="item-content"><?= DateHelper::toFormat($log_file->updatedDate()); ?></div>
        </div>

        <div class="col-sm-3">
          <h5><?= Yii::t('backend', 'Last modification user'); ?></h5>
          <div class="item-content"><?= $log_file->owner() .':'. $log_file->group(); ?></div>
        </div>

        <div class="col-sm-3">
          <h5><?= Yii::t('backend', 'File permissions'); ?></h5>
          <div class="item-content"><?= $log_file->permissions(); ?></div>
        </div>

        <div class="col-sm-3">
          <h5><?= Yii::t('backend', 'File size'); ?></h5>
          <div class="item-content"><?= $log_file->formatSize(); ?></div>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-sm-12">
          <div id="log-content" class="item-content log-information-field">
            <?= $content_log; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
