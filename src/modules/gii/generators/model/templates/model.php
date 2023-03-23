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

use <?= $generator->queryNs; ?>\<?= $queryClassName; ?>;
use Yii;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data): ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>


    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [<?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n        ") ?>];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => Yii::t('". $generator->messageCategory ."', " . $generator->generateString($label) . "),\n" ?>
<?php endforeach; ?>
        ];
    }


    /**
     * -------------------------------------------------------------------------
     * RELATIONS
     * -------------------------------------------------------------------------
<?php if (!empty($relations)): ?>
     *
<?php foreach ($relations as $name => $relation): ?>
<?php if ( isset($relationsOne[$name]) ) : ?>     * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?><?php endif; ?>
<?php endforeach; ?>
<?php foreach ($relations as $name => $relation): ?>
<?php if ( isset($relationsMany[$name]) ) : ?>     * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?><?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
     */

    /**
     * This function helps \mootensai\relation\RelationTrait runs faster
     * @return array relation names of this model
     */
    public function relationNames() : array
    {
        return [<?= "\n            '" . implode("',\n            '", array_keys($relations)) . "'\n        " ?>];
    }

    /**
     * @return \yii\db\ActiveQuery
     *
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['user_id' => 'author_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     *
    public function getFiles()
    {
        return $this->hasMany(AssetFile::class, ['created_user_id' => 'user_id']);
    }
    */
<?php /* foreach ($relations as $name => $relation): ?>


    /**
     * @return \yii\db\ActiveQuery
     *
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; */ ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n\n";
?>
    /**
     * @return <?= $queryClassName ."\n" ?>
     */
    public static function find()
    {
        return new <?= $queryClassName ?>(static::class);
    }
<?php endif; ?>


    /**
     * Title used for this model
     *
     * @return string
     */
    public function title()
    {
        return <?= !empty($modelTitle) ? implode(' - ', $modelTitle) : '""'; ?>;
    }
}
