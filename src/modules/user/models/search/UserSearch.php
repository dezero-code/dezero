<?php
/**
 * UserSearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\models\search;

use dezero\modules\user\models\User;
use dezero\modules\user\models\query\UserQuery;
use yii\data\ActiveDataProvider;

/**
 * Search class for \dezero\modules\user\models\User.
 *
 * @see \dezero\modules\user\models\User
 */
class UserSearch extends User
{
    /**
     * @var UserQuery
     */
    protected $query;


    /**
     * UserSearch constructor
     */
    public function __construct(UserQuery $query, array $vec_config = [])
    {
        $this->query = $query;
        parent::__construct($vec_config);
    }


    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['first_name', 'last_name', 'last_login_date', 'last_login_ip', 'last_verification_date', 'last_change_password_date', 'default_role', 'default_theme', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],
            'integerFields' => [['user_id', 'last_login_date', 'is_verified_email', 'last_verification_date', 'is_force_change_password', 'last_change_password_date', 'is_superadmin', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'safeFields' => [['username', 'email', 'password', 'first_name', 'last_name', 'status_type', 'language_id', 'last_login_ip', 'default_role', 'default_theme', 'timezone', 'entity_uuid'], 'safe'],
        ];
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


        // Date filter
        if ( $this->created_date !== null )
        {
            $date = strtotime($this->created_date);
            $query->andFilterWhere(['between', 'created_date', $date, $date + 3600 * 24]);
        }

        // Compare conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'status_type' => $this->status_type,
            'language_id' => $this->language_id,
            'is_verified_email' => $this->is_verified_email,
            'is_force_change_password' => $this->is_force_change_password,
            'is_superadmin' => $this->is_superadmin,
            'disabled_user_id' => $this->disabled_user_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'entity_uuid' => $this->entity_uuid,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'last_login_ip', $this->last_login_ip])
            ->andFilterWhere(['like', 'default_role', $this->default_role])
            ->andFilterWhere(['like', 'default_theme', $this->default_theme])
            ->andFilterWhere(['like', 'timezone', $this->timezone]);

        return $dataProvider;
    }
}
