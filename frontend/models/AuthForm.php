<?php

namespace frontend\models;

use yii\base\Model;

class AuthForm extends Model
{
    public $token;
    public $method;
    public $json;

    public function rules()
    {
        return [
            [['token', 'json'], 'required'],
            [['method','json'], 'string']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'token' => 'Токен доступа',
            'method' => 'Метод передачи данных',
            'json' => 'Данные в формате JSON',
        ];
    }
}