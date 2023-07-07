<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use Dz;
use dezero\helpers\Log;
use Yii;

/**
 * Trait class to implement change status process
 */
trait StatusTrait
{
    /**
     * Change status
     */
    public function changeStatus($new_status, $comments = null, $is_sending_mail = false)
    {
        // Save old status
        $old_status = $this->status_type;

        // Set new status
        $this->status_type = $new_status;

        // Save only if status has changed
        if ( $old_status !== $this->status_type )
        {
            if ( ! $this->save() )
            {
                Log::saveModelError($this);
                return false;
            }

            // Save status history
            // $this->saveStatusHistory($new_status, $comments);

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
        }

        return true;
    }
}



