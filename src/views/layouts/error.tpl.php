<?php
/*
|--------------------------------------------------------------------------
| Error page
|--------------------------------------------------------------------------
|
| Available variables:
|  - $this: dezero\web\View component
|  - $name: Error name
|  - $exception \yii\web\HttpException
|
*/
  use dezero\helpers\Url;
  use yii\helpers\Html;

  $this->title = $name;
?>
<section class="error-section">
  <div class="error-container error-wrapper text-center">
    <h1>ERROR <span class="light-text"><?= Html::encode($exception->statusCode); ?></span></h1>

    <h2><?= nl2br(Html::encode(Yii::t('backend', $message))); ?></h2>

    <div class="error-actions">
      <br><br>
      <p>
        <a class="btn btn-primary" href="<?= Url::home(); ?>"><?= Yii::t('backend', 'GO TO MAIN PAGE'); ?></a>
      </p>
    </div>
  </div>
</section>
