<?php
/**
 * EntityConfigurator class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use dezero\base\Configurator;
use dezero\contracts\ConfiguratorInterface;
use dezero\entity\ActiveRecord;
use dezero\helpers\ConfigHelper;
use Yii;

/**
 * Base class to handle configuration options for Entity models
 */
abstract class EntityConfigurator extends Configurator implements ConfiguratorInterface
{
    /**
     * Constructor
     */
    public function __construct(ActiveRecord $model, string $type, array $vec_config = [])
    {
        $this->model = $model;
        $this->type = $type;
        $this->vec_config = $vec_config;

        $this->init();
    }


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Return the view file path for current type
     */
    public function viewPath(string $view_file) : ?string
    {
        return $this->getConfig('views', $view_file);
    }


    /**
     * Return the corresponding text
     */
    public function text(string $text_key) : ?string
    {
        return $this->getConfig('texts', $text_key);
    }
}
