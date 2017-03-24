<?php

use yii\db\Schema;
use yii\db\Migration;

class m170324_074908_init_db extends Migration
{
    public function up()
    {
        $sql = file_get_contents(__DIR__ . "/init_db.sql");
        $this->execute($sql);
    }

    public function down()
    {
        echo "m170324_074908_init_db cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
