<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\components;

use dezero\modules\category\models\Category;
use Yii;
use yii\base\Component;

/**
 * CategoryManager - Helper classes collection for category theme
 */
class CategoryManager extends Component
{
    /**
     * @var array
     */
    protected $vec_config;


    /**
     * Load configuration options
     */
    public function loadConfig() : void
    {
        if ( empty($this->vec_config) )
        {
            $this->vec_config = Yii::$app->config->get('categories');
        }
    }


    /**
     * Return the configuration options for a category type
     */
    public function getConfig(?string $category_type = null) : ?array
    {
        $this->loadConfig();

        if ( $category_type !== null && isset($this->vec_config[$category_type]) )
        {
            return $this->vec_config[$category_type];
        }

        return Yii::$app->categoryManager->defaultConfiguration();
    }


    /**
     * Return the default configuration for a category type
     */
    public function defaultConfiguration() : array
    {
        return [
            // Allowed actions
            'is_editable'               => true,
            'is_disable_allowed'        => true,
            'is_delete_allowed'         => true,
            'is_first_level_sortable'   => true,

            // Max depth level
            'max_depth' => 1,
        ];
    }


    /**
     * Return the view file path for a category type
     */
    public function viewPath(string $view_file, ?string $category_type = null) : string
    {
        // Default view paths
        $vec_views = [
            'index'         => '//category/_base/index',
            'create'        => '//category/_base/create',
            'update'        => '//category/_base/update',
            '_form'         => '//category/_base/_form',
            '_form_seo'     => '//category/_base/_form_seo',
            '_grid_column'  => '//category/_base/_grid_column',
            '_tree'         => '//category/_base/_tree',
            '_tree_main'    => '//category/_base/_tree_main',
        ];

        // Return view path from configuration file
        if ( $category_type !== null )
        {
            $vec_config = Yii::$app->categoryManager->getConfig($category_type);
            if ( !empty($vec_config) && isset($vec_config['views']) && isset($vec_config['views'][$view_file]) )
            {
                return $vec_config['views'][$view_file];
            }
        }

        // Return view path from default values
        if ( isset($vec_views[$view_file]) )
        {
            return $vec_views[$view_file];
        }

        // No view has been found
        return $view_file;
    }


    /**
     * Return the corresponding text
     */
    public function text(string $text_key, ?string $category_type = null) : string
    {
        $vec_texts = [
            'entity_label'      => 'Category',
            'subentity_label'   => 'Subcategory',
            'entities_label'    => 'Categories',

            'index_title'       => 'Manage categories',
            'panel_title'       => 'Categories',
            'list_title'        => 'Categories list',
            'add_button'        => 'Add category',
            'create_title'      => 'Create category',
            'subcategory_title' => 'Create subcategory of "{subcategory}"',

            // Success messages
            'created_success'   => 'New category created successfully',
            'updated_success'   => 'Category updated successfully',

            // Disable
            'disable_success'   => 'Category DISABLED successfully',
            'disable_error'     => 'Category could not be DISABLED',
            'disable_confirm'   => '<h3>Are you sure you want to <span class=\'text-danger\'>DISABLE</span> this category?</h3>',

            // Enable
            'enable_success'    => 'Category ENABLED successfully',
            'enable_error'      => 'Category could not be ENABLED',
            'enable_confirm'    => '<h3>Are you sure you want to <span class=\'text-success\'>ENABLE</span> this category?</h3>',

            // Delete
            'delete_success'    => 'Category DELETED successfully',
            'delete_error'      => 'Category could not be DELETED',
            'delete_confirm'    => '<h3><i class="icon wb-alert-circle text-danger"></i> Are you sure you want to <span class=\'text-danger\'>DELETE</span> this category?</h3>',

            // Other
            'subcategories'     => 'subcategories',
            'empty_text'        => 'No categories found'
        ];


        // Return view path from configuration file
        if ( $category_type !== null )
        {
            $vec_config = Yii::$app->categoryManager->getConfig($category_type);
            if ( !empty($vec_config) && isset($vec_config['texts']) && isset($vec_config['texts'][$text_key]) )
            {
                return $vec_config['texts'][$text_key];
            }
        }

        // Return view path from default values
        if ( isset($vec_texts[$text_key]) )
        {
            return $vec_texts[$text_key];
        }

        return '';
    }


    /**
     * Get Category models from LEVEL 1
     */
    public function getAllByDepth(string $category_type, int $depth = 0) : ?array
    {
        return Category::find()
            ->category_type($category_type)
            ->depth($depth)
            ->orderBy(['weight' => SORT_ASC])
            ->all();
    }
}
