<?php
/*
|--------------------------------------------------------------------------
| Admin list page for backup files
|--------------------------------------------------------------------------
|
| Available variables:
|  - $vec_files: Array with File objects
|
*/
  use dezero\helpers\DateHelper;
  use dezero\helpers\Html;
  use dezero\helpers\Url;

  // Page title
  $this->title = Yii::t('backend', 'Datatabase Backup Files');
?>
<div class="page-header">
  <h1 class="page-title"><?= $this->title; ?></h1>
  <div class="page-header-actions">
    <a href="<?= Url::to("/system/backup/create"); ?>" class="btn btn-primary"><i class="wb-plus"></i><?= Yii::t('backend', 'Create new backup'); ?></a>
  </div>
</div>

<div class="page-content">
  <div class="row row-lg">
    <div class="col-lg-12">
      <div class="panel panel-top-summary">
        <div class="panel-body container-fluid">
          <table class="items table table-striped table-hover">
            <thead>
              <tr>
                <th><?= Yii::t('backend', 'DUMP FILE NAME'); ?></th>
                <th class="center"><?= Yii::t('backend', 'LAST UPDATE DATE'); ?></th>
                <th class="center"><?= Yii::t('backend', 'OWNER:GROUP'); ?></th>
                <th class="center"><?= Yii::t('backend', 'PERMISSIONS'); ?></th>
                <th class="center"><?= Yii::t('backend', 'SIZE'); ?></th>
                <th class="center">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php if ( empty($vec_files) ) : ?>
                <tr>
                  <td colspan="6"><?= Yii::t('backend', 'No backup files found'); ?></td>
              <?php else : ?>
                <?php foreach ( $vec_files as $backup_file ) : ?>
                  <tr>
                    <td><?= Html::a($backup_file->basename(), ['/system/backup/download', 'file' => $backup_file->basename()], ['data-method' => 'post']); ?></td>
                    <td class="center"><?= DateHelper::toFormat($backup_file->updatedDate()); ?></td>
                    <td class="center"><?= $backup_file->owner() .':'. $backup_file->group(); ?></td>
                    <td class="center"><?= $backup_file->permissions(); ?></td>
                    <td class="center"><?= $backup_file->formatSize(); ?></td>
                    <td class="center">
                      <a class="delete btn btn-sm btn-icon btn-pure btn-default dz-bootbox-confirm" title="" data-toggle="tooltip" data-confirm="<?= Yii::t('backend', '¿Seguro que desea borrar este backup?'); ?>" data-method="post" href="<?= Url::to('/system/backup/delete', ['file' => $backup_file->basename()]); ?>" data-original-title="<?= Yii::t('backend', 'Borrar'); ?>" ><i class="wb-trash"></i></a>
                    </td>
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
