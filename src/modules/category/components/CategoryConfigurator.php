<?php
/**
 * CategoryConfigurator class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\components;

use dezero\contracts\ConfiguratorInterface;
use dezero\entity\EntityConfigurator;
use Yii;

/**
 * Base class to handle configuration options for Category models
 */
class CategoryConfigurator extends EntityConfigurator implements ConfiguratorInterface
{
    /**
     * Load the configuration for a specific type
     */
    public function loadConfiguration() : array
    {
        $vec_config = Yii::$app->config->get('components/categories', $this->type);
        if ( $vec_config === null )
        {
            return [];
        }

        $this->vec_config = $vec_config;
        return $this->vec_config;
    }


    /**
     * Return the default configuration for the category type
     */
    public function defaultConfiguration() : array
    {
        // Try with default configuration defined on "/app/config/categories"
        $vec_config = Yii::$app->config->get('components/categories', 'default');
        if ( $vec_config !== null )
        {
            return $vec_config;
        }

        return [
            // Actions allowed
            'is_editable'               => true,
            'is_disable_allowed'        => true,
            'is_delete_allowed'         => true,
            'is_first_level_sortable'   => true,

            // Max levels (subcategories)
            'max_levels'                => 3,

            // Optional fields
            'is_multilanguage'          => true,
            'is_description'            => true,
            'is_image'                  => true,
            'is_seo_fields'             => true,

            // Custom path for images
            'images_path'               => 'www' . DIRECTORY_SEPARATOR .'files'. DIRECTORY_SEPARATOR .'images'. DIRECTORY_SEPARATOR .'category'. DIRECTORY_SEPARATOR,

            // View files path
            'views' => [
                'index'             => '//category/_base/index',
                'create'            => '//category/_base/create',
                'update'            => '//category/_base/update',
                '_form'             => '//category/_base/_form',
                '_form_seo'         => '//category/_base/_form_seo',
                '_grid_column'      => '//category/_base/_grid_column',
                '_tree_main'        => '//category/_base/_tree_main',
                '_tree'             => '//category/_base/_tree',
                '_tree_sublevels'   => '//category/_base/_tree_sublevels',
            ],

            // Texts
            'texts' => [
                'entity_label'      => 'Category',
                'subentity_label'   => 'Subcategory',
                'entities_label'    => 'Categories',

                'index_title'       => 'Manage categories',
                'panel_title'       => 'Categories',
                'list_title'        => 'Categories list',
                'add_button'        => 'Add category',
                'create_title'      => 'Create category',
                'sub_add_button'    => 'Add subcategory',
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
                'empty_text'        => 'No categories found',
            ]
        ];
    }


    /**
     * Check if first level is sortable
     */
    public function isFirstLevelSortable() : bool
    {
        return $this->get('is_first_level_sortable') === true;
    }


    /**
     * Check if category type is editable
     */
    public function isEditable() : bool
    {
        return $this->get('is_editable') === true;
    }


    /**
     * Check if category has enabled the image field
     */
    public function isImage() : bool
    {
        return $this->get('is_image') === true;
    }


    /**
     * Check if category has enabled the description field
     */
    public function isDescription() : bool
    {
        return $this->get('is_description') === true;
    }


    /**
     * Check if category type has allowed the DISABLE option
     */
    public function isDisableAllowed() : bool
    {
        return $this->get('is_disable_allowed') === true;
    }


    /**
     * Check if category type has allowed the DELETE option
     */
    public function isDeleteAllowed() : bool
    {
        return $this->get('is_delete_allowed') === true;
    }


    /**
     * Return max depth level
     */
    public function getMaxLevels() : int
    {
        return $this->get('max_levels');
    }
}
