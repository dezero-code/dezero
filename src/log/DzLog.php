<?php
/**
 * DzLog class file
 *
 * Helper class for logging
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class DzLog
{
    /**
     * Last MODEL errors
     */
    private static $_last_model_errors = [];


    /**
     * Magic method __callStatic to invoke methods like "dev", "error" or "warning"
     */
    public static function __callStatic(string $method, $args)
    {
        // echo __METHOD__ . "\n";
        $args[] = $method;
        return call_user_func_array(__CLASS__ . '::log', $args);
    }



    /**
     * Save a log message
     *
     * @see https://yii2-cookbook-test.readthedocs.io/logging-problems-and-solutions/
     */
    public static function log($log_message, string $log_category = 'dev', bool $is_realtime = true) : bool
    {
        // Enable realtime logging
        if ( $is_realtime )
        {
            Yii::getLogger()->flushInterval = 1;
        }

        // Yii default values
        else
        {
            Yii::getLogger()->flushInterval = 1000;
        }

        // Print an array
        if ( is_array($log_message) )
        {
            $log_message = print_r($log_message, true);
        }

        // Print a ActiveRecord object
        elseif ( is_object($log_message) && method_exists($log_message, 'getAttributes') )
        {
            $log_message = print_r($log_message->getAttributes(), true);
        }

        Yii::info($log_message, $log_category);

        return true;
    }


    /**
     * Register an error after saving the model
     */
    public static function saveModelError($model, bool $is_trace_level = true) : bool
    {
        $log_message = '';

        // Get model information
        $class_name = get_class($model);
        $log_message .= 'ERROR saving ' . $class_name . ' model (';
        // $model_id = ActiveRecord::extractPkValue($model, true);
        $model_id = $model->getPrimaryKey();
        if ( $model_id === null )
        {
            $log_message .= 'isNewRecord';
        }
        elseif ( is_array($model_id) )
        {
            $log_message .= implode(", ", $model_id);
        }
        else
        {
            $log_message .= $model_id;
        }

        $log_message .= ')';

        // Add user information
        if ( Dz::isConsole()  )
        {
            $log_message .= ' - Console command';
        }
        elseif ( Yii::$app->user->id > 0 )
        {
            $log_message .= ' - User ' . Yii::$app->user->id . ' (' . Yii::$app->user->username . ')';
        }
        else
        {
            $log_message .= ' - Anonymous';
        }

        // Write error in the log
        $vec_errors = $model->getErrors();
        $log_message .= ' : ' . print_r($vec_errors, true);

        // Cache error
        self::addModelError($class_name, $vec_errors);

        // Log application code?
        if ( $is_trace_level )
        {
            $vec_traces = [];
            $count = 0;
            $ts = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_pop($ts); // remove the last trace since it would be the entry script, not very useful
            foreach ( $ts as $trace )
            {
                if ( isset($trace['file'], $trace['line']) && strpos($trace['file'], YII2_PATH) !== 0 )
                {
                    unset($trace['object'], $trace['args']);
                    $vec_traces[] = $trace;
                    if ( ++$count >= 3 )
                    {
                        break;
                    }
                }
            }

            if ( ! empty($vec_traces) )
            {
                $log_message .= " --- DEBUG BACKTRACE ---\n" . print_r($vec_traces, true);
            }
        }

        // End line
        $log_message .= "------------------------------------------------------------------------------";

        // Register error message
        Yii::info($log_message, "error");

        return true;
    }


    /**
     * Return last model error
     */
    public static function getModelError(string $class_name, string $output_mode = 'default')
    {
        if ( isset(self::$_last_model_errors[$class_name]) )
        {
            // Return errors using COMPACT mode
            if ( $output_mode === 'compact' )
            {
                return self::compactErrors(self::$_last_model_errors[$class_name]);
            }

            return self::$_last_model_errors[$class_name];
        }

        return false;
    }


    /**
     * Return last model error
     */
    public static function lastModelError(bool $is_return_as_array = false, string $output_mode = 'default')
    {
        if ( ! $is_return_as_array )
        {
            return self::getModelError('_last', $output_mode);
        }

        if ( isset(self::$_last_model_errors['_last_class']) )
        {
            return [
                self::$_last_model_errors['_last_class'] => self::getModelError('_last', $output_mode)
            ];
        }

        return false;
    }


    /**
     * Return last model error
     */
    public static function errorsList()
    {
        $vec_output = [];

        if ( ! empty(self::$_last_model_errors) )
        {
            foreach ( self::$_last_model_errors as $class_name => $vec_model_errors )
            {
                if ( $class_name !== '_last' && $class_name !== '_last_class' && ! preg_match("/\\\\/", $class_name) && ! empty($vec_model_errors) && is_array($vec_model_errors) )
                {
                    foreach ( $vec_model_errors as $error_field => $vec_errors )
                    {
                        $vec_output = ArrayHelper::merge($vec_output, $vec_errors);
                    }
                }
            }
        }

        return $vec_output;
    }


    /**
     * Parse array errors into a string ready to view on Javascript output
     *
     * Parse an structure array from...
     *      [User] => Array
     *         (
     *             [email] => Array
     *                 (
     *                     [0] => Email is not a valid email address.
     *                 )
     *              [name] => Array
     *                  (
     *                      [0] => Name is required.
     *                  )
     *         )
     *
     * ... to this new structure ...
     *
     *      [User_email] => 'Email is not a valid email address.'
     *      [User_name] => 'Name is required'
     *
     */
    public static function compactErrors(array $vec_errors) : array
    {
        $vec_output = [];

        if ( ! empty($vec_errors) )
        {
            if ( is_array($vec_errors) )
            {
                foreach ( $vec_errors as $class_name => $vec_field_errors )
                {
                    if ( is_array($vec_field_errors) )
                    {
                        foreach ( $vec_field_errors as $que_field => $que_error )
                        {
                            $vec_output[$class_name . '_' . $que_field] = $que_error;
                        }
                    }
                    else
                    {
                        $vec_output[$class_name] = $vec_field_errors;
                    }
                }
            }
        }

        return $vec_output;
    }


    /**
     * Set a model error
     */
    private static function addModelError($class_name, $vec_errors = [])
    {
        // Save error with full namespace. Example: dzlab\commerce\models\Customer
        self::$_last_model_errors[$class_name] = $vec_errors;

        // Save error without namespace. Example: Customer
        $base_class_name = StringHelper::basename($class_name);
        self::$_last_model_errors[$base_class_name] = $vec_errors;

        // Save last error
        self::$_last_model_errors['_last'] = $vec_errors;
        self::$_last_model_errors['_last_class'] = $base_class_name;

        return true;
    }
}
