<?php
/**
 * ImportBc (Batch) model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\sync\models;

use dezero\helpers\Url;
use dezero\modules\sync\models\Batch;
use Yii;

/**
 * Import model class for table "batch" (batch_type = 'import')
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $batch_id
 * @property string $batch_type
 * @property string $name
 * @property string $description
 * @property int $total_items
 * @property int $total_errors
 * @property int $total_warnings
 * @property string $summary_json
 * @property string $results_json
 * @property int $total_operations
 * @property int $last_operation
 * @property int $item_starting_num
 * @property int $item_ending_num
 * @property int $file_id
 * @property string $entity_uuid
 * @property string $entity_type
 * @property int $entity_source_id
 * @property int $created_date
 * @property int $created_user_id
 * @property int $updated_date
 * @property int $updated_user_id
 *
 * -------------------------------------------------------------------------
 * RELATIONS
 * -------------------------------------------------------------------------
 * @property User $createdUser
 * @property Entity $entityUu
 * @property AssetFile $file
 * @property User $updatedUser
 */
class ImportBatch extends Batch
{
    public const IMPORT_TYPE = 'import';


    /**
     * {@inheritdoc}
     */
    public function init() : void
    {
        parent::init();

        $this->batch_type = self::IMPORT_TYPE;
    }


    /**
     * {@inheritdoc}
     */
    public function getSummary() : array
    {
        $vec_summary = parent::getSummary();

        if ( !isset($vec_summary['imported']) )
        {
            $vec_summary['imported'] = 0;
            if ( isset($vec_summary['inserted']) )
            {
                $vec_summary['imported'] += $vec_summary['inserted'];
            }
            if ( isset($vec_summary['updated']) )
            {
                $vec_summary['imported'] += $vec_summary['updated'];
            }
        }

        return $vec_summary;
    }
}
