<?php

namespace frontend\controllers;

use common\models\User;
use common\models\UserJsonData;
use frontend\models\AuthForm;
use frontend\models\AuthUpdateForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    private const TOKEN_LIFETIME = 300;
    private array $error = [];
    private ?string $accessText = null;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new AuthForm();
        $modelUpdate = new AuthUpdateForm();

        return $this->render('index', [
            'model' => $model,
            'modelUpdate' => $modelUpdate
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * @return false|string
     * @throws NotFoundHttpException
     */
    public function actionAddWithToken(): string
    {
        if (!$this->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $time_start = microtime(true);
        $memory_start = memory_get_usage();

        [$data, $token, $type] = $this->getRequestData();

        if (!$user = User::findByAuthToken(trim($token))) {
            return 'User not found';
        }

        if (!$this->isAccess($token, $user)) {
            return $this->accessText;
        }

        if (!$dataId = $this->saveData($data, $user->id, $type)) {
            Yii::error(sprintf('Data not saved for the user "%s" and token %s', $user->username, $token));
            return 'Data not saved';
        }

        $indicators = $this->calculateIndicators($time_start, $memory_start);

        return 'The request was successful (ID = ' . $dataId . '): ' . Json::encode($indicators);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdateWithToken(): string
    {
        if (!$this->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $time_start = microtime(true);
        $memory_start = memory_get_usage();

        [$code, $id, $token, $type] = $this->getRequestData();

        if (!$user = User::findByAuthToken(trim($token))) {
            return 'User not found!';
        }

        if (!$this->isAccess($token, $user)) {
            return $this->accessText;
        }

        if (!$this->updateData($user->id, $code, $id, $type)) {
            Yii::error(sprintf('Data not updated for the user "%s" and token %s', $user->username, $token));
            return 'Data not updated! ' . Json::encode($this->error) ?? '';
        }

        $indicators = $this->calculateIndicators($time_start, $memory_start);

        return 'The request was successful: ' . Json::encode($indicators);
    }

    /**
     * @param string $token
     * @param User $user
     * @return bool
     */
    private function isAccess(string $token, User $user): bool
    {
        if (!$this->isValidToken($token)) {
            Yii::$app->user->logout();
            Yii::warning(sprintf('The authentication token for the user "%s" is not alive: %s', $user->username, $token));
            $this->accessText = 'Token is not valid!';
            return false;
        }

        if (Yii::$app->user->isGuest) {
            $webUser = Yii::$app->user;
            $webUser->login($user);
        }
        return true;
    }

    /**
     * @param int $userId
     * @param string $code
     * @param int $id
     * @param string $type
     * @return bool
     */
    private function updateData(int $userId, string $code, int $id, string $type): bool
    {
        $userJsonItem = UserJsonData::find()->where(['user_id' => $userId, 'id' => $id])->one();
        if (!$userJsonItem) {
            $this->error[] = 'Entry no!';
            return false;
        }

        $objData = Json::decode($userJsonItem->json, false);

        foreach(explode(",", $code) as $line) {
            try {
                eval(''. $line . ';');
            } catch (\Throwable $e) {
                $this->error[] = $e->getMessage();
                continue;
            }

        }

        if ($this->error) {
            return false;
        }

        $json = Json::encode($objData);
        $userJsonItem->json = $json;
        $userJsonItem->type = $type;
        return $userJsonItem->save();
    }

    /**
     * @param string $data
     * @param int $userId
     * @param string $type
     * @return int|null
     */
    private function saveData(string $data, int $userId, string $type): ?int
    {
        $userJsonData = new UserJsonData();
        $userJsonData->user_id = $userId;
        $userJsonData->type = $type;
        $userJsonData->json = $data;
        return $userJsonData->save() ? $userJsonData->id : null;
    }

    /**
     * @param $time_start
     * @param $memory_start
     * @return array
     */
    private function calculateIndicators($time_start, $memory_start): array
    {
        $time_end = microtime(true);
        $memory_end = memory_get_usage();

        $time = $time_end - $time_start;
        $memory = $memory_end - $memory_start;

        return ['time' => $time, 'memory' => $memory];
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isValidToken(string $token): bool
    {
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);

        return $timestamp + self::TOKEN_LIFETIME >= time();
    }

    /**
     * @return array
     */
    private function getRequestData(): array
    {
        $token = $this->request->getHeaders()->get('X-MyToken');
        switch (true) {
            case $this->request->isGet:
                if (isset( $this->request->get()['json'])) {
                    return [$this->request->get()['json'], $token, 'get'];
                }
                return [$this->request->get()['code'], $this->request->get()['id'], $token, 'get'];

            default:
                if (isset( $this->request->post()['json'])) {
                    return [$this->request->post()['json'], $token, 'post'];
                }
                return [$this->request->post()['code'], $this->request->post()['id'], $token, 'post'];
        }
    }
}
