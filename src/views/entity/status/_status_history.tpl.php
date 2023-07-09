<?php
/*
|--------------------------------------------------------------------------
| Entity status history table
|--------------------------------------------------------------------------
|
| Available variables:
|  - $model: Entity model
|  - $vec_status_history_models: Array with all the StatusHistory models
|  - $status_type: Current status type
|  - $vec_status_labels: Array with all the status labels
|  - $vec_status_color: Optional. Array with all the status colors
|  - $container_options: Optional. Array with HTML options
|
*/
?>
<div class="table-responsive table-status-history">
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th><?= Yii::t('backend', 'Date'); ?></th>
        <th><?= Yii::t('backend', 'User'); ?></th>
        <th><?= Yii::t('backend', 'Status'); ?></th>
        <th><?= Yii::t('backend', 'Comments'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if ( !empty($vec_status_history_models) ) : ?>
        <?php foreach( $vec_status_history_models as $status_history_model ) : ?>
          <tr>
            <td><?= $status_history_model->created_date; ?></td>
            <td><?= $status_history_model->createdUser->fullname(); ?></td>
            <td class="col-status">
              <?php
                $this->renderPartial('//entity/status/_view_status', [
                  'status_type'       => $status_type,
                  'vec_status_types'  => $vec_status_labels,
                  'vec_status_color'  => $vec_status_color ?? [],
                  'container_options' => $container_options ?? [],
                ]);
              ?>
            </td>
            <td><?= $status_history_model->comments; ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr>
          <td colspan="4"><?= Yii::t('backend', 'No status change have been registered'); ?></td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
