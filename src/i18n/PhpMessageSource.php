<?php
/**
 * PhpMessageSource class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\i18n;

use Yii;

/**
 * PhpMessageSource represents a message source that stores translated messages in PHP scripts.
 */
class PhpMessageSource extends \yii\i18n\PhpMessageSource
{
    /**
     * @var string the base path for all translated messages. Defaults to '@app/translations'.
     */
    public $basePath = '@app/translations';


    /**
     * {@inheritdoc}
     */
    protected function loadMessages($category, $language)
    {
        $messageFile = $this->getMessageFilePath($category, $language);
        $messages = $this->loadMessagesFromFile($messageFile);

        $fallbackLanguage = substr((string)$language, 0, 2);
        $fallbackSourceLanguage = substr($this->sourceLanguage, 0, 2);

        if ( $fallbackLanguage !== '' && $language !== $fallbackLanguage )
        {
            $messages = $this->loadFallbackMessages($category, $fallbackLanguage, $messages, $messageFile);
        }
        elseif ($fallbackSourceLanguage !== '' && $language === $fallbackSourceLanguage)
        {
            $messages = $this->loadFallbackMessages($category, $this->sourceLanguage, $messages, $messageFile);
        }
        elseif ($messages === null)
        {
            return array();
            // Yii::warning("The message file for category '$category' does not exist: $messageFile", __METHOD__);
        }

        return (array) $messages;
    }

}
