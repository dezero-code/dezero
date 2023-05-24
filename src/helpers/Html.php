<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */


namespace dezero\helpers;

use Dz;
use dezero\helpers\ArrayHelper;
use Yii;

/**
 * Helper for generating HTML tags
 */
class Html extends \yii\helpers\Html
{
    /*
    |--------------------------------------------------------------------------
    | OVERRIDED METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * {@inheritdoc}
     */
    public static function errorSummary($models, $options = [])
    {
        $header = isset($options['header']) ? $options['header'] : '<button type="button" class="close" aria-label="Close" data-dismiss="alert"><span aria-hidden="true">×</span></button>';
        $footer = ArrayHelper::remove($options, 'footer', '');
        $encode = ArrayHelper::remove($options, 'encode', true);
        $showAllErrors = ArrayHelper::remove($options, 'showAllErrors', false);
        unset($options['header']);
        $lines = self::collectErrors($models, $encode, $showAllErrors);

        if ( empty($lines) )
        {
            // still render the placeholder for client-side validation use
            $content = '<ul></ul>';
            $options['style'] = isset($options['style']) ? rtrim($options['style'], ';') . '; display:none' : 'display:none';
        }
        else
        {
            $content = '<ul><li>' . implode("</li>\n<li>", $lines) . '</li></ul>';
        }

        if ( !isset($options['id']) )
        {
            $options['id'] = 'form-messages';
        }

        return Html::tag('div', $header . $content . $footer, $options);
    }

    /**
     * {@inheritdoc}
     */
    private static function collectErrors($models, $encode, $showAllErrors)
    {
        $lines = [];
        if ( !is_array($models) )
        {
            $models = [$models];
        }

        foreach ( $models as $model )
        {
            $lines = array_unique(array_merge($lines, $model->getErrorSummary($showAllErrors)));
        }

        // If there are the same error messages for different attributes, array_unique will leave gaps
        // between sequential keys. Applying array_values to reorder array keys.
        $lines = array_values($lines);

        if ( $encode )
        {
            foreach ( $lines as &$line )
            {
                $line = Html::encode($line);
            }
        }

        return $lines;
    }


    /**
     * Generate custom radio inline template
     *
     *  ```
     *    <input type="radio" id="is_force_change_password-0" name="User[is_force_change_password]" value="0" checked>
     *    <label for="is_force_change_password-0">No</label>
     *  ```
     */
    public function radioInline($name, $checked = false, $options = [])
    {
        // 'checked' option has priority over $checked argument
        if ( !isset($options['checked']) )
        {
            $options['checked'] = (bool) $checked;
        }
        $value = array_key_exists('value', $options) ? $options['value'] : '1';

        if ( isset($options['label']) )
        {
            $label = $options['label'];
            $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
            unset($options['label'], $options['labelOptions']);

            // Do not add "class" attribute. This attribute is added in the wrapper. See ActiveField::radioList()
            if ( isset($labelOptions['class']) )
            {
                unset($labelOptions['class']);
            }

            $content = static::input('radio', $name, $value, $options);
            $content .= static::label($label, $options['id'], $labelOptions);

            return $content;
        }

        return static::input('radio', $name, $value, $options);
    }


   /*
    |--------------------------------------------------------------------------
    | CUSTOM METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Generates a breadcrumb navigation menu
     */
    public static function breadcrumbs(array $vec_links)
    {
        return self::generateMenuList($vec_links, ['class' => 'breadcrumb'], false, 'ol');
    }


    /**
     * Generate a menu with an un-ordered list of items.
     */
    public static function generateMenuList(array $vec_links, array $options = [], bool $show_active_class = false, string $menu_tag = 'ul') : string
    {
        // Check visible
        if ( !empty($vec_links) )
        {
            foreach ( $vec_links as $num_link => $que_link )
            {
                if ( isset($que_link['visible']) && eval('return '. $que_link['visible']. ';') === false )
                {
                    unset($vec_links['num_link']);
                }
            }
        }

        $output = '';
        if ( !empty($vec_links) )
        {
            $output = self::listing($menu_tag, $vec_links, $options, true);

            if ( $show_active_class )
            {
                // Get URL from module, controller and action
                $que_url = '';
                $current_module = Dz::currentModule(true);
                if ( $current_module !== null )
                {
                    $que_url = $current_module;
                }
                $que_url .= '/'. Dz::currentController(true);
                // $que_url .= Dz::currentAction(true);

                $output = str_replace($que_url .'/"', $que_url .'/" class="active"', $output);
            }
        }

        return $output;
    }


    /**
     * Generate an ordered or un-ordered list.
     */
    private static function listing(string $type, array $list, array $options = [], bool $is_menu = false) : string
    {
        $content = '';

        $current_module = Dz::currentModule(true);
        $current_controller =  Dz::currentController(true);
        $current_action =  Dz::currentAction(true);

        if ( count($list) <= 0 )
        {
            return $content;
        }

        foreach ( $list as $key => $value )
        {
            // If the value is an array, we will recurse the function so that we can
            // produce a nested list within the list being built. Of course, nested
            // lists may exist within nested lists, etc.
            if (is_array($value))
            {
                // Check access
                if ( isset($value['visible']) && eval('return '. $que_link['visible']. ';') === true )
                {
                    continue;
                }

                // Menu (check current URL)
                if ( $is_menu )
                {
                    if ( strtolower($value['url'][0]) == '/'. $current_module .'/'. $current_controller .'/'. $current_action || ( $current_action == Yii::$app->controller->defaultAction && (strtolower($value['url'][0]) == '/'. $current_module .'/'. $current_controller) ) )
                    {
                        $content .= '<li class="active">';
                    }
                    else
                    {
                        $content .= '<li>';
                    }
                    $content .= self::a($value['label'], $value['url']) .'</li>';
                }
                else
                {
                    if (is_int($key))
                    {
                        $content .= self::listing($type, $value);
                    }
                    else
                    {
                        $content .= '<li>'. $key . self::listing($type, $value) .'</li>';
                    }
                }
            }
            else
            {
                $content .= '<li>'. $value .'</li>';
            }
        }

        return self::tag($type, $content, $options);
    }


    /**
     * Render a button for GridView
     *
     * @see Html::a
     */
    public static function gridButton($title, $url = null, $options = [])
    {
        $text = $title;
        if ( isset($options['icon']) )
        {
            $icon = $options['icon'];
            $text = "<i class='wb-{$icon}'></i>";
            unset($options['icon']);

            // Tooltip
            $options['data-original-title'] = $title;
            $options['data-toggle'] = 'tooltip';

            // Button classes
            $options['class'] = isset($options['class']) ? ' ' : '';
            $options['class'] .= 'btn btn-sm btn-icon btn-pure btn-default';
        }



        return Html::a($text, $url, $options);
    }
}
