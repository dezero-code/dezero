<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */


namespace dezero\helpers;

use Dz;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Url;
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
    public static function a($text, $url = null, $options = [])
    {
        if ( $url !== null )
        {
            // Allow hash key "#"
            $options['href'] = ($url === '#' || $url === ['#']) ? '#' : Url::to($url);
        }

        return parent::tag('a', $text, $options);
    }


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
     * {@inheritdoc}
     */
    public static function checkboxList($name, $selection = null, $items = [], $options = [])
    {
        if ( ! isset($options['item']) )
        {
            $itemOptions = ArrayHelper::remove($options, 'itemOptions', []);
            $encode = ArrayHelper::getValue($options, 'encode', true);
            $itemCount = count($items) - 1;

            $options['item'] = function ($index, $label, $name, $checked, $value) use (
                $itemOptions,
                $encode,
                $itemCount
            ) {
                $options = array_merge([
                    // 'class' => ['checkbox-custom', 'checkbox-primary'],
                    'label' => $encode ? static::encode($label) : $label,
                    // 'labelOptions' => ['class' => 'form-check-label'],
                    'value' => $value
                ], $itemOptions);

                // Custom "id" attribute for this radio item. For example, "user-is_force_change_password-1"
                if ( !isset($options['id']) )
                {
                    $options['id'] = Html::getInputIdByName($name) .'-'. Html::getInputIdByName($value);
                }

                $wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['checkbox-custom', 'checkbox-primary']]);

                $html =
                    Html::beginTag('div', $wrapperOptions) . "\n" .
                    Html::checkboxInline($name, $checked, $options) . "\n" .
                    Html::endTag('div') . "\n";

                return $html;
            };
        }

        return parent::checkboxList($name, $selection, $items, $options);
    }


    /**
     * {@inheritdoc}
     */
    public static function dropDownList($name, $selection = null, $items = [], $options = [])
    {
        if ( !empty($items) && isset($options['data-plugin']) && $options['data-plugin'] === 'select2' )
        {
            $items = ArrayHelper::merge(['' => ''], $items);
        }

        return parent::dropdownList($name, $selection, $items, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOM METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Generate custom checkbox inline template
     *
     *  ```
     *    <input type="checkbox" id="user-roles-admin" name="User[roles][]" value="admin" checked>
     *    <label for="user-roles-admin">No</label>
     *  ```
     */
    public static function checkboxInline($name, $checked = false, $options = [])
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

            $content = static::input('checkbox', $name, $value, $options);
            $content .= static::label($label, $options['id'], $labelOptions);

            return $content;
        }

        return static::input('checkbox', $name, $value, $options);
    }


    /**
     * Generate custom radio inline template
     *
     *  ```
     *    <input type="radio" id="is_force_change_password-0" name="User[is_force_change_password]" value="0" checked>
     *    <label for="is_force_change_password-0">No</label>
     *  ```
     */
    public static function radioInline($name, $checked = false, $options = [])
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


    /**
     * Generates a breadcrumb navigation menu
     */
    public static function breadcrumbs(array $vec_links, array $options = [])
    {
        if ( !isset($options['class']) )
        {
            $options['class'] = 'breadcrumb';
        }
        else
        {
            $options['class'] .= ' breadcrumb';
        }

        return self::generateMenuList($vec_links, $options, false, 'ol');
    }


    /**
     * Generate a menu with an un-ordered list of items
     */
    public static function generateMenuList(array $vec_links, array $options = [], bool $show_active_class = false, string $menu_tag = 'ul') : string
    {
        if ( empty($vec_links) )
        {
            return '';
        }

        // Check visible
        foreach ( $vec_links as $num_link => $que_link )
        {
            if ( isset($que_link['visible']) && eval('return '. $que_link['visible']. ';') === false )
            {
                unset($vec_links['num_link']);
            }
        }

        if ( empty($vec_links) )
        {
            return '';
        }

        $output = self::listing($menu_tag, $vec_links, $options, true);

        if ( $show_active_class )
        {
            // Get URL from module, controller and action
            $url = '';
            $current_module = Dz::currentModule(true);
            if ( $current_module !== null )
            {
                $url = $current_module;
            }
            $url .= '/'. Dz::currentController(true);
            // $url .= Dz::currentAction(true);

            $output = str_replace($url .'/"', $url .'/" class="active"', $output);
        }

        return $output;
    }


    /**
     * Generates a Bootstrap dropdown menu widget
     */
    public static function dropdownMenu(array $vec_items, array $options = []) : string
    {
        if ( empty($vec_items) )
        {
            return '';

        }

        $output = '';

        // Dropdown menu item
        $output = self::beginTag('div', $options );
        foreach ( $vec_items as $menu_item )
        {
            if ( is_array($menu_item) )
            {
                // Check access
                if ( isset($menu_item['visible']) && eval('return '. $menu_item['visible']. ';') === false )
                {
                    continue;
                }

                // HTML Options
                $item_options = isset($menu_item['options']) ? $menu_item['options'] : [];

                // Icon button
                if ( isset($menu_item['icon']) )
                {
                    if ( ! preg_match("/^wb\-|^fa\-/", $menu_item['icon']) )
                    {
                        $menu_item['icon'] = 'wb-'. $menu_item['icon'];
                    }
                    $menu_item['label'] = '<i class="icon '. $menu_item['icon'] .'"></i> '. $menu_item['label'];
                }

                // Class "dz-bootbox-confirm" needed
                if ( !isset($item_options['class']) )
                {
                    $item_options['class'] = 'dropdown-item';
                }
                else
                {
                    $item_options['class'] .= ' dropdown-item';
                }

                // Confirm box??
                if ( isset($menu_item['confirm']) )
                {
                    $item_options['data-confirm'] = $menu_item['confirm'];
                    // $item_options['class'] .= ' dz-bootbox-confirm';
                }

                // Sub-navigation?
                if ( isset($menu_item['items']) && !empty($menu_item['items']) )
                {
                    if ( empty($menu_item['url']) )
                    {
                        $menu_item['url'] = '#';
                    }

                    $submenu = self::dropdownMenu($menu_item['items'], ['class' => 'dropdown-menu']);
                    // if ( !empty($submenu) )
                    // {
                    //     $output .=  '<li class="dropdown-submenu pull-left">'. self::link($menu_item['label'], $menu_item['url'], $item_options) . $submenu .'</li>';
                    // }
                }

                // Single item
                else
                {
                    if ( isset($menu_item['url']) )
                    {
                        $output .= self::a($menu_item['label'], $menu_item['url'], $item_options);
                    }
                    else
                    {
                        $output .= $menu_item['label'];
                    }
                }
            }

            // Separator
            else if ( $menu_item == '---' )
            {
                $output .= '<div class="divider"></div>';
            }
        }

        // Check if there's one child '<li>' at least in menu
        if ( $output == self::beginTag('div', $options ) )
        {
            return '';
        }

        return $output .'</div>';
    }


    /**
     * Generate an ordered or un-ordered list
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
     * Render a button for a GridView
     *
     * @see Html::a
     */
    public static function gridButton($title, $url = null, $options = []) : string
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
            $options['class'] = isset($options['class']) ? $options['class'] . ' ' : '';
            $options['class'] .= 'btn btn-sm btn-icon btn-pure btn-default';
        }

        // Replace "data-confirm" to "data-dialog"
        // if ( isset($options['data-confirm']) )
        // {
        //     $options['data-dialog'] = $options['data-confirm'];
        //     unset($options['data-confirm']);
        // }

        return Html::a($text, $url, $options);
    }
}
