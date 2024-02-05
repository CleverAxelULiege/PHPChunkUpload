<?php

namespace Surveys\Repositories;

use Database\DefaultDatabase;
use Database\Parent\Database;
use Surveys\Mapper;

class UserRepository{
    private Database $db;

    public function __construct(Database $db = new DefaultDatabase())
    {
        $this->db = $db;
    }

    public function getUserByName(string $username){
        $user = $this->db->run("SELECT * FROM users WHERE username ILIKE :username", [
            "username" => $username
        ])->fetch();

        if($user === false){
            return null;
        }

        return Mapper::toUserDTO($user);
    }

    public function getUserBySession(){
        if(!isset($_SESSION["user_id"])){
            return null;
        }

        $user = $this->db->run("SELECT * FROM users WHERE id = :user_id", [
            "user_id" => $_SESSION["user_id"]
        ])->fetch();

        if($user === false){
            return null;
        }

        return Mapper::toUserDTO($user);
    }
}