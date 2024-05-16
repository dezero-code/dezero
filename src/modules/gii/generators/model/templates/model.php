<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $properties array list of properties (property => [type, name. comment]) */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>
/**
 * <?= $className ?> model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; <?= date('Y'); ?> Fabián Ruiz
 */

namespace <?= $generator->ns ?>;

use dezero\helpers\ArrayHelper;
<?php foreach ($relations as $name => $relation): ?><?php if ( !empty($relationsNamespaces[$name]) ) : ?><?php foreach ( $relationsNamespaces[$name] as $namespace ) : ?>
use <?= $namespace; ?>;
<?php endforeach; ?><?php endif; ?><?php endforeach; ?>
use <?= $generator->ns ?>\base\<?= $className ?> as Base<?= $className ?>;
use <?= $generator->queryNs; ?>\<?= $queryClassName; ?>;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * <?= $className ?> model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
<?php foreach ($properties as $property => $data): ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
 * -------------------------------------------------------------------------
 * RELATIONS
 * -------------------------------------------------------------------------
<?php foreach ($relations as $name => $relation): ?>
<?php if ( isset($relationsOne[$name]) ) : ?> * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?><?php endif; ?>
<?php endforeach; ?>
<?php foreach ($relations as $name => $relation): ?>
<?php if ( isset($relationsMany[$name]) ) : ?> * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?><?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends Base<?= $className . "\n" ?>
{
<?php if ( !empty($enum) ) : ?>
<?php
    foreach($enum as $column_name => $column_data){
        echo '    // ' . ucfirst(strtolower($column_data['label'])) . 's' . PHP_EOL;
        foreach ($column_data['values'] as $enum_value){
            echo '    public const ' . $enum_value['const_name'] . ' = \'' . $enum_value['value'] . '\';' . PHP_EOL;
        }
        echo PHP_EOL;
    }
?>

<?php endif; ?>
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [<?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n        ") ?>];
        */

        return ArrayHelper::merge(
            parent::rules(),
            [
                // Custom validation rules
            ]
        );
    }


    /**
     * {@inheritdoc}
     *
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }
    */


    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => Yii::t('". $generator->messageCategory ."', " . $generator->generateString($label) . "),\n" ?>
<?php endforeach; ?>
        ];
    }

<?php if (!empty($relations)): ?>

   /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
<?php foreach ($relations as $name => $relation): ?>
<?php if ( isset($relationsOne[$name]) ) : ?>

    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function get<?= $name ?>() : ActiveQueryInterface
    {
        <?= $relation[0] . "\n" ?>
    }

<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($relations as $name => $relation): ?>
<?php if ( isset($relationsMany[$name]) ) : ?>

    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function get<?= $name ?>() : ActiveQueryInterface
    {
        <?= $relation[0] . "\n" ?>
    }

<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if ( !empty($enum) ) : ?>

    /*
    |--------------------------------------------------------------------------
    | ENUM LABELS
    |--------------------------------------------------------------------------
    */
<?php
    // Custom ENUM "labels" methods
    foreach ($enum as $column_name => $column_data) :
?>

    /**
     * Get "<?= $column_name?>" labels
     */
    public function <?= $column_name; ?>_labels() : array
    {
        return [
<?php
        foreach($column_data['values'] as $k => $value)
        {
            echo "            "."self::" . $value['const_name'] . " => Yii::t('". $generator->messageCategory ."', " . $generator->generateString($value['label']) . "),\n";
        }
?>
        ];
    }


    /**
     * Get "<?= $column_name?>" specific label
     */
    public function <?= $column_name; ?>_label(?string $<?= $column_name; ?> = null) : string
    {
        $<?= $column_name; ?> = ( $<?= $column_name; ?> === null ) ? $this-><?= $column_name; ?> : $<?= $column_name; ?>;
        $vec_labels = $this-><?= $column_name; ?>_labels();

        return isset($vec_labels[$<?= $column_name; ?>]) ? $vec_labels[$<?= $column_name; ?>] : '';
    }

    <?php endforeach; ?>
<?php
    // Custom ENUM "labels" methods
    foreach ($enum as $column_name => $column_data) :
?>

    /*
    |--------------------------------------------------------------------------
    | <?= $column_data['label']; ?> METHODS
    |--------------------------------------------------------------------------
    */

<?php
    foreach($column_data['values'] as $k => $value) :
?>
    public function is<?= $value['camel_name']; ?>() : bool
    {
        return $this-><?= $column_name; ?> === self::<?= $value['const_name']; ?>;
    }

<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>

    /*
    |--------------------------------------------------------------------------
    | TITLE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Title used for this model
     */
    public function title() : string
    {
        return <?= !empty($modelTitle) ? '$this->'. implode(' ." - ". $this->', $modelTitle) : '""'; ?>;
    }
}
