<?php

/** @var yii\web\View $this */
/** @var \frontend\models\AuthUpdateForm $modelUpdate */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';

$methods = ['get' => 'GET', 'post' => 'POST'];
?>
<div class="site-index">
    <div class="p-5 bg-transparent rounded-3">

    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
                <h4>Аутентификация (добавление)</h4>
                <?php $form = ActiveForm::begin(['id' => 'auth-form']) ?>

                <?= $form->field($model, 'method')->dropDownList($methods) ?><br>
                <?= $form->field($model, 'token')->textInput() ?><br>
                <?= $form->field($model, 'json')->textarea([
                        'rows' => 10,
                        'placeholder' => '{"name": "John","age": 30,"email": "john@example.com","phone": "+1 555-555-1234","pets": ["dog", "cat"]}'
                ]) ?><br>
                <?= Html::submitButton('Отправить', [
                        'class' => 'btn btn-primary',
                ]) ?>

                <?php ActiveForm::end(); ?>
                <br><br>
                <div class="auth-form-response bg-success text-white p-2 d-none"></div>
            </div>
            <div class="col-lg-6">
                <h4>Аутентификация (обновление)</h4>
                <?php $form = ActiveForm::begin(['id' => 'auth-update-form']) ?>

                <?= $form->field($modelUpdate, 'method')->dropDownList($methods) ?><br>
                <?= $form->field($modelUpdate, 'token')->textInput() ?><br>
                <?= $form->field($modelUpdate, 'id')->textInput() ?><br>
                <?= $form->field($modelUpdate, 'code')->textarea([
                        'rows' => 6,
                        'placeholder' => '$objData->pets[0] = "pig", $objData->name = "Petr", $objData->age = 40'
                ]) ?><br>
                <?= Html::submitButton('Отправить', [
                    'class' => 'btn btn-primary',
                ]) ?>

                <?php ActiveForm::end(); ?>
                <br><br>
                <div class="auth-update-form-response bg-success text-white p-2 d-none"></div>

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
            url: '" . Url::to(['site/add-with-token']) . "',
            type: method,
            data: {json: json}, // некоторые данные
            headers: {
                'X-CSRF-Token': csrfToken,
                'X-MyToken': token
            },
            success: function(res, status, jqXHR) {
                if(!res) alert('Ошибка аутентификации');
                $('#auth-form input[type=\"text\"], #auth-form textarea').val('');
                const authFormResponse = $('.auth-form-response');
                authFormResponse.text(res.message);
                authFormResponse.removeClass('d-none');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                let response = JSON.parse(jqXHR.responseText);
                let message = response.message;
                $('#auth-form input[type=\"text\"], #auth-form textarea').val('');
                const authFormResponse = $('.auth-form-response');
                authFormResponse.text(message);
                authFormResponse.removeClass('bg-success').addClass('bg-danger').removeClass('d-none');
            }
        });
        submitted = true;
        reloadPage();
    });
    
    $('#auth-update-form').on('submit', function(e) {
        if (submitted) {
             e.preventDefault();
             return;
        }
        $('#auth-update-form button[type=\"submit\"]').prop('disabled', true);
        
        let csrfToken = $('meta[name=\"csrf-token\"]').attr(\"content\");
        let token = $('#authupdateform-token').val();
        let code = $('#authupdateform-code').val();
        let method = $('#authupdateform-method option:selected').text();
        let id = $('#authupdateform-id').val();
        
        $.ajax({
            url: '" . Url::to(['site/update-with-token']) . "',
            type: method,
            data: {
                code: code,
                id: id
            }, 
            headers: {
                'X-CSRF-Token': csrfToken,
                'X-MyToken': token
            },
            success: function(res) {
                if(!res) alert('Ошибка аутентификации');
                $('#auth-update-form input[type=\"text\"], #auth-update-form textarea').val('');
                const authFormResponse = $('.auth-update-form-response');
                authFormResponse.text(res.message);
                authFormResponse.removeClass('d-none');
                console.log(res);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                let response = JSON.parse(jqXHR.responseText);
                let message = response.message;
                $('#auth-update-form input[type=\"text\"], #auth-update-form textarea').val('');
                const authFormResponse = $('.auth-update-form-response');
                authFormResponse.text(message);
                authFormResponse.removeClass('bg-success').addClass('bg-danger').removeClass('d-none');
            }
        });
        submitted = true;
        reloadPage();
    });
    
    function reloadPage() {
        setTimeout(function() {
            location.reload();
        }, 3000);
    }
");

