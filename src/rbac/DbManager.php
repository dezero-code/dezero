<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rbac;

use dezero\helpers\StringHelper;
use dezero\rbac\Item;
use yii\db\Expression;
use yii\db\Query;

/**
 * AuthManager represents an authorization manager that stores authorization information in database.
 */
class DbManager extends \yii\rbac\DbManager
{
    /**
     * {@inheritdoc}
     */
    protected function addItem($item)
    {
        $now = time();
        if ( $item->created_date === null )
        {
            $item->created_date = $now;
        }
        if ( $item->updated_date === null )
        {
            $item->updated_date = $now;
        }

        $this->db->createCommand()
            ->insert($this->itemTable, [
                'name'          => $item->name,
                'type'          => $item->type,
                'item_type'     => $this->getItemType($item->type, $item->name),
                'description'   => $item->description,
                'rule_name'     => $item->ruleName,
                'data'          => $item->data === null ? null : serialize($item->data),
                'created_date'  => $item->created_date,
                'updated_date'  => $item->updated_date,
                'entity_uuid'   => StringHelper::UUID()
            ])->execute();

        $this->invalidateCache();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    protected function updateItem($name, $item)
    {
        if ( $item->name !== $name && !$this->supportsCascadeUpdate() )
        {
            $this->db->createCommand()
                ->update($this->itemChildTable, ['parent' => $item->name], ['parent' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->itemChildTable, ['child' => $item->name], ['child' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->assignmentTable, ['item_name' => $item->name], ['item_name' => $name])
                ->execute();
        }

        $item->updated_date = time();

        $this->db->createCommand()
            ->update($this->itemTable, [
                'name'          => $item->name,
                'description'   => $item->description,
                'item_type'     => $this->getItemType($item->type, $item->name),
                'rule_name'     => $item->ruleName,
                'data'          => $item->data === null ? null : serialize($item->data),
                'updated_date'  => $item->updated_date,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    protected function addRule($rule)
    {
        $now = time();
        if ( $rule->created_date === null )
        {
            $rule->created_date = $time;
        }
        if ( $rule->updated_date === null )
        {
            $rule->updated_date = $time;
        }

        $this->db->createCommand()
            ->insert($this->ruleTable, [
                'name'          => $rule->name,
                'data'          => serialize($rule),
                'created_date'  => $rule->created_date,
                'updated_date'  => $rule->updated_date,
            ])->execute();

        $this->invalidateCache();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    protected function updateRule($name, $rule)
    {
        if ( $rule->name !== $name && !$this->supportsCascadeUpdate() )
        {
            $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => $rule->name], ['rule_name' => $name])
                ->execute();
        }

        $rule->updated_date = time();

        $this->db->createCommand()
            ->update($this->ruleTable, [
                'name'          => $rule->name,
                'data'          => serialize($rule),
                'updated_date'  => $rule->updated_date,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::className() : Role::className();

        if ( !isset($row['data']) || ($data = @unserialize(is_resource($row['data']) ? stream_get_contents($row['data']) : $row['data'])) === false )
        {
            $data = null;
        }

        return new $class([
            'name'          => $row['name'],
            'type'          => $row['type'],
            'item_type'     => $row['item_type'],
            'description'   => $row['description'],
            'rule_name'     => $row['rule_name'] ?: null,
            'data'          => $data,
            'created_date'  => $row['created_date'],
            'updated_date'  => $row['updated_date'],
            'entity_uuid'   => $row['entity_uuid'],
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getChildren($name)
    {
        $query = (new Query())
            ->select(['name', 'type', 'item_type', 'description', 'rule_name', 'data', 'created_date', 'updated_date', 'entity_uuid'])
            ->from([$this->itemTable, $this->itemChildTable])
            ->where(['parent' => $name, 'name' => new Expression('[[child]]')]);

        $vec_children = [];
        foreach ( $query->all($this->db) as $row )
        {
            $vec_children[$row['name']] = $this->populateItem($row);
        }

        return $vec_children;
    }


    /**
     * {@inheritdoc}
     */
    public function assign($role, $user_id)
    {
        $assignment = new Assignment([
            'user_id'       => $user_id,
            'role_name'     => $role->name,
            'created_date'  => time(),
        ]);

        $this->db->createCommand()
            ->insert($this->assignmentTable, [
                'user_id'       => $assignment->user_id,
                'item_name'     => $assignment->role_name,
                'created_date'  => $assignment->created_date,
            ])->execute();

        unset($this->checkAccessAssignments[(string) $user_id]);
        return $assignment;
    }


    /**
     * {@inheritdoc}
     */
    public function getAssignments($user_id)
    {
        if ( $this->isEmptyUserId($user_id) )
        {
            return [];
        }

        $query = (new Query())
            ->from($this->assignmentTable)
            ->where(['user_id' => (string) $user_id]);

        $vec_assignments = [];
        foreach ( $query->all($this->db) as $row )
        {
            $vec_assignments[$row['item_name']] = new Assignment([
                'user_id'       => $row['user_id'],
                'role_name'     => $row['item_name'],
                'created_date'  => $row['created_at'],
            ]);
        }

        return $vec_assignments;
    }


    /**
     * Get "status_type" labels
     */
    public function getItemType($type, $name) : array
    {
        // Role (1)
        if ( $type === Item::TYPE_ROLE )
        {
            return Item::ITEM_TYPE_ROLE;
        }

        // Permission (2)
        if ( $type === Item::TYPE_PERMISSION )
        {
            if ( preg_match("/\//", $name) )
            {
                return Item::ITEM_TYPE_OPERATION;
            }

            return Item::ITEM_TYPE_PERMISSION;
        }
    }


    /**
     * Get "status_type" specific label
     */
    public function status_type_label(?string $status_type = null) : string
    {
        $status_type = ( $status_type === null ) ? $this->status_type : $status_type;
        $vec_labels = $this->status_type_labels();

        return isset($vec_labels[$status_type]) ? $vec_labels[$status_type] : '';
    }
}
