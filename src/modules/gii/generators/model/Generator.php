<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace dezero\modules\gii\generators\model;

use dezero\helpers\ArrayHelper;
use dezero\helpers\StringHelper;
use Dz;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
// class Generator extends \yii\gii\generators\model\Generator
class Generator extends \yii\gii\Generator
{
    const RELATIONS_NONE = 'none';
    const RELATIONS_ALL = 'all';
    const RELATIONS_ALL_INVERSE = 'all-inverse';

    public $db = 'db';
    public $ns = 'frontend\models';
    public $tableName;
    public $modelClass;
    public $baseClass = 'dezero\entity\ActiveRecord';
    public $generateRelations = self::RELATIONS_ALL;
    public $generateRelationsFromCurrentSchema = true;
    public $generateLabelsFromComments = false;
    public $useTablePrefix = false;
    public $standardizeCapitals = false;
    public $useSchemaName = true;
    public $moduleName;
    public $messageCategory = 'backend';

    // ActiveQuery
    public $generateQuery = true;
    public $queryNs = 'frontend\models\query';
    public $queryClass;
    public $queryBaseClass = 'dezero\db\ActiveQuery';

    // Search sub class
    public $generateSearch = true;
    public $searchNs = 'frontend\models\search';
    public $searchClass;


    public $modelTitle = [];
    public $relationsOne = [];
    public $relationsMany = [];
    public $relationsNamespaces = [];

