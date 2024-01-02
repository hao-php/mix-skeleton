<?php

namespace App\Model;

use App\Common\Util\SingleTon\ContextSingleTonTrait;
use App\Container\DB;
use Haoa\MixExt\Db\Model as MixModel;

class BaseModel extends MixModel
{

    use ContextSingleTonTrait;

    /**
     * 更新的时候自动写入修改时间
     * @var string
     */
    protected string $updateTimeField = '';

    /**
     * 创建的时候自动写入创建时间
     * @var string
     */
    protected string $createTimeField = '';

    public function __construct()
    {
        $this->database = DB::instance();
    }

}