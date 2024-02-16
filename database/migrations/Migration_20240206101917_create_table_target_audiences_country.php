<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;
use Migrations\Utilities\TableUpdateInterface;

class Migration_20240206101917_create_table_target_audiences_country extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("target_audiences_country", function (TableCreateInterface $table) {
            $table->addColumn("id")->autoIncrement()->int()->primaryKey();
            $table->addColumn("country")->varchar("255")->nullable(false);
            $table->addColumn("survey_question_id")->int()->nullable(false)->foreignKey("survey_questions", "id")->onDeleteCascade();
        });
    }

    public function down(Schema $schema)
    {
        $schema->updateTable("target_audiences_country", function (TableUpdateInterface $table) {
            $table->updateColumn("survey_question_id")->dropForeignKey();
        });
        $schema->dropTable("target_audiences_country");
    }
}
