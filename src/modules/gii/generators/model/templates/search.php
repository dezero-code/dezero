<?php
/**
 * This is the template for generating the Search subclass.
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
if ($generator->ns !== $generator->searchNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>
/**
 * <?= $className ?> search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; <?= date('Y'); ?> Fabián Ruiz
 */

namespace <?= $generator->searchNs ?>;

use <?= $generator->ns ?>\<?= $modelClassName ?>;
use <?= $generator->queryNs ?>\<?= $queryClassName; ?>;
use yii\data\ActiveDataProvider;

/**
 * Search class for <?= $modelFullClassName ?>.
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= $modelClassName . "\n" ?>
{
    /**
     * @var <?= $queryClassName ."\n"; ?>
     */
    protected $query;


    /**
     * <?= $className ?> constructor
     */
    public function __construct(<?= $queryClassName; ?> $query, array $vec_config = [])
    {
        $this->query = $query;
        parent::__construct($vec_config);
    }


    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [<?= empty($searchRules) ? '' : ("\n            " . implode(",\n            ", $searchRules) . ",\n        ") ?>];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = $this->query;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Uncomment the following line if you do not want to return any records when validation fails
        if ( ! ( $this->load($params) && $this->validate() ) )
        {
            return $dataProvider;
        }

<?php if ( !empty($searchFilters['date']) ): ?>

        // Date filter
        if ( $this->created_date !== null )
        {
            $date = strtotime($this->created_date);
            $query->andFilterWhere(['between', 'created_date', $date, $date + 3600 * 24]);
        }
<?php /*foreach ( $searchFilters['date'] as $column_name ) : ?>
        if ( $this-><?= $column_name; ?> !== null )
        {
            $date = strtotime($this-><?= $column_name; ?>);
            $query->andFilterWhere(['between', '<?= $column_name; ?>', $date, $date + 3600 * 24]);
        }
<?php endforeach;*/ ?>
<?php endif; ?>
<?php if ( !empty($searchFilters['compare']) ): ?>

        // Compare conditions
        $query->andFilterWhere([
<?php foreach ( $searchFilters['compare'] as $column_name ) : ?>
            '<?= $column_name; ?>' => $this-><?= $column_name; ?>,
<?php endforeach; ?>
        ]);
<?php endif; ?>
<?php if ( !empty($searchFilters['like']) ): ?>
<?php foreach ( $searchFilters['like'] as $num_column => $column_name ) : ?>
<?php if ( $num_column === 0 ) : ?>

        // Like conditions
        $query->andFilterWhere(['like', '<?= $column_name; ?>', $this-><?= $column_name; ?>])<?php if ( count($searchFilters['like']) === ($num_column + 1) ) : ?>;<?php endif; ?><?php echo "\n"; ?>
<?php else : ?>
            ->andFilterWhere(['like', '<?= $column_name; ?>', $this-><?= $column_name; ?>])<?php if ( count($searchFilters['like']) === ($num_column + 1) ) : ?>;<?php endif; ?><?php echo "\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>

        return $dataProvider;
    }
}
