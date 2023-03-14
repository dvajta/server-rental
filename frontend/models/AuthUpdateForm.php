<?php


namespace frontend\models;


use yii\base\Model;

class AuthUpdateForm extends Model
{
    public $token;
    public $method;
    public $code;
    public $id;

    public function rules()
    {
        return [
            [['token', 'code', 'id'], 'required'],
            [['method','code'], 'string'],
            ['id', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID записи для обновлени',
            'token' => 'Токен доступа',
            'method' => 'Метод передачи данных',
            'code' => 'Код-инструкция обновления',
        ];
    }
}