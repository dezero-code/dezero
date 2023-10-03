<?php
/*
|--------------------------------------------------------------------------
| HEADER partial - Brand (logo image or text)
|--------------------------------------------------------------------------
|
| Available variables:
|  - $this: dezero\web\View component
|
*/

  use dezero\helpers\Url;

?>
<div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
    <?php
    /*
    <img class="navbar-brand-logo" src="<?= Url::base(); ?>/images/logo/ico-bv.png" alt="<?= Yii::$app->name; ?>">
    <?php /*
    <span class="navbar-brand-logo">
      <svg id="site--logo-svg" class="block w-full h-auto" width="51" height="51" viewBox="0 0 51 51" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path id="logo--icon" class=" -normal" d="M13.948 19.502c-3.576 0-5.75-2.661-5.75-5.68 0-3.055 2.194-5.696 5.75-5.696 3.552 0 5.723 2.641 5.723 5.696 0 3.019-2.151 5.68-5.723 5.68M13.961 0C5.331 0 0 6.411 0 13.84c0 7.327 5.28 13.788 13.961 13.788 8.682 0 13.911-6.461 13.911-13.788C27.872 6.41 22.592 0 13.962 0" fill="#FFC200"></path></g></svg>
    </span>
    */ ?>
    <span class="navbar-brand-logo">DZ</span>
    <span class="navbar-brand-text hidden-xs-down"> <?= Yii::$app->name; ?></span>
  </div>
