<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\helpers\Str;
use Yii;
use yii\db\Query;

/**
 * DbSession extends [[Session]] by using database as session data storage.
 */
class DbSession extends \yii\web\DbSession
{
    /**
     * Initializes the DbSession component.
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Custom event triggered after authenticate process has been made succesfully
     */
    public function afterAuthenticate(int $user_id) : bool
    {
        $this->db->createCommand()
            ->update(
                $this->sessionTable,
                [
                    'user_id'       => $user_id,
                    'created_date'  => time(),
                    'uuid'          => Str::UUID()
                ],
                'session_id = :id', [':id' => session_id()]
        );

        return true;
    }


    /**
     * {@inheritdoc}
     *
     * Basically, copy parent method and replace "id" with "session_id"
     */
    protected function composeFields($id = null, $data = null)
    {
        $fields = $this->writeCallback ? call_user_func($this->writeCallback, $this) : [];
        if ( $id !== null )
        {
            $fields['session_id'] = $id;
        }
        if ( $data !== null )
        {
            $fields['data'] = $data;
        }

        return $fields;
    }


    /**
     * {@inheritdoc}
     *
     * Basically, copy parent method and replace "id" with "session_id"
     */
    public function regenerateID($deleteOldSession = false) : void
    {
        $old_id = session_id();

        // if no session is started, there is nothing to regenerate
        if ( empty($old_id) )
        {
            return;
        }

        // parent::regenerateID(false);
         if ( $this->getIsActive() )
         {
            // add @ to inhibit possible warning due to race condition
            // https://github.com/yiisoft/yii2/pull/1812
            if ( YII_DEBUG && !headers_sent() )
            {
                session_regenerate_id($deleteOldSession);
            }
            else
            {
                @session_regenerate_id($deleteOldSession);
            }
        }

        $new_id = session_id();

        // if session id regeneration failed, no need to create/update it.
        if ( empty($new_id) )
        {
            Yii::warning('Failed to generate new session ID', __METHOD__);
            return;
        }

        $row = $this->db->useMaster(function() use ($old_id) {
            return (new Query())->from($this->sessionTable)
               ->where(['session_id' => $old_id])
               ->createCommand($this->db)
               ->queryOne();
        });

        if ( $row !== false && $this->getIsActive() )
        {
            if ( $deleteOldSession )
            {
                $this->db->createCommand()
                    ->update($this->sessionTable, ['session_id' => $new_id], ['session_id' => $old_id])
                    ->execute();
            }
            else
            {
                $row['session_id'] = $new_id;
                $row['created_date'] = time();
                $row['entity_uuid'] = Str::UUID();
                $this->db->createCommand()
                    ->insert($this->sessionTable, $row)
                    ->execute();
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function writeSession($id, $data) : bool
    {
        // Ignore write when forceRegenerate is active for this id
        if ( $this->getUseStrictMode() && $id === $this->_forceRegenerateId )
        {
            return true;
        }

        // Exception must be caught in session write handler
        // https://www.php.net/manual/en/function.session-set-save-handler.php#refsect1-function.session-set-save-handler-notes
        try
        {
            // Ensure backwards compatability (fixed #9438)
            if ( $this->writeCallback && ! $this->fields )
            {
                $this->fields = $this->composeFields();
            }

            // Ensure data consistency
            if ( ! isset($this->fields['data']) )
            {
                $this->fields['data'] = $data;
            }
            else
            {
                $_SESSION = $this->fields['data'];
            }

            // Ensure 'id' and 'expire' are never affected by [[writeCallback]]
            $vec_default_fields = [
                'session_id'    => $id,
                'expires_date'  => time() + $this->getTimeout(),
            ];

            // 31/03/2023 - Check if session exists. If not, "created_date" and "enitity_uuid" must be required
            $row = $this->db->useMaster(function() use ($id) {
                return (new Query())->from($this->sessionTable)
                   ->where(['session_id' => $id])
                   ->createCommand($this->db)
                   ->queryOne();
            });
            if ( $row !== false )
            {
                $vec_default_fields['created_date'] = $row['created_date'];
                $vec_default_fields['entity_uuid'] = $row['entity_uuid'];
            }
            else
            {
                $vec_default_fields['created_date'] = time();
                $vec_default_fields['entity_uuid'] = Str::UUID();
            }

            $this->fields = array_merge($this->fields, $vec_default_fields);
            $this->fields = $this->typecastFields($this->fields);
            $this->db->createCommand()->upsert($this->sessionTable, $this->fields)->execute();
            $this->fields = [];
        }
        catch (\Exception $e)
        {
            Yii::$app->errorHandler->handleException($e);

            return false;
        }

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function destroySession($id) : bool
    {
        $this->db->createCommand()
            ->delete($this->sessionTable, ['session_id' => $id])
            ->execute();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function gcSession($maxLifetime) : bool
    {
        $this->db->createCommand()
            ->delete($this->sessionTable, '[[expires_date]]<:expire', [':expire' => time()])
            ->execute();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    protected function getReadQuery($id) : Query
    {
        return (new Query())
            ->from($this->sessionTable)
            ->where('[[expires_date]]>:expire AND [[session_id]]=:id', [':expire' => time(), ':id' => $id]);
    }
}
