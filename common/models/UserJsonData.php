<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user_json_data".
 *
 * @property int $id
 * @property int $user_id ID пользователя
 * @property string $type Тип запроса
 * @property string $json Данные JSON
 * @property string $created_at Дата создания
 * @property string|null $updated_at Дата обновления
 */
class UserJsonData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_json_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'json'], 'required'],
            [['user_id'], 'integer'],
            [['json'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'json' => 'Json',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}