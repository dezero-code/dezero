<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2025 Fabián Ruiz
 */

namespace dezero\mail;

use dezero\base\File;
use dezero\helpers\Json;
use dezero\helpers\Log;
use Yii;
use yii\symfonymailer\Mailer as SymfonyMailer;
use yii\symfonymailer\Message;

/**
 * Extended Mailer component with enhanced logging and file storage
 */
class Mailer extends SymfonyMailer
{
    /**
     * @var string The default view extension to use
     */
    public $defaultViewExtension = 'tpl.php';


    /**
     * @var string Default sender email address
     */
    public $fromEmail;


    /**
     * @var string Default sender name
     */
    public $fromName;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Initialize default from values from component parameters or fallback to env vars
        $this->fromEmail = $this->fromEmail ?: getenv('SMTP_USER');
        $this->fromName = $this->fromName ?: getenv('SMTP_FROM_NAME');

        // Set default sender from environment variables
        $this->messageConfig['from'] = [
            $this->fromEmail => $this->fromName
        ];

        // Add logging and file storage handling
        $this->on(self::EVENT_AFTER_SEND, [$this, 'handleAfterSend']);
    }


    /**
     * {@inheritdoc}
     */
    public function compose($view = null, array $params = [])
    {
        if ( $view === null )
        {
            return parent::compose($view, $params);
        }

        // Add extension .tpl.php by default if not already $view does not end with .tpl.php
        if ( is_string($view) && !empty($this->defaultViewExtension) && ! str_ends_with($view, $this->defaultViewExtension) )
        {
            $view .= '.' . $this->defaultViewExtension;

            return parent::compose($view, $params);
        }

        // Check now for an array option
        if ( is_array($view) )
        {
            $view = array_map(function($v) {
                return str_ends_with($v, $this->defaultViewExtension) ? $v : $v . '.' . $this->defaultViewExtension;
            }, $view);
        }

        return parent::compose($view, $params);
    }


    /**
     * Handle after send event to log and save email files
     *
     * @param \yii\symfonymailer\events\AfterSendEvent $event
     */
    public function handleAfterSend($event)
    {
        $message = $event->message;
        $is_success = $event->isSuccessful;
        $to = implode(', ', array_keys($message->getTo()));

        // Base log information
        $vec_data = [
            'to'        => $to,
            'from'      => key($message->getFrom()) . ' (' . current($message->getFrom()) . ')',
            'subject'   => $message->getSubject(),
        ];

        // Log based on result
        if ( $is_success )
        {
            Log::mail('Email sent: ' . Json::encode($vec_data));
        }
        else
        {
            $vec_data['error'] = $event->error ?? 'Unknown error';
            Log::mail_error('Email failed: ' . Json::encode($vec_data));
        }

        // Always save .eml file
        $is_save_eml_file = $this->saveEmlFile($message);
    }


    /**
     * Save email as .eml file
     */
    protected function saveEmlFile(Message $message) : bool
    {
        $eml_path = Yii::getAlias('@storage/logs/mail/');

        // Ensure directory exists
        $eml_directory = File::ensureDirectory($eml_path);
        if ( ! $eml_directory )
        {
            Log::mail_warning('Failed to create directory: ' . $eml_path);

            return false;
        }

        // Save email content into an .eml file
        $eml_file_name = time() . '-' . substr(md5(rand()), 0, 6) . '.eml';
        $eml_file = File::load($eml_path . $eml_file_name);
        if ( ! $eml_file->write($message->toString()) )
        {
            Log::mail_warning('Failed to write email file: ' . $eml_path . $eml_file_name);

            return false;
        }

        return true;
    }
}
