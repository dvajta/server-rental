<?php

namespace console\controllers;

use Yii;
use common\models\User;
use yii\console\Controller;


class CronController extends Controller
{
    public $login;
    public $password;

    private const TOKEN_LIFETIME = 300;

    /**
     * @inheritdoc
     */
    public function options($actionId): array
    {
        return [
            'login',
            'password'
        ];
    }

    /**
     * @throws \yii\base\Exception
     */
    public function actionGetToken()
    {
        $user = User::findByUsername($this->login);
        if (!$user) {
            $this->stderr(sprintf('User with login "%s" not found!', $this->login) . "\n");
            return;
        }

        if (!$user->validatePassword($this->password)) {
            $this->stderr(sprintf('Wrong password entered for user "%s"!', $this->login) . "\n");
            return;
        }

        if ($token = $this->checkAliveToken($user)) {
            $this->stderr(sprintf('The authentication token for the user "%s" is still alive: %s', $this->login, $token) . "\n");
            return;
        }

        try {
            $token = Yii::$app->security->generateRandomString() . '_' . time();
            $user->auth_token = $token;
            $user->save();
            $this->stderr($token . "\n");
        } catch (\Throwable $e) {
            $this->stderr($e->getMessage() . "\n");
        }
    }

    /**
     * @param User $user
     * @return string|null
     */
    private function checkAliveToken(User $user): ?string
    {
        if ($user->auth_token === null) {
            return null;
        }

        $timestamp = (int) substr($user->auth_token, strrpos($user->auth_token, '_') + 1);
        if ($timestamp + self::TOKEN_LIFETIME >= time()) {
            return $user->auth_token;
        }

        return null;
    }
}
