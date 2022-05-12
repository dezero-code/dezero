<?php
/*
|--------------------------------------------------------------------------
| HEADER partial
|--------------------------------------------------------------------------
|
*/

  use dezero\helpers\Url;

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
        <span class="sr-only"><?= Yii::t('app', 'Show menu'); ?></span>
        <span class="hamburger-bar"></span>
      </button>
      <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse" data-toggle="collapse">
        <i class="icon wb-more-horizontal" aria-hidden="true"></i>
      </button>
      <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
        <?php /*<img class="navbar-brand-logo" src="<?= Url::theme(); ?>/images/logo/ico-bv.png" alt="<?= Yii::$app->name; ?>">*/ ?>
        <span class="navbar-brand-logo">
          <svg id="site--logo-svg" class="block w-full h-auto" width="51" height="51" viewBox="0 0 51 51" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path id="logo--icon" class=" -normal" d="M13.948 19.502c-3.576 0-5.75-2.661-5.75-5.68 0-3.055 2.194-5.696 5.75-5.696 3.552 0 5.723 2.641 5.723 5.696 0 3.019-2.151 5.68-5.723 5.68M13.961 0C5.331 0 0 6.411 0 13.84c0 7.327 5.28 13.788 13.961 13.788 8.682 0 13.911-6.461 13.911-13.788C27.872 6.41 22.592 0 13.962 0" fill="#FFC200"></path></g></svg>
        </span>
        <span class="navbar-brand-text hidden-xs-down"> <?= Yii::$app->name; ?></span>
      </div>
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
              <span class="sr-only"><?= Yii::t('app', 'Show menu'); ?></span>
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
          <a class="navbar-no-avatar profile-nav-link nav-link" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button" title="<?= Yii::t('app', 'My profile'); ?>">
            <i class="icon wb-user-circle" aria-hidden="true"></i>
            <?php /*<span><?php echo Yii::app()->user->username; ?></span>*/ ?>
            <span>Admin</span>
          </a>
          <div class="dropdown-menu" role="menu">
            <a class="dropdown-item" href="<?= Url::to('/user/admin/update', ['id' => Yii::$app->user->id]); ?>" role="menuitem">
              <i class="icon wb-user" aria-hidden="true"></i> <?= Yii::t('app', 'My profile'); ?>
            </a>
            <?php if ( Yii::$app->user->id == 1 ) : ?>
              <a class="dropdown-item" href="<?= Url::to('/user/logout/loginAs'); ?>" role="menuitem">
                <i class="icon wb-random" aria-hidden="true"></i> <?= Yii::t('app', 'Log in as...'); ?>
              </a>
              <div class="dropdown-divider" role="presentation"></div>
              <a class="dropdown-item" href="<?= Url::to('/settings/log'); ?>" role="menuitem">
                <i class="icon wb-clipboard" aria-hidden="true"></i> <?= Yii::t('app', 'File logs'); ?>
              </a>
              <a class="dropdown-item" href="<?= Url::to('/settings/apiLog'); ?>" role="menuitem">
                <i class="icon wb-cloud" aria-hidden="true"></i> <?= Yii::t('app', 'API logs'); ?>
              </a>
              <a class="dropdown-item" href="<?= Url::to('/settings/backup/'); ?>" role="menuitem">
                <i class="icon wb-replay" aria-hidden="true"></i> <?= Yii::t('app', 'DB backups'); ?>
              </a>
            <?php endif; ?>
            <div class="dropdown-divider" role="presentation"></div>
            <a class="dropdown-item" href="<?= Url::to('/user/logout'); ?>" role="menuitem">
              <i class="icon wb-power" aria-hidden="true"></i> <?= Yii::t('app', 'Log out'); ?>
            </a>
          </div>
        </li>
        <li>&nbsp;</li>
      </ul>
      <!-- End Navbar Toolbar Right -->
    </div>
    <!-- End Navbar Collapse -->
</nav>
