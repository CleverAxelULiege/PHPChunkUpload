<?php

namespace Migrations;

use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;
use Migrations\Utilities\TableUpdateInterface;

class Migration_20240205151348_create_table_target_audiences_age extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("target_audiences_age", function (TableCreateInterface $table) {
            $table->addColumn("id")->autoIncrement()->int()->primaryKey();
            $table->addColumn("min_age")->smallint()->nullable(true)->default("null");
            $table->addColumn("max_age")->smallint()->nullable(true)->default("null");
            $table->addColumn("survey_question_id")->int()->nullable(false)->foreignKey("survey_questions", "id")->onDeleteCascade();
        });
    }

    public function down(Schema $schema)
    {
        $schema->updateTable("target_audiences_age", function (TableUpdateInterface $table) {
            $table->updateColumn("survey_question_id")->dropForeignKey();
        });

        $schema->dropTable("target_audiences_age");
    }
}
