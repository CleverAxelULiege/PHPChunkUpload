<?php

namespace Migrations;

use Migrations\Utilities\Defaults\DefaultJson;
use Migrations\Utilities\Schema;
use Migrations\Utilities\Migration;
use Migrations\Utilities\TableCreateInterface;

class Migration_20240205115826_create_table_users extends Migration
{

    public function up(Schema $schema)
    {
        $schema->createTable("users", function (TableCreateInterface $table) {
            $table->addColumn("id")->int()->autoIncrement()->primaryKey();
            $table->addColumn("username")->varchar(255)->nullable(false);
            $table->addColumn("password")->varchar(255)->nullable(false);
            $table->addColumn("roles")->json()->nullable(false)->default(DefaultJson::EMPTY_ARRAY);
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropTable("users");
    }
}
