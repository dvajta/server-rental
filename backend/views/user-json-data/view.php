<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\UserJsonData $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Json Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-json-data-view">
    <p>
        <?= Html::a('Назад', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            //'user_id',
            'type',
            'json:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>
<?php
    $html = $model->jsonToHtmlList($model->json);
    echo '<div id="json-to-html">' . $html . '</div>';
?>

</div>
<script>
    window.addEventListener('load', function() {
        // Получим все вложенные списки и спрячем их
        var sublists = document.querySelectorAll("#json-to-html ul ul");
        for (var i = 0; i < sublists.length; i++) {
            sublists[i].style.display = "none";
        }

        // Добавим ссылки для раскрытия / сворачивания списков
        var listItems = document.querySelectorAll("#json-to-html li");
        for (var j = 0; j < listItems.length; j++) {
            var sublist = listItems[j].querySelector("ul");
            if (sublist) {
                var link = document.createElement("a");
                link.href = "#";
                link.innerText = "[+]";
                link.className = "expand";
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    var sublist = this.nextElementSibling;
                    sublist.style.display = (sublist.style.display === "none") ? "block" : "none";
                    this.innerText = (sublist.style.display === "none") ? "[+]" : "[-]";
                });
                listItems[j].insertBefore(link, sublist);
            }
        }
    });
</script>