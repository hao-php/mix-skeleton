<?php

namespace App\Model;

use App\Container\DB;
use Haoa\MixExt\Db\Model;
use Haoa\Util\SingleTon\ContextSingleTonTrait;

class BaseModel extends Model
{

    use ContextSingleTonTrait;


    public function __construct()
    {
        $this->database = DB::instance();
    }

}