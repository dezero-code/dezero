<?php
/*
|--------------------------------------------------------------------------
| Main HTML layout
|--------------------------------------------------------------------------
|
| Available variables:
|  - $this: dezero\web\View component
|
*/

  // use yii\bootstrap4\Alert;
  use dezero\widgets\Alert;

  // Layout params
  $vec_params = Yii::$app->backendManager->layoutParams();
  $current_action = $vec_params['current_action'];
  $current_controller = $vec_params['current_controller'];
  $current_module = $vec_params['current_module'];

  // Get body classes
  $body_classes = implode(' ', Yii::$app->backendManager->bodyClasses());
?>

<?php $this->beginPage(); ?>
<!DOCTYPE html>

<html class="no-js css-menubar" lang="<?= Yii::$app->language; ?>">
  <?=
    // <head> & JSS & CSS files
    $this->render('//layouts/_html_head', $vec_params);
  ?>
  <body class="animsition <?= $body_classes; ?>">
  <?php $this->beginBody(); ?>

    <?php
      // Full layout - Login or change password page
      if ( Yii::$app->user->isGuest || ($current_module == 'user' && $current_controller == 'password') ) :
    ?>
      <div class="page vertical-align text-center">
        <div class="page-content vertical-align-middle <?= $current_controller .'-'. $current_action; ?>-content">
          <?= $content ?>
        </div>
      </div>
    <?php
      // Registered users
      else :
    ?>
      <?=
        // HEADER
        $this->render('//layouts/_header', $vec_params);
      ?>
      <?=
        // SIDEBAR
        $this->render('//layouts/_sidebar', $vec_params);
      ?>

      <div class="page">
        <?php /*
        <div id="flash-messages" class="flash-messages-wrapper container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <?php foreach ( Yii::$app->session->getAllFlashes(true) as $type => $message ): ?>
                <?php if (in_array($type, ['success', 'danger', 'warning', 'info'], true)): ?>
                  <?=
                    Alert::widget([
                      'options' => [
                        'class' => 'alert in dark alert-dissimible alert-block alert-' . $type
                      ],
                      'body' => $message,
                    ]);
                  ?>
                <?php endif ?>
              <?php endforeach ?>
            </div>
          </div>
        </div>
        */ ?>
        <div id="flash-messages" class="flash-messages-wrapper container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <?= Alert::widget(); ?>
            </div>
          </div>
        </div>

        <?= $content; ?>
      </div>
    <?php endif; ?>

  <?php $this->endBody(); ?>
  </body>
</html>
<?php $this->endPage(); ?>
