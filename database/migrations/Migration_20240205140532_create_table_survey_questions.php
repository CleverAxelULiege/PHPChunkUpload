<?php

namespace Migrations;

use Migrations\Utilities\Defaults\DefaultDatetime;
use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;

class Migration_20240205140532_create_table_survey_questions extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("survey_questions", function (TableCreateInterface $table) {
            $table->addColumn("id")->int()->autoIncrement()->primaryKey();
            $table->addColumn("question_fr")->varchar(1024)->nullable(false);
            // $table->addColumn("question_en")->varchar(1024)->nullable(false);
            // $table->addColumn("video_resolution")->smallint()->nullable(true)->default("null");
            // $table->addColumn("min_length_response_time")->int()->nullable(false)->default("0");
            // $table->addColumn("max_length_response_time")->int()->nullable(false);
            // $table->addColumn("accept_only_audio")->bool()->nullable(false)->default("false");
            $table->addColumn("created_at")->timestamp()->nullable(false)->default(DefaultDatetime::getCurrentTimestamp(0));
            $table->addColumn("updated_at")->timestamp()->nullable(true)->default("null");
            $table->addColumn("deleted_at")->timestamp()->nullable(true)->default("null");
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropTable("survey_questions");
    }
}
