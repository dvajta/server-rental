<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
$form = ActiveForm::begin([
    'id' => 'auth-form',
]);
$methods = ['get' => 'GET', 'post' => 'POST'];
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-transparent rounded-3">

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h4>Аутентификация (добавление)</h4>
                <?= $form->field($model, 'method')->dropDownList($methods) ?><br>
                <?= $form->field($model, 'token')->textInput() ?><br>
                <?= $form->field($model, 'json')->textarea(['rows' => 10]) ?><br>
                <?= Html::submitButton('Отправить', [
                        'class' => 'btn btn-primary',
                ]) ?>

                <?php ActiveForm::end(); ?>
                <br><br>
                <div class="auth-form-response bg-success text-white p-2 d-none">Это зеленая плашка</div>
            </div>
            <div class="col-lg-4">
                <h4>Аутентификация (обновление)</h4>


            </div>
            <div class="col-lg-4">
                <h4>Список записей</h4>


            </div>
        </div>

    </div>
</div>
<?php $this->registerJs("
    let submitted = false;
    $('#auth-form').on('submit', function(e) {
        if (submitted) {
             e.preventDefault();
             return;
        }
        $('#auth-form button[type=\"submit\"]').prop('disabled', true);
        
        let csrfToken = $('meta[name=\"csrf-token\"]').attr(\"content\");
        let token = $('#authform-token').val();
        let json = $('#authform-json').val();
        let method = $('#authform-method option:selected').text();
        
        $.ajax({
            url: '" . Url::to(['site/token']) . "',
            type: method,
            data: {json: json}, // некоторые данные
            headers: {
                'X-CSRF-Token': csrfToken,
                'X-MyToken': token
            },
            success: function(res) {
                if(!res) alert('Ошибка аутентификации');
                $('#auth-form input[type=\"text\"], #auth-form textarea').val('');
                const authFormResponse = $('.auth-form-response');
                authFormResponse.text(res);
                authFormResponse.removeClass('d-none');
                console.log(res);
            },
        });
        submitted = true;
    });
");

