<?php
/*
|--------------------------------------------------------------------------
| Change status of an entity model - Slide Panel
|--------------------------------------------------------------------------
|
| Available variables:
|  - $model: Entity model
|  - $buttonOptions: HTML options for SAVE button
|
*/
  use dezero\helpers\Html;

?>
<header class="slidePanel-header">
  <div class="slidePanel-actions" aria-label="actions" role="group">
    <button type="button" class="btn btn-pure btn-inverse slidePanel-close actions-top icon wb-close" aria-hidden="true"></button>
    <?php /*
    <div class="btn-group actions-bottom" role="group">
      <button type="button" class="btn btn-pure btn-inverse icon wb-chevron-left" aria-hidden="true"></button>
      <button type="button" class="btn btn-pure btn-inverse icon wb-chevron-right" aria-hidden="true"></button>
    </div>
    */ ?>
  </div>
  <h1><?= $model->title(); ?> - <?= Yii::t('backend', 'Change Status'); ?></h1>
</header>
<div class="slidePanel-inner">
  <section class="slidePanel-inner-section">
    <div class="row">
      <label class="col-lg-3 form-control-label"><?= Yii::t('backend', 'Current status'); ?></label>
      <div class="col-lg-9">
        <?=
          $this->render('//entity/status/_view_status', [
            'status_type'        => $model->status_type,
            'vec_status_labels'  => $model->status_type_labels(),
            'vec_status_colors'  => $model->status_type_colors()
          ]);
        ?>
      </div>
    </div>

    <div class="row">
      <label class="col-lg-3 pt-5 form-control-label"><?= Yii::t('backend', 'New status'); ?></label>
      <div class="col-lg-9">
        <?=
          Html::activeDropDownList($model, 'status_type', $model->status_type_labels(), [
            // 'id'           => Html::getInputId($model, 'status_type'),
            'data-init-value' => $model->status_type
          ]);
        ?>
      </div>
    </div>


    <div class="row">
      <label class="col-lg-3 form-control-label"><?= Yii::t('backend', 'Internal comments'); ?></label>
      <div class="col-lg-9">
        <textarea id="Status_comments" class="maxlength-textarea form-control mb-sm" rows="3"></textarea>
        <p class="help-block"><?= Yii::t('backend', 'Private comments'); ?></p>
      </div>
    </div>

    <?php /*
    <div id="send-mail-row" class="form-group row hide">
      <label class="col-lg-3 form-control-label"><?= Yii::t('backend', 'Send email?'); ?></label>
      <div class="col-lg-9">
        <div id="document-is-sending-mail" class="form-group form-radio-group">
          <div class="radio-custom radio-default radio-inline">
            <input type="radio" id="is_sending_mail-1" name="User[is_sending_mail]" value="1"<?php if ( $model->is_sending_mail == 1 ) : ?> checked<?php endif; ?>>
            <label for="User[is_sending_mail]"><?= Yii::t('backend', 'Yes'); ?></label>
          </div>
          <div class="radio-custom radio-default radio-inline">
            <input type="radio" id="is_sending_mail-0" name="User[is_sending_mail]" value="0"<?php if ( $model->is_sending_mail == 0 ) : ?> checked<?php endif; ?>>
            <label for="User[is_sending_mail]"><?= Yii::t('backend', 'No'); ?></label>
          </div>
        </div>
      </div>
    </div>
    */ ?>

    <div class="row">
      <label class="col-lg-3 form-control-label"></label>
      <div class="col-lg-9">
        <button class="btn btn-primary" data-dismiss="modal" type="button" disabled<?php if ( isset($buttonOptions) ) : ?><?= Html::renderTagAttributes($buttonOptions); ?><?php endif; ?>><?= Yii::t('backend', 'Change status'); ?></button>
      </div>
    </div>

  </section>
</div>
