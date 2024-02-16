<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;
use Migrations\Utilities\TableUpdateInterface;

class Migration_20240206101007_create_table_survey_questions_users extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("survey_questions_users", function (TableCreateInterface $table) {
            $table->addColumn("survey_question_id")->int()->nullable(false)->foreignKey("survey_questions", "id")->onDeleteCascade();
            $table->addColumn("user_id")->int()->nullable(false)->foreignKey("users", "id")->onDeleteCascade();
        });
    }

    public function down(Schema $schema)
    {
        $schema->updateTable("survey_questions_users", function (TableUpdateInterface $table) {
            $table->updateColumn("survey_question_id")->dropForeignKey();
            $table->updateColumn("user_id")->dropForeignKey();
        });
        $schema->dropTable("survey_questions_users");
    }
}
