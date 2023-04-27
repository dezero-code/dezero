<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>
/**
 * <?= $className ?> query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; <?= date('Y'); ?> Fabián Ruiz
 */

namespace <?= $generator->queryNs ?>;

/**
 * ActiveQuery class for <?= $modelFullClassName ?>.
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
<?php
    // Filter by PRIMARY KEY
    $primaryKeyAttribute = null;
    if ( !empty($primaryKey) && is_array($primaryKey) && count($primaryKey) === 1 ) :
?>
<?php $primaryKeyAttribute = $primaryKey[0]; ?>
    /**
     * Filter the query by "<?= $primaryKeyAttribute; ?>" attribute value
     */
    public function <?= $primaryKeyAttribute; ?>(int $<?= $primaryKeyAttribute; ?>) : self
    {
        return $this->andWhere(['<?= $primaryKeyAttribute; ?>' => $<?= $primaryKeyAttribute; ?>]);
    }

<?php endif; ?>
<?php
    // Filter by MODEL TITLE
    if ( !empty($modelTitle) && is_array($modelTitle) && count($modelTitle) === 1 && ( $primaryKeyAttribute === null || $primaryKeyAttribute !== $modelTitle[0]) ) :
?>
<?php $modelTitleAttribute = $modelTitle[0]; ?>

    /**
     * Filter the query by "<?= $modelTitleAttribute; ?>" attribute value
     */
    public function <?= $modelTitleAttribute; ?>(string $<?= $modelTitleAttribute; ?>) : self
    {
        return $this->andWhere(['<?= $modelTitleAttribute; ?>' => $<?= $modelTitleAttribute; ?>]);
    }
<?php endif; ?>
}
