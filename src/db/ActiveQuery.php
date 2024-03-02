<?php
/**
 * ActiveQuery class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\db;

use dezero\errors\QueryAbortedException;
use Yii;

/**
 * ActiveQuery represents a DB query associated with an Active Record class.
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * Filter the query by "entity_uuid" attribute value
     */
    public function uuid(string $uuid) : self
    {
        $this->andWhere(['entity_uuid' => $uuid]);

        return $this;
    }


    /**
     * Filter the query by enabled elements
     */
    public function enabled() : self
    {
        // $this->andWhere(['is_disabled' => 0]);
        $this->andWhere('disabled_date IS NULL');

        return $this;
    }


    /**
     * Filter the query by disabled elements
     */
    public function disabled() : self
    {
        // $this->andWhere(['is_disabled' => 1]);
        $this->andWhere('disabled_date IS NOT NULL');

        return $this;
    }


   /*
    |--------------------------------------------------------------------------
    | METHODS FROM \dezero\db\Query
    |--------------------------------------------------------------------------
    */

    /**
     * @inheritdoc
     * @return mixed|null first row of the query result array, or `null` if there are no query results.
     */
    public function one($db = null)
    {
        $limit = $this->limit;
        $this->limit = 1;

        try
        {
            $result = parent::one($db);
            if ( $result === false )
            {
                $result = null;
            }
        }
        catch ( QueryAbortedException $e )
        {
            $result = null;
        }

        $this->limit = $limit;

        return $result;
    }


    /**
     * @inheritdoc
     */
    public function all($db = null)
    {
        try
        {
            return parent::all($db);
        }
        catch (QueryAbortedException $e)
        {
            return [];
        }
    }


    /**
     * @inheritdoc
     */
    public function scalar($db = null)
    {
        $limit = $this->limit;
        $this->limit = 1;

        try
        {
            $result = parent::scalar($db);
        }
        catch (QueryAbortedException $e)
        {
            $result = false;
        }

        $this->limit = $limit;

        return $result;
    }


    /**
     * @inheritdoc
     */
    public function column($db = null)
    {
        try
        {
            return parent::column($db);
        }
        catch (QueryAbortedException $e)
        {
            return [];
        }
    }

    /**
     * @inheritdoc
     */
    public function exists($db = null)
    {
        try
        {
            return parent::exists($db);
        }
        catch (QueryAbortedException $e)
        {
            return false;
        }
    }


    /**
     * Shortcut for `createCommand()->getRawSql()`.
     *
     * @param YiiConnection|null $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return string
     * @see createCommand()
     * @see \yii\db\Command::getRawSql()
     */
    public function getRawSql(YiiConnection $db = null) : string
    {
        return $this->createCommand($db)->getRawSql();
    }


    /**
     * @inheritdoc
     */
    public function where($condition, $params = [])
    {
        if ( ! $condition )
        {
            $condition = null;
        }

        return parent::where($condition, $params);
    }


    /**
     * @inheritdoc
     */
    public function andWhere($condition, $params = [])
    {
        if ( ! $condition)
        {
            return $this;
        }

        return parent::andWhere($condition, $params);
    }


    /**
     * @inheritdoc
     */
    public function orWhere($condition, $params = [])
    {
        if ( ! $condition)
        {
            return $this;
        }

        return parent::orWhere($condition, $params);
    }
}
