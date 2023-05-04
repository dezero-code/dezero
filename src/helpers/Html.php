<?php
/**
 * Html class helper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */


namespace dezero\helpers;

use dezero\helpers\ArrayHelper;
use Yii;

/**
 * Helper for generating HTML tags
 */
class Html extends \yii\helpers\Html
{
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
}