    // Columns excluded for REQUIRED rule (TimestampBehavior and BlameableBehavior)
    public $excludedColumns = ['created_date', 'created_user_id', 'updated_date', 'updated_user_id', 'entity_uuid'];


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Dezero model Generator';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generates a ActiveRecord class for the specified database table.';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'ns', 'tableName', 'modelClass', 'moduleName', 'baseClass', 'queryNs', 'queryClass', 'queryBaseClass', 'searchNs', 'searchClass'], 'filter', 'filter' => 'trim'],
            [['ns', 'queryNs', 'searchNs'], 'filter', 'filter' => function ($value) { return trim($value, '\\'); }],

            [['db', 'ns', 'tableName', 'moduleName', 'baseClass', 'queryNs', 'queryBaseClass', 'searchNs'], 'required'],
            [['db', 'moduleName', 'modelClass', 'queryClass', 'searchClass'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['ns', 'baseClass', 'queryNs', 'queryBaseClass', 'searchNs'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['tableName'], 'match', 'pattern' => '/^([\w ]+\.)?([\w\* ]+)$/', 'message' => 'Only word characters, and optionally spaces, an asterisk and/or a dot are allowed.'],
            [['db'], 'validateDb'],
            [['ns', 'queryNs', 'searchNs'], 'validateNamespace'],
            [['tableName'], 'validateTableName'],
            [['modelClass'], 'validateModelClass', 'skipOnEmpty' => false],
            [['baseClass'], 'validateClass', 'params' => ['extends' => ActiveRecord::class]],
            [['queryBaseClass'], 'validateClass', 'params' => ['extends' => ActiveQuery::class]],
            [['generateRelations'], 'in', 'range' => [self::RELATIONS_NONE, self::RELATIONS_ALL, self::RELATIONS_ALL_INVERSE]],
            [['generateLabelsFromComments', 'useTablePrefix', 'useSchemaName', 'generateQuery', 'generateSearch', 'generateRelationsFromCurrentSchema'], 'boolean'],
            [['enableI18N', 'standardizeCapitals'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'ns' => 'Namespace',
            'db' => 'Database Connection ID',
            'tableName' => 'Table Name',
            'moduleName' => 'Module Name',
            'standardizeCapitals' => 'Standardize Capitals',
            'modelClass' => 'Model Class Name',
            'baseClass' => 'Base Class',
            'generateRelations' => 'Generate Relations',
            'generateRelationsFromCurrentSchema' => 'Generate Relations from Current Schema',
            'generateLabelsFromComments' => 'Generate Labels from DB Comments',
            'useSchemaName' => 'Use Schema Name',

            // ActiveQueru
            'generateQuery' => 'Generate ActiveQuery',
            'queryNs' => 'ActiveQuery Namespace',
            'queryClass' => 'ActiveQuery Class',
            'queryBaseClass' => 'ActiveQuery Base Class',

            // Search subclass
            'generateSearch' => 'Generate Search Subclass',
            'searchNs' => 'Search Subclass Namespace',
            'searchClass' => 'Search Class',

            'messageCategory' => 'Message Category',

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'ns' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
            'db' => 'This is the ID of the DB application component.',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>. In this case, multiple ActiveRecord classes
                will be generated, one for each matching table name; and the class names will be generated from
                the matching characters. For example, table <code>tbl_post</code> will generate <code>Post</code>
                class.',
            'moduleName' => 'This is the name of the Module where the models will be stored',
            'modelClass' => 'This is the name of the ActiveRecord class to be generated. The class name should not contain
                the namespace part as it is specified in "Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveRecord classes will be generated.',
            'standardizeCapitals' => 'This indicates whether the generated class names should have standardized capitals. For example,
            table names like <code>SOME_TABLE</code> or <code>Other_Table</code> will have class names <code>SomeTable</code>
            and <code>OtherTable</code>, respectively. If not checked, the same tables will have class names <code>SOMETABLE</code>
            and <code>OtherTable</code> instead.',
            'baseClass' => 'This is the base class of the new ActiveRecord class. It should be a fully qualified namespaced class name.',
            'generateRelations' => 'This indicates whether the generator should generate relations based on
                foreign key constraints it detects in the database. Note that if your database contains too many tables,
                you may want to uncheck this option to accelerate the code generation process.',
            'generateRelationsFromCurrentSchema' => 'This indicates whether the generator should generate relations from current schema or from all available schemas.',
            'generateLabelsFromComments' => 'This indicates whether the generator should generate attribute labels
                by using the comments of the corresponding DB columns.',
            'useTablePrefix' => 'This indicates whether the table name returned by the generated ActiveRecord class
                should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the
                table name is <code>tbl_post</code> and <code>tablePrefix=tbl_</code>, the ActiveRecord class
                will return the table name as <code>{{%post}}</code>.',
            'useSchemaName' => 'This indicates whether to include the schema name in the ActiveRecord class
                when it\'s auto generated. Only non default schema would be used.',
            'generateQuery' => 'This indicates whether to generate ActiveQuery for the ActiveRecord class.',
            'queryNs' => 'This is the namespace of the ActiveQuery class to be generated, e.g., <code>app\models\query</code>',
            'queryClass' => 'This is the name of the ActiveQuery class to be generated. The class name should not contain
                the namespace part as it is specified in "ActiveQuery Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveQuery classes will be generated.',
            'queryBaseClass' => 'This is the base class of the new ActiveQuery class. It should be a fully qualified namespaced class name.',
            'searchNs' => 'This is the namespace of the Search subclass to be generated, e.g., <code>app\models\search</code>',
            'searchClass' => 'This is the name of the Search subclass to be generated.'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function autoCompleteData()
    {
        $db = $this->getDbConnection();
        if ($db !== null) {
            return [
                'tableName' => function () use ($db) {
                    return $db->getSchema()->getTableNames();
                },
            ];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        // @todo make 'query.php' to be required before 2.1 release
        return ['model.php'/*, 'query.php'*/];
    }

    /**
     * {@inheritdoc}
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['ns', 'db', 'baseClass', 'generateRelations', 'generateLabelsFromComments', 'queryNs', 'queryBaseClass', 'useTablePrefix', 'generateQuery', 'generateSearch', 'searchNs']);
    }

    /**
     * Returns the `tablePrefix` property of the DB connection as specified
     *
     * @return string
     * @since 2.0.5
     * @see getDbConnection
     */
    public function getTablePrefix()
    {
        $db = $this->getDbConnection();
        if ($db !== null) {
            return $db->tablePrefix;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $searchClassName = ($this->generateSearch) ? $this->generateSearchClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'searchClassName' => $searchClassName,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],

                // Custom parameters from Dezero Framework
                'ns' => $this->ns,
                'queryNs' => $this->queryNs,
                'enum' => $this->getEnum($tableSchema->columns),
                'searchNs' => $this->searchNs,
                'searchRules' => $this->generateSearchRules($tableSchema),
                'searchFilters' => $this->generateSearchFilters($tableSchema),

                // Model title
                'modelTitle' => $this->modelTitle,

                // Primary key (array)
                'primaryKey' => $tableSchema->primaryKey,

                // Relations
                'relationsOne' => $this->relationsOne,
                'relationsMany' => $this->relationsMany,
                'relationsNamespaces' => $this->relationsNamespaces,
            ];

            // Base model class
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/base/' . $modelClassName . '.php',
                $this->render('base.php', $params)
            );

            // Main model class
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            // Query class
            if ($queryClassName) {
                $params['className'] = $queryClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php',
                    $this->render('query.php', $params)
                );
            }

            // Search subclass
            if ($searchClassName) {
                $params['className'] = $searchClassName;
                $params['modelClassName'] = $modelClassName;
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->searchNs)) . '/' . $searchClassName . '.php',
                    $this->render('search.php', $params)
                );
            }
        }

        return $files;
    }

    /**
     * Generates the properties for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated properties (property => type)
     * @since 2.0.6
     */
    protected function generateProperties($table)
    {
        $properties = [];
        foreach ($table->columns as $column) {
            $columnPhpType = $column->phpType;
            if ($columnPhpType === 'integer') {
                $type = 'int';
            } elseif ($columnPhpType === 'boolean') {
                $type = 'bool';
            } else {
                $type = $columnPhpType;
            }
            $properties[$column->name] = [
                'type' => $type,
                'name' => $column->name,
                'comment' => $column->comment,
            ];

            // 23/03/2023 - Model title. Use first STRING value detected
            if ( empty($this->modelTitle) && $type === 'string' )
            {
                $this->modelTitle = [$column->name];
            }
        }

        // 23/03/2023 - Model title. Use primarey key
        if ( empty($this->modelTitle) && !empty($table->primaryKey) )
        {
            $this->modelTitle = $table->primaryKey;
        }

        return $properties;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $vec_types = [
            'required'  => [],
            'integer'   => [],
            'number'    => [],
            'boolean'   => [],
            'string'    => [],
            'safe'      => [],
            'null'      => [],
        ];
        $vec_lengths = [];

        foreach ( $table->columns as $column )
        {
            if ( $column->autoIncrement )
            {
                continue;
            }

            if ( ! $column->allowNull && $column->defaultValue === null && ! in_array($column->name, $this->excludedColumns) )
            {
                $vec_types['required'][] = $column->name;
            }
            else if ( $column->allowNull )
            {
                $vec_types['null'][] = $column->name;
            }

            switch ( $column->type )
            {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $vec_types['integer'][] = $column->name;
                break;

                case Schema::TYPE_BOOLEAN:
                    $vec_types['boolean'][] = $column->name;
                break;

                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $vec_types['number'][] = $column->name;
                break;

                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                    $vec_types['safe'][] = $column->name;
                break;

                default:
                    // strings
                    if ( $column->size > 0 )
                    {
                        $vec_lengths[$column->size][] = $column->name;
                    }
                    else if ( ! $this->isEnum($column) )
                    {
                        $vec_types['string'][] = $column->name;
                    }
                break;
            }
        }

        $vec_rules = [];

        // Add all rules excepting NULL (it will be the last ones)
        $is_prefix_rule = false;
        foreach ( $vec_types as $type => $vec_columns )
        {
            if ( !empty($vec_columns) )
            {
                if ( $type !== 'null' )
                {
                    $prefix_rule = '';
                    if ( $is_prefix_rule === false )
                    {
                        $is_prefix_rule = true;
                        $prefix_rule = "// Typed rules\n            ";
                    }
                    $vec_rules[] = $prefix_rule ."'{$type}Fields' => [['" . implode("', '", $vec_columns) . "'], '$type']";
                }
            }
        }

        // Add "max length" attributes
        if ( !empty($vec_lengths) )
        {
            $is_prefix_rule = false;
            ksort($vec_lengths);
            foreach ( $vec_lengths as $length => $vec_columns )
            {
                if ( !empty($vec_columns) )
                {
                    $prefix_rule = '';
                    if ( $is_prefix_rule === false )
                    {
                        $is_prefix_rule = true;
                        $prefix_rule = "\n            // Max length rules\n            ";
                    }
                    $vec_rules[] = $prefix_rule ."'max{$length}' => [['" . implode("', '", $vec_columns) . "'], 'string', 'max' => $length]";
                }
            }
        }

        // Add ENUM attributes
        // For enum fields create rules "in range" for all enum values
        $vec_enums = $this->getEnum($table->columns);
        if ( !empty($vec_enums) )
        {
            $is_prefix_rule = false;
            foreach ( $vec_enums as $field_name => $field_details )
            {
                $ea = [];
                foreach ($field_details['values'] as $field_enum_values)
                {
                    $ea[] = 'self::'.$field_enum_values['const_name'];
                }

                $prefix_rule = '';
                if ( $is_prefix_rule === false )
                {
                    $is_prefix_rule = true;
                    $prefix_rule = "\n            // ENUM rules\n            ";
                }
                $vec_rules[] = $prefix_rule ."'". StringHelper::camelCase($field_name) ."List' => ['".$field_name."', 'in', 'range' => [\n                    ".implode(
                        ",\n                    ",
                        $ea
                    ).",\n                ]\n            ]";
            }
        }

        // Default NULL values
        if ( !empty($vec_types['null']) )
        {
            $vec_columns = $vec_types['null'];
            $prefix_rule = "\n            // Default NULL\n            ";
            $vec_rules[] = $prefix_rule ."'defaultNull' => [['" . implode("', '", $vec_columns) . "'], 'default', 'value' => null]";
        }


        // Add UNIQUE rules
        $db = $this->getDbConnection();
        try
        {
            $uniqueIndexes = array_merge($db->getSchema()->findUniqueIndexes($table), [$table->primaryKey]);
            $uniqueIndexes = array_unique($uniqueIndexes, SORT_REGULAR);
            if ( !empty($uniqueIndexes) )
            {
                $is_prefix_rule = false;
                foreach ( $uniqueIndexes as $uniqueColumns )
                {
                    // Avoid validating auto incremental columns
                    if ( ! $this->isColumnAutoIncremental($table, $uniqueColumns) )
                    {
                        $attributesCount = count($uniqueColumns);

                        $prefix_rule = '';
                        if ( $is_prefix_rule === false )
                        {
                            $is_prefix_rule = true;
                            $prefix_rule = "\n            // UNIQUE rules\n            ";
                        }

                        if ( $attributesCount === 1 )
                        {
                            $vec_rules[] = $prefix_rule ."'". StringHelper::camelCase($uniqueColumns[0]) ."Unique' => [['" . $uniqueColumns[0] . "'], 'unique']";
                        }
                        else if ( $attributesCount > 1 )
                        {
                            $columnsList = implode("', '", $uniqueColumns);
                            $vec_rules[] = $prefix_rule ."'". StringHelper::camelCase($uniqueColumns[0]) . StringHelper::camelCase($uniqueColumns[1]) ."Unique' => [['$columnsList'], 'unique', 'targetAttribute' => ['$columnsList']]";
                        }
                    }
                }
            }
        }
        catch (NotSupportedException $e)
        {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        /*
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            unset($refs[0]);
            $attributes = implode("', '", array_keys($refs));
            $targetAttributes = [];
            foreach ($refs as $key => $value) {
                $targetAttributes[] = "'$key' => '$value'";
            }
            $targetAttributes = implode(', ', $targetAttributes);
            $vec_rules[] = "[['$attributes'], 'exist', 'skipOnError' => true, 'targetClass' => $refClassName::class, 'targetAttribute' => [$targetAttributes]]";
        }
        */

        return $vec_rules;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     * @param \yii\db\TableSchema the table being checked
     * @param array $fks obtained from the checkJunctionTable() method
     * @param array $relations
     * @return array modified $relations
     */
    private function generateManyManyRelations($table, $fks, $relations)
    {
        $db = $this->getDbConnection();

        foreach ($fks as $pair) {
            list($firstKey, $secondKey) = $pair;
            $table0 = $firstKey[0];
            $table1 = $secondKey[0];
            unset($firstKey[0], $secondKey[0]);
            $className0 = $this->generateClassName($table0);
            $className1 = $this->generateClassName($table1);
            $table0Schema = $db->getTableSchema($table0);
            $table1Schema = $db->getTableSchema($table1);

            // @see https://github.com/yiisoft/yii2-gii/issues/166
            if ($table0Schema === null || $table1Schema === null) {
                continue;
            }

            $link = $this->generateRelationLink(array_flip($secondKey));
            $viaLink = $this->generateRelationLink($firstKey);
            $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);
            if ( !isset($relations[$table0Schema->fullName][$relationName]) )
            {
                $relations[$table0Schema->fullName][$relationName] = [
                    "return \$this->hasMany($className1::class, $link)->viaTable('"
                    . $this->generateTableName($table->name) . "', $viaLink);",
                    $className1,
                    true,
                ];
            }

            $link = $this->generateRelationLink(array_flip($firstKey));
            $viaLink = $this->generateRelationLink($secondKey);
            $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);
            if ( !isset($relations[$table1Schema->fullName][$relationName]) )
            {
                $relations[$table1Schema->fullName][$relationName] = [
                    "return \$this->hasMany($className0::class, $link)->viaTable('"
                    . $this->generateTableName($table->name) . "', $viaLink);",
                    $className0,
                    true,
                ];
            }
        }

        return $relations;
    }

    /**
     * @return string[] all db schema names or an array with a single empty string
     * @throws NotSupportedException
     * @since 2.0.5
     */
    protected function getSchemaNames()
    {
        $db = $this->getDbConnection();

        if ($this->generateRelationsFromCurrentSchema) {
            if ($db->schema->defaultSchema !== null) {
                return [$db->schema->defaultSchema];
            }
            return [''];
        }

        $schema = $db->getSchema();
        if ($schema->hasMethod('getSchemaNames')) { // keep BC to Yii versions < 2.0.4
            try {
                $schemaNames = $schema->getSchemaNames();
            } catch (NotSupportedException $e) {
                // schema names are not supported by schema
            }
        }
        if (!isset($schemaNames)) {
            if (($pos = strpos($this->tableName, '.')) !== false) {
                $schemaNames = [substr($this->tableName, 0, $pos)];
            } else {
                $schemaNames = [''];
            }
        }
        return $schemaNames;
    }

    /**
     * @return array the generated relation declarations
     */
    protected function generateRelations()
    {
        if ($this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }

        $db = $this->getDbConnection();
        $relations = [];
        $schemaNames = $this->getSchemaNames();
        foreach ($schemaNames as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    if ( ! isset($relations[$table->fullName][$relationName]) )
                    {
                        $relations[$table->fullName][$relationName] = [
                            "return \$this->hasOne($refClassName::class, $link);",
                            $refClassName,
                            false,
                        ];
                    }

                    // 23/03/2023 - Add special relationOne
                    $this->relationsOne[$relationName] = ["$refClassName::class", $refClassName];

                    // 16/05/2024 - Add namespaces
                    $this->generateNamespaces($relationName, $refClassName);

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    if ( ! isset($relations[$refTableSchema->fullName][$relationName]) )
                    {
                        $relations[$refTableSchema->fullName][$relationName] = [
                            "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "($className::class, $link);",
                            $className,
                            $hasMany,
                        ];

                        // 23/03/2023 - Add special relationOne
                        if ( $hasMany )
                        {
                            $this->relationsMany[$relationName] = ["$className::class", $className];
                        }
                        else
                        {
                            $this->relationsOne[$relationName] = ["$className::class", $className];
                        }
                    }
                }

                if (($junctionFks = $this->checkJunctionTable($table)) === false) {
                    continue;
                }

                // $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }

        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }

        if ( !empty($relations) )
        {
            ksort($relations);
        }

        return $relations;
    }

    /**
     * Adds inverse relations
     *
     * @param array $relations relation declarations
     * @return array relation declarations extended with inverse relation names
     * @since 2.0.5
     */
    protected function addInverseRelations($relations)
    {
        $db = $this->getDbConnection();
        $relationNames = [];

        $schemaNames = $this->getSchemaNames();
        foreach ($schemaNames as $schemaName) {
            foreach ($db->schema->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);

                    $leftRelationName = $this->generateRelationName($relationNames, $table, $fks[0], false);
                    $relationNames[$table->fullName][$leftRelationName] = true;
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $rightRelationName = $this->generateRelationName(
                        $relationNames,
                        $refTableSchema,
                        $className,
                        $hasMany
                    );
                    $relationNames[$refTableSchema->fullName][$rightRelationName] = true;

                    $relations[$table->fullName][$leftRelationName][0] =
                        rtrim($relations[$table->fullName][$leftRelationName][0], ';')
                        . "->inverseOf('".lcfirst($rightRelationName)."');";
                    $relations[$refTableSchema->fullName][$rightRelationName][0] =
                        rtrim($relations[$refTableSchema->fullName][$rightRelationName][0], ';')
                        . "->inverseOf('".lcfirst($leftRelationName)."');";
                }
            }
        }
        return $relations;
    }

    /**
     * Determines if relation is of has many type
     *
     * @param TableSchema $table
     * @param array $fks
     * @return bool
     * @since 2.0.5
     */
    protected function isHasManyRelation($table, $fks)
    {
        $uniqueKeys = [$table->primaryKey];
        try {
            $uniqueKeys = array_merge($uniqueKeys, $this->getDbConnection()->getSchema()->findUniqueIndexes($table));
        } catch (NotSupportedException $e) {
            // ignore
        }
        foreach ($uniqueKeys as $uniqueKey) {
            if (count(array_diff(array_merge($uniqueKey, $fks), array_intersect($uniqueKey, $fks))) === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generates the link parameter to be used in generating the relation declaration.
     * @param array $refs reference constraint
     * @return string the generated link parameter.
     */
    protected function generateRelationLink($refs)
    {
        $pairs = [];
        foreach ($refs as $a => $b) {
            $pairs[] = "'$a' => '$b'";
        }

        return '[' . implode(', ', $pairs) . ']';
    }

    /**
     * Checks if the given table is a junction table, that is it has at least one pair of unique foreign keys.
     * @param \yii\db\TableSchema the table being checked
     * @return array|bool all unique foreign key pairs if the table is a junction table,
     * or false if the table is not a junction table.
     */
    protected function checkJunctionTable($table)
    {
        if (count($table->foreignKeys) < 2) {
            return false;
        }
        $uniqueKeys = [$table->primaryKey];
        try {
            $uniqueKeys = array_merge($uniqueKeys, $this->getDbConnection()->getSchema()->findUniqueIndexes($table));
        } catch (NotSupportedException $e) {
            // ignore
        }
        $result = [];
        // find all foreign key pairs that have all columns in an unique constraint
        $foreignKeys = array_values($table->foreignKeys);
        $foreignKeysCount = count($foreignKeys);

        for ($i = 0; $i < $foreignKeysCount; $i++) {
            $firstColumns = $foreignKeys[$i];
            unset($firstColumns[0]);

            for ($j = $i + 1; $j < $foreignKeysCount; $j++) {
                $secondColumns = $foreignKeys[$j];
                unset($secondColumns[0]);

                $fks = array_merge(array_keys($firstColumns), array_keys($secondColumns));
                foreach ($uniqueKeys as $uniqueKey) {
                    if (count(array_diff(array_merge($uniqueKey, $fks), array_intersect($uniqueKey, $fks))) === 0) {
                        // save the foreign key pair
                        $result[] = [$foreignKeys[$i], $foreignKeys[$j]];
                        break;
                    }
                }
            }
        }
        return empty($result) ? false : $result;
    }

    /**
     * Generate a relation name for the specified table and a base name.
     * @param array $relations the relations being generated currently.
     * @param \yii\db\TableSchema $table the table schema
     * @param string $key a base name that the relation name may be generated from
     * @param bool $multiple whether this is a has-many relation
     * @return string the relation name
     */
    protected function generateRelationName($relations, $table, $key, $multiple)
    {
        static $baseModel;
        /* @var $baseModel \yii\db\ActiveRecord */
        if ($baseModel === null) {
            $baseClass = $this->baseClass;
            $baseClassReflector = new \ReflectionClass($baseClass);
            if ($baseClassReflector->isAbstract()) {
                $baseClassWrapper =
                    'namespace ' . __NAMESPACE__ . ';'.
                    'class GiiBaseClassWrapper extends \\' . $baseClass . ' {' .
                        'public static function tableName(){' .
                            'return "' . addslashes($table->fullName) . '";' .
                        '}' .
                    '};' .
                    'return new GiiBaseClassWrapper();';
                $baseModel = eval($baseClassWrapper);
            } else {
                $baseModel = new $baseClass();
            }
            $baseModel->setAttributes([]);
        }

        if (!empty($key) && strcasecmp($key, 'id')) {
            if (substr_compare($key, 'id', -2, 2, true) === 0) {
                $key = rtrim(substr($key, 0, -2), '_');
            } elseif (substr_compare($key, 'id', 0, 2, true) === 0) {
                $key = ltrim(substr($key, 2, strlen($key)), '_');
            }
        }
        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        /*
        $i = 0;
        while ($baseModel->hasProperty(lcfirst($name))) {
            $name = $rawName . ($i++);
        }
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName . ($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName . ($i++);
        }
        */

        return $name;
    }

    /**
     * Validates the [[db]] attribute.
     */
    public function validateDb()
    {
        if (!Yii::$app->has($this->db)) {
            $this->addError('db', 'There is no application component named "db".');
        } elseif (!Yii::$app->get($this->db) instanceof Connection) {
            $this->addError('db', 'The "db" application component must be a DB connection instance.');
        }
    }

    /**
     * Validates the namespace.
     *
     * @param string $attribute Namespace variable.
     */
    public function validateNamespace($attribute)
    {
        $value = $this->$attribute;
        $value = ltrim($value, '\\');
        $path = Yii::getAlias('@' . str_replace('\\', '/', $value), false);
        if ($path === false) {
            $this->addError($attribute, 'Namespace must be associated with an existing directory.');
        }
    }

    /**
     * Validates the [[modelClass]] attribute.
     */
    public function validateModelClass()
    {
        if ($this->isReservedKeyword($this->modelClass)) {
            $this->addError('modelClass', 'Class name cannot be a reserved PHP keyword.');
        }
        if ((empty($this->tableName) || substr_compare($this->tableName, '*', -1, 1)) && $this->modelClass == '') {
            $this->addError('modelClass', 'Model Class cannot be blank if table name does not end with asterisk.');
        }
    }

    /**
     * Validates the [[tableName]] attribute.
     */
    public function validateTableName()
    {
        if (strpos($this->tableName, '*') !== false && substr_compare($this->tableName, '*', -1, 1)) {
            $this->addError('tableName', 'Asterisk is only allowed as the last character.');

            return;
        }
        $tables = $this->getTableNames();
        if (empty($tables)) {
            $this->addError('tableName', "Table '{$this->tableName}' does not exist.");
        } else {
            foreach ($tables as $table) {
                $class = $this->generateClassName($table);
                if ($this->isReservedKeyword($class)) {
                    $this->addError('tableName', "Table '$table' will generate a class which is a reserved PHP keyword.");
                    break;
                }
            }
        }
    }

    protected $tableNames;
    protected $classNames;

    /**
     * @return array the table names that match the pattern specified by [[tableName]].
     */
    protected function getTableNames()
    {
        if ($this->tableNames !== null) {
            return $this->tableNames;
        }
        $db = $this->getDbConnection();
        if ($db === null) {
            return [];
        }
        $tableNames = [];
        if (strpos($this->tableName, '*') !== false) {
            if (($pos = strrpos($this->tableName, '.')) !== false) {
                $schema = substr($this->tableName, 0, $pos);
                $pattern = '/^' . str_replace('*', '\w+', substr($this->tableName, $pos + 1)) . '$/';
            } else {
                $schema = '';
                $pattern = '/^' . str_replace('*', '\w+', $this->tableName) . '$/';
            }

            foreach ($db->schema->getTableNames($schema) as $table) {
                if (preg_match($pattern, $table)) {
                    $tableNames[] = $schema === '' ? $table : ($schema . '.' . $table);
                }
            }
        } elseif (($table = $db->getTableSchema($this->tableName, true)) !== null) {
            $tableNames[] = $this->tableName;
            $this->classNames[$this->tableName] = $this->modelClass;
        }

        return $this->tableNames = $tableNames;
    }

    /**
     * Generates the table name by considering table prefix.
     * If [[useTablePrefix]] is false, the table name will be returned without change.
     * @param string $tableName the table name (which may contain schema prefix)
     * @return string the generated table name
     */
    public function generateTableName($tableName)
    {
        if (!$this->useTablePrefix) {
            return $tableName;
        }

        $db = $this->getDbConnection();
        if (preg_match("/^{$db->tablePrefix}(.*?)$/", $tableName, $matches)) {
            $tableName = '{{%' . $matches[1] . '}}';
        } elseif (preg_match("/^(.*?){$db->tablePrefix}$/", $tableName, $matches)) {
            $tableName = '{{' . $matches[1] . '%}}';
        }
        return $tableName;
    }

    /**
     * Generates a class name from the specified table name.
     * @param string $tableName the table name (which may contain schema prefix)
     * @param bool $useSchemaName should schema name be included in the class name, if present
     * @return string the generated class name
     */
    protected function generateClassName($tableName, $useSchemaName = null)
    {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }

        $schemaName = '';
        $fullTableName = $tableName;
        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos) . '_';
            }
            $tableName = substr($tableName, $pos + 1);
        }

        $db = $this->getDbConnection();
        $patterns = [];
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }

        // DEZERO - Check if table has a prefix equal to table name.
        // Example: class of table "user_user" will be User and not UserUSer
        if ( preg_match("/\_/", $className) )
        {
            $vec_class_name = explode("_", $className);
            if ( count($vec_class_name) === 2 && $vec_class_name[0] === $vec_class_name[1] )
            {
                $className = $vec_class_name[0];
            }
        }

        if ($this->standardizeCapitals) {
            $schemaName = ctype_upper(preg_replace('/[_-]/', '', $schemaName)) ? strtolower($schemaName) : $schemaName;
            $className = ctype_upper(preg_replace('/[_-]/', '', $className)) ? strtolower($className) : $className;
            return $this->classNames[$fullTableName] = Inflector::camelize(Inflector::camel2words($schemaName.$className));
        } else {
            return $this->classNames[$fullTableName] = Inflector::id2camel($schemaName.$className, '_');
        }

    }

    /**
     * Generates a query class name from the specified model class name.
     * @param string $modelClassName model class name
     * @return string generated class name
     */
    protected function generateQueryClassName($modelClassName)
    {
        $queryClassName = $this->queryClass;
        if (empty($queryClassName) || strpos($this->tableName, '*') !== false) {
            $queryClassName = $modelClassName . 'Query';
        }
        return $queryClassName;
    }

    /**
     * Generates a search class name from the specified model class name.
     * @param string $modelClassName model class name
     * @return string generated class name
     */
    protected function generateSearchClassName($modelClassName)
    {
        $searchClassName = $this->searchClass;
        if (empty($searchClassName) || strpos($this->tableName, '*') !== false) {
            $searchClassName = $modelClassName . 'Search';
        }
        return $searchClassName;
    }

    /**
     * @return Connection the DB connection as specified by [[db]].
     */
    protected function getDbConnection()
    {
        return Yii::$app->get($this->db, false);
    }

    /**
     * @return string|null driver name of db connection.
     * In case db is not instance of \yii\db\Connection null will be returned.
     * @since 2.0.6
     */
    protected function getDbDriverName()
    {
        /** @var Connection $db */
        $db = $this->getDbConnection();
        return $db instanceof \yii\db\Connection ? $db->driverName : null;
    }

    /**
     * Checks if any of the specified columns is auto incremental.
     * @param \yii\db\TableSchema $table the table schema
     * @param array $columns columns to check for autoIncrement property
     * @return bool whether any of the specified columns is auto incremental.
     */
    protected function isColumnAutoIncremental($table, $columns)
    {
        foreach ($columns as $column) {
            if (isset($table->columns[$column]) && $table->columns[$column]->autoIncrement) {
                return true;
            }
        }

        return false;
    }


    /**
     * prepare ENUM field values.
     *
     * @param array $columns
     *
     * @return array
     */
    public function getEnum($columns)
    {
        $enum = [];
        foreach ($columns as $column) {
            if (!$this->isEnum($column)) {
                continue;
            }

            $column_camel_name = str_replace(' ', '', ucwords(implode(' ', explode('_', $column->name))));
            $enum[$column->name]['func_opts_name'] = 'opts'.$column_camel_name;
            $enum[$column->name]['func_get_label_name'] = 'get'.$column_camel_name.'ValueLabel';
            $enum[$column->name]['label'] = StringHelper::uppercase(Inflector::camel2words($column->name));
            $enum[$column->name]['values'] = [];

            $enum_values = explode(',', substr($column->dbType, 4, strlen($column->dbType) - 1));

            foreach ($enum_values as $value) {
                $value = trim($value, "()'");

                $const_name = strtoupper($column->name.'_'.$value);
                $const_name = preg_replace('/\s+/', '_', $const_name);
                $const_name = str_replace(['-', '_', ' '], '_', $const_name);
                $const_name = preg_replace('/[^A-Z0-9_]/', '', $const_name);

                $label = Inflector::camel2words($value);

                $enum[$column->name]['values'][] = [
                    'value' => $value,
                    'label' => $label,
                    'const_name' => $const_name,
                    'camel_name' => Inflector::id2camel($value, '_'),
                ];
            }
        }

        return $enum;
    }

    /**
     * validate is ENUM.
     *
     * @param  $column table column
     *
     * @return type
     */
    public function isEnum($column)
    {
        return substr(strtoupper($column->dbType), 0, 4) === 'ENUM';
    }



    /**
     * Generates validation rules for serach class.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateSearchRules($table)
    {
        $vec_rules = [];
        $vec_types = [
            'null'      => [],
            'integer'   => [],
            'number'    => [],
            'safe'      => [],
        ];
        foreach ( $table->columns as $column )
        {
            if ( $column->allowNull )
            {
                $vec_types['null'][] = $column->name;
            }

            switch ( $column->type )
            {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $vec_types['integer'][] = $column->name;
                break;

                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $vec_types['number'][] = $column->name;
                break;

                default:
                    $vec_types['safe'][] = $column->name;
                break;
            }
        }

        foreach ( $vec_types as $type => $vec_columns )
        {
            if ( !empty($vec_columns) )
            {
                if ( $type === 'null' )
                {
                    $vec_rules[] = "'defaultNull' => [['" . implode("', '", $vec_columns) . "'], 'default', 'value' => null]";
                }
                else
                {
                    $vec_rules[] = "'{$type}Fields' => [['" . implode("', '", $vec_columns) . "'], '$type']";
                }
            }
        }

        // Custom filter rules (commented)
        $vec_rules[] = "\n            // Custom search filters\n            // 'customFilters' => [['name_filter'], 'safe']";

        return $vec_rules;
    }

    /**
     * Generate filter conditions for ActiveDataProvider
     */
    public function generateSearchFilters($table)
    {
        $vec_search_filters = [
            'compare'   => [],
            'like'      => [],
            'date'      => []
        ];
        foreach ($table->columns as $column)
        {
            if ( $column->phpType === 'integer' || $column->phpType === 'boolean' || $this->isEnum($column) || preg_match("/\_id$/", $column->name) || preg_match("/\_uuid$/", $column->name) )
            {
                if ( preg_match("/\_date$/", $column->name) )
                {
                    $vec_search_filters['date'][] = $column->name;
                }
                else
                {
                    $vec_search_filters['compare'][] = $column->name;
                }
            }
            else
            {
                $vec_search_filters['like'][] = $column->name;
            }
        }

        return $vec_search_filters;
    }


    /**
     * Return full module list (included CORE)
     */
    public function getModulesList()
    {
        // First of all, load "core" modules
        $vec_modules = Dz::getCoreModules();
        foreach ( $vec_modules as $module_name => $module_namespace )
        {
            $vec_modules['core_'. $module_name] = $module_namespace;
            unset($vec_modules[$module_name]);
        }

        // Now, process loaded modules in the application
        $vec_app_modules = Dz::getModules();

        if ( !empty($vec_app_modules) )
        {
            foreach ( $vec_app_modules as $module_name => $module_namespace )
            {
                if ( $module_name !== 'gii' && ! preg_match("/^\\\dezero\\\modules/", $module_namespace) )
                {
                    $vec_modules[$module_name] = $module_namespace;
                }
            }
        }

        return $vec_modules;
    }


    /**
     * Generate namespaces for relations
     */
    private function generateNamespaces($relationName, $refClassName) : void
    {
        if ( ! isset($this->relationsNamespaces[$relationName]) )
        {
            $this->relationsNamespaces[$relationName] = [];
        }

        // Models from ASSET module
        if ( $refClassName === 'AssetFile' || $refClassName === 'AssetImage' )
        {
            $this->relationsNamespaces[$relationName][$refClassName] = "dezero\modules\asset\models\\{$refClassName}";
        }

        // Models from CATEGORY module
        if ( $refClassName === 'Category' )
        {
            $this->relationsNamespaces[$relationName][$refClassName] = "dezero\modules\category\models\\{$refClassName}";
        }

        // Models from ENTITY module
        if ( $refClassName === 'Entity' || $refClassName === 'EntityFile' || $refClassName === 'StatusHistory' )
        {
            $this->relationsNamespaces[$relationName][$refClassName] = "dezero\modules\\entity\models\\{$refClassName}";
        }

        // Models from SETTINGS module
        if ( $refClassName === 'Country' || $refClassName === 'Language' )
        {
            $this->relationsNamespaces[$relationName][$refClassName] = "dezero\modules\settings\models\\{$refClassName}";
        }

        // Models from SYNC module
        if ( $refClassName === 'Batch' || $refClassName === 'ImportBatch' )
        {
            $this->relationsNamespaces[$relationName][$refClassName] = "dezero\modules\sync\models\\{$refClassName}";
        }
    }
}
