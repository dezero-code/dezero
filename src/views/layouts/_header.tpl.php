<?php
/*
|--------------------------------------------------------------------------
| HEADER partial
|--------------------------------------------------------------------------
|
| Available variables:
|  - $this: dezero\web\View component
|
*/

  use dezero\helpers\Url;

  // Layout params
  $vec_params = Yii::$app->backendManager->layoutParams();
?>
<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">
   <?php
    /*
    |--------------------------------------------------------------------------
    | LOGO
    |--------------------------------------------------------------------------
    */
  ?>
  <div class="navbar-header">
    <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
      data-toggle="menubar">
        <span class="sr-only"><?= Yii::t('backend', 'Show menu'); ?></span>
        <span class="hamburger-bar"></span>
      </button>
      <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse" data-toggle="collapse">
        <i class="icon wb-more-horizontal" aria-hidden="true"></i>
      </button>
      <?=
        // HEADER BRAND (LOGO or TEXT)
        $this->render('//layouts/_header--brand', );
      ?>
  </div>

  <div class="navbar-container container-fluid">
    <!-- Navbar Collapse -->
    <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
      <?php
        /*
        |--------------------------------------------------------------------------
        | NAVBAR TOP LEFT
        |--------------------------------------------------------------------------
        */
      ?>
      <!-- Navbar Toolbar -->
      <ul class="nav navbar-toolbar">
        <li class="nav-item hidden-float" id="toggleMenubar">
          <a class="nav-link" data-toggle="menubar" href="#" role="button">
            <i class="icon hamburger hamburger-arrow-left">
              <span class="sr-only"><?= Yii::t('backend', 'Show menu'); ?></span>
              <span class="hamburger-bar"></span>
            </i>
          </a>
        </li>
      </ul>
      <!-- End Navbar Toolbar -->

      <?php
        /*
        |--------------------------------------------------------------------------
        | NAVBAR TOP RIGHT
        |--------------------------------------------------------------------------
        */
      ?>
     <!-- Navbar Toolbar Right -->
      <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
        <li class="nav-item dropdown">
          <a class="navbar-no-avatar profile-nav-link nav-link" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button" title="<?= Yii::t('backend', 'My profile'); ?>">
            <i class="icon wb-user-circle" aria-hidden="true"></i>
            <span><?=  Yii::$app->user->id > 0 ? Yii::$app->user->model->username : 'User'; ?></span>
          </a>
          <div class="dropdown-menu" role="menu">
            <?php if ( Yii::$app->user->can('user_manage') ) : ?>
              <a class="dropdown-item" href="<?= Url::to('/user/admin/update', ['user_id' => Yii::$app->user->id]); ?>" role="menuitem">
                <i class="icon wb-user" aria-hidden="true"></i> <?= Yii::t('backend', 'My profile'); ?>
              </a>
            <?php endif; ?>
            <?php if ( Yii::$app->user->id == 1 ) : ?>
              <a class="dropdown-item" href="<?= Url::to('/user/logout/loginAs'); ?>" role="menuitem">
                <i class="icon wb-random" aria-hidden="true"></i> <?= Yii::t('backend', 'Log in as...'); ?>
              </a>
              <div class="dropdown-divider" role="presentation"></div>
              <a class="dropdown-item" href="<?= Url::to('/settings/log'); ?>" role="menuitem">
                <i class="icon wb-clipboard" aria-hidden="true"></i> <?= Yii::t('backend', 'File logs'); ?>
              </a>
              <a class="dropdown-item" href="<?= Url::to('/settings/apiLog'); ?>" role="menuitem">
                <i class="icon wb-cloud" aria-hidden="true"></i> <?= Yii::t('backend', 'API logs'); ?>
              </a>
              <a class="dropdown-item" href="<?= Url::to('/settings/backup/'); ?>" role="menuitem">
                <i class="icon wb-replay" aria-hidden="true"></i> <?= Yii::t('backend', 'DB backups'); ?>
              </a>
            <?php endif; ?>
            <?php if ( Yii::$app->user->can('user_manage') ) : ?>
              <div class="dropdown-divider" role="presentation"></div>
            <?php endif; ?>
            <a class="dropdown-item" href="<?= Url::to('/user/logout'); ?>" role="menuitem">
              <i class="icon wb-power" aria-hidden="true"></i> <?= Yii::t('backend', 'Log out'); ?>
            </a>
          </div>
        </li>
        <li>&nbsp;</li>
      </ul>
      <!-- End Navbar Toolbar Right -->
    </div>
    <!-- End Navbar Collapse -->
</nav>
