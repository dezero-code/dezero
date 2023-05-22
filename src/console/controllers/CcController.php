<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\console\controllers;

use dezero\base\File;
use yii\console\Controller;
use yii\helpers\Console;
use Yii;

/**
 * Command to clear assets and cache
 */
class CcController extends Controller
{
    /**
     * Clear cache, assets and temp directories
     *
     * > ./yii cc/all
     */
    public function actionAll()
    {
        $this->actionAssets();
        $this->actionTemp();
        $this->actionCache();
    }


    /**
     * Clear ASSETS directory
     *
     * > ./yii cc/assets
     */
    public function actionAssets()
    {
        $this->clearDirectory('@assets', 'ASSETS');
    }


    /**
     * Clear TEMP directory
     *
     * > ./yii cc/assets
     */
    public function actionTemp()
    {
        $this->clearDirectory('@tmp', 'PUBLIC TEMP');
        $this->clearDirectory('@privateTmp', 'PRIVATE TEMP');
    }


    /**
     * Flush cache
     *
     * > ./yii cc/cache
     */
    public function actionCache()
    {
        Yii::$app->runAction('cache/flush-all');
    }


    /**
     * Clear a directory given as $alias input parameter
     */
    private function clearDirectory($alias, $name)
    {
         $directory = File::load($alias);
        if ( $directory->exists() )
        {
            if ( $directory->clear() )
            {
                $this->stdout("Cleared {$name} directory\n", Console::FG_YELLOW);
                $this->stdout("\t* {$directory->realPath()}\n\n", Console::FG_GREEN);
            }
            else
            {
                $this->stdout("{$name} directory could not be cleared\n", Console::FG_RED);
                $this->stdout("\t* {$directory->realPath()}\n\n", Console::FG_RED);
            }
        }
        else
        {
            $this->stdout("{$name} directory does not exist\n", Console::FG_RED);
            $this->stdout("\t *{$directory->realPath()}\n\n", Console::FG_RED);
        }
    }
}
