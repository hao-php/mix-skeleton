<?php

namespace App\Model;

use App\Container\DB;

class UserModel extends BaseModel
{

    public string $table = "user";

    public function __construct()
    {
        $this->database = DB::instance();
    }


}