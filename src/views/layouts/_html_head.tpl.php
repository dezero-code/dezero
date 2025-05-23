<?php
/*
|--------------------------------------------------------------------------
| HTML <head> layout
|--------------------------------------------------------------------------
|
| Available variables:
|  - $this: dezero\web\View component
|
*/

  use yii\helpers\Html;

  // Get the CORE URL where backend assets are published
  $core_assets_url = Yii::$app->backendManager->coreAssetUrl();

  // Register CSS files
  $this->registerCssBackend();

  // Register JAVASCRIPT files & variables
  $this->registerJsBackend();
?>
<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex">
  <meta name="description" content="<?= Html::encode(Yii::$app->name); ?> - Backend">
  <meta name="author" content="Dezero Framework">

  <?php
    /**
     * FAVICON - Less is more
     * @see https://realfavicongenerator.net/blog/new-favicon-package-less-is-more/
     */

    /*
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::$app->request->baseUrl; ?>/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::$app->request->baseUrl; ?>/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::$app->request->baseUrl; ?>/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= Yii::$app->request->baseUrl; ?>/images/favicon/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="<?= Yii::$app->request->baseUrl; ?>/images/favicon/ms-icon-144x144.png">
    */
  ?>
  <meta name="theme-color" content="#ffffff">
  <?php if ( Yii::$app->user->isGuest ) : ?>
    <?php
      // Check if current action is login page (front page)
      if ( $current_action == 'login' || empty($this->title) ) :
    ?>
      <title><?= Yii::$app->name; ?></title>
    <?php else: ?>
      <title><?= $this->title; ?> | <?= Yii::$app->name; ?></title>
    <?php endif; ?>
  <?php else: ?>
    <title><?= $this->title; ?> | <?= Yii::$app->name; ?></title>
  <?php endif; ?>

  <?php /*<?php $this->registerCsrfMetaTags(); ?>*/ ?>
  <?php $this->head(); ?>

  <!--[if lt IE 9]>
    <script src="<?= $core_assets_url; ?>/libraries/html5shiv/html5shiv.min.js"></script>
  <![endif]-->
  <!--[if lt IE 10]>
    <script src="<?= $core_assets_url; ?>/libraries/media-match/media.match.min.js"></script>
    <script src="<?= $core_assets_url; ?>/libraries/respond/respond.min.js"></script>
  <![endif]-->
  <script src="<?= $core_assets_url; ?>/libraries/breakpoints/breakpoints.min.js"></script>
  <script>Breakpoints();</script>
</head>
