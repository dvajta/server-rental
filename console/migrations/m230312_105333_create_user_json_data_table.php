<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_json_data}}`.
 */
class m230312_105333_create_user_json_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_json_data}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('ID пользователя'),
            'type' => $this->string()->notNull()->comment('Тип запроса'),
            'json' => $this->text()->notNull()->comment('Данные JSON'),
            'created_at' =>$this->dateTime()->notNull()->comment('Дата создания'),
            'updated_at' =>$this->dateTime()->defaultValue(null)->comment('Дата обновления'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_json_data}}');
    }
}
