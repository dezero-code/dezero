<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use Dz;
use dezero\helpers\Log;
use dezero\modules\entity\models\StatusHistory;
use Yii;
use yii\db\ActiveQueryInterface;

/**
 * Trait class to implement change status process
 */
trait StatusTrait
{
    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getStatusHistory() : ActiveQueryInterface
    {
        return $this->hasMany(StatusHistory::class, ['entity_uuid' => 'entity_uuid'])
            ->orderBy(['status_history_id' => SORT_DESC]);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getLastStatusHistory() : ActiveQueryInterface
    {
        return $this->hasOne(StatusHistory::class, ['entity_uuid' => 'entity_uuid'])
            ->orderBy(['status_history_id' => SORT_DESC]);
    }


    /**
     * Change status
     */
    public function changeStatus($new_status, $comments = null, $is_sending_mail = false) : bool
    {
        // Save old status
        $old_status = $this->status_type;

        // Set new status
        $this->status_type = $new_status;

        // Save only if status has changed
        if ( $old_status === $this->status_type )
        {
            return true;
        }

        if ( ! $this->save() )
        {
            Log::saveModelError($this);
            return false;
        }

        // Save status history
        $this->saveStatusHistory($new_status, $comments);

        // Send email notifications
        /*
        if ( $is_sending_mail )
        {
            if ( $this->status_type === 'active' )
            {
                $this->sendEmail('community_user_accepted');
            }
            else if ( $this->status_type === 'rejected' )
            {
                $this->sendEmail('community_user_rejected');
            }
        }
        */

        return true;
    }


    /**
     * Save new status into status history
     */
    public function saveStatusHistory($new_status, $comments = null) : bool
    {
        // Get last StatusHistory model
        $last_status_history_model = StatusHistory::find()
            ->where(['entity_uuid' => $this->entity_uuid])
            ->orderBy(['status_history_id' => SORT_DESC])
            ->one();

        // Do not repeat history for same status
        if ( $last_status_history_model !== null && $last_status_history_model->status_type === $new_status )
        {
            return true;
        }

        // Register new status history
        $status_history_model = Dz::makeObject(StatusHistory::class);
        $status_history_model->setAttributes([
            'entity_uuid'       => $this->entity_uuid,
            'entity_source_id'  => $this->entity ? $this->entity->source_id : null,
            'entity_type'       => $this->getEntityType(),
            'status_type'       => $new_status,
            'comments'          => $comments
        ]);

        if ( ! $status_history_model->save() )
        {
            Log::saveModelError($status_history_model);
            return false;
        }

        return true;
    }
}



