<?php
/*
|--------------------------------------------------------------------------
| Main HTML layout
|--------------------------------------------------------------------------
*/

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

    <?php /*<h1>BACKEND FROM CORE</h1>*/ ?>

    <?=
      // HEADER
      $this->render('//layouts/_header', $vec_params);
    ?>
    <?=
      // SIDEBAR
      $this->render('//layouts/_sidebar', $vec_params);
    ?>
    <div class="page">
      <?= $content; ?>
    </div>

  <?php $this->endBody(); ?>
  </body>
</html>
<?php $this->endPage(); ?>
