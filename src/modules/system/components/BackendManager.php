<?php
/**
 * Backend Manager
 *
 * Helper classes collection for backend theme
 */

namespace dezero\modules\system\components;

use Dz;
use Yii;
use yii\base\Component;

/**
 * BackendManager - Helper classes collection for backend theme
 */
class BackendManager extends Component
{
    /**
     * @var string Current controller name
     */
    public $current_controller;


    /**
     * @var string Current action name
     */
    public $current_action;


    /**
     * @var string Current module name
     */
    public $current_module;


    /**
     * Init function. Required!
     */
    public function init()
    {
        parent::init();

        if ( Dz::currentTheme() === 'backend' )
        {
            $this->current_controller = Dz::currentController(true);
            $this->current_action = Dz::currentAction(true);
            $this->current_module = Dz::currentModule(true);
        }
    }


    /**
     * Return params used on main layout and partials "_header.tpl.php" and "_footer.tpl.php"
     */
    public function layoutParams() : array
    {
        return [
            'current_module'        => Dz::currentModule(true),
            'current_controller'    => Dz::currentController(true),
            'current_action'        => Dz::currentAction(true),
            'language_id'           => Yii::$app->language,
            'is_logged_in'          => false, // ! Yii::$app->user->isGuest,
            'path_info'             => Yii::$app->request->getPathInfo(),
        ];
    }


    /**
     * Get body classes
     */
    public function bodyClasses() : array
    {
        $vec_classes = [
            // Current action
            $this->current_action . '-page',

            // Current controller and action
            $this->current_controller . '-' . $this->current_action . '-page',

            // Logged in?
            // ( Yii::app()->user->isGuest ? 'not-logged-in' : 'logged-in')
        ];

        // Login & password page (different layout)
        // if ( Yii::app()->user->isGuest || ($this->current_module == 'user' && $this->current_controller == 'password') )
        if ( ($this->current_module == 'user' && $this->current_controller == 'password') )
        {
            $vec_classes[] = 'page-login';
            $vec_classes[] = 'layout-full';
        }

        // Logged-in pages -> Enable main menu
        else
        {
            $vec_classes[] = 'site-navbar-small';

            // Left sidebar column
            if ( $this->current_module == 'admin' && ( $this->current_controller == 'mail' && $this->current_action !== 'view' ) )
            {
                $vec_classes[] = 'page-aside-static';
                $vec_classes[] = 'page-aside-left';
            }
        }

        return $vec_classes;
    }
}
