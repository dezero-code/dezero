<?php
/*
|--------------------------------------------------------------------------
| Admin list page for log files
|--------------------------------------------------------------------------
|
| Available variables:
|  - $vec_files: Array with File objects
|
*/
  use dezero\helpers\DateHelper;
  use dezero\helpers\Html;

  // Page title
  $this->title = Yii::t('backend', 'Log Files');
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
</div>

<div class="page-content">
  <div class="row row-lg">
    <div class="col-lg-12">
      <div class="panel panel-top-summary">
        <div class="panel-body container-fluid">
          <table class="items table table-striped table-hover">
            <thead>
              <tr>
                <th><?= Yii::t('backend', 'LOG FILE NAME'); ?></th>
                <th class="center"><?= Yii::t('backend', 'LAST UPDATE DATE'); ?></th>
                <th class="center"><?= Yii::t('backend', 'OWNER:GROUP'); ?></th>
                <th class="center"><?= Yii::t('backend', 'PERMISSIONS'); ?></th>
                <th class="center"><?= Yii::t('backend', 'SIZE'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php if ( empty($vec_files) ) : ?>
                <tr>
                  <td colspan="5"><?= Yii::t('backend', 'No log files found'); ?></td>
              <?php else : ?>
                <?php foreach ( $vec_files as $log_file ) : ?>
                  <tr>
                    <td><?= Html::a($log_file->basename(), ['/system/log/view', 'file' => $log_file->basename()]); ?></td>
                    <td class="center"><?= DateHelper::toFormat($log_file->updatedDate()); ?></td>
                    <td class="center"><?= $log_file->owner() .':'. $log_file->group(); ?></td>
                    <td class="center"><?= $log_file->permissions(); ?></td>
                    <td class="center"><?= $log_file->formatSize(); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
