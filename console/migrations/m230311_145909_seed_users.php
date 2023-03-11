<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m230311_145909_seed_users
 */
class m230311_145909_seed_users extends Migration
{
    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
     */
    public function up()
    {
        $faker = \Faker\Factory::create();

        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $users[] = [
                'username' => $faker->userName,
                'auth_key' => Yii::$app->security->generateRandomString(),
                'email' => $faker->email,
                'password_hash' => Yii::$app->security->generatePasswordHash('secret123'), // задаем одинаковый пароль для всех пользователей
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }

        $this->batchInsert(User::tableName(), ['username', 'auth_key', 'email', 'password_hash', 'created_at', 'updated_at'], $users);
    }

    public function down()
    {
        $this->delete(User::tableName());
    }
}
