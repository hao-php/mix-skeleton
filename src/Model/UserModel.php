<?php

namespace App\Model;

class UserModel extends BaseModel
{

    public string $table = "user";

    protected string $updateTimeField = 'created_at';

    protected string $createTimeField = 'updated_at';

    protected function buildCreateTime()
    {
        return date('Y-m-d H:i:s');
    }

    protected function buildUpdateTime($time = null)
    {
        // 创建的时候, 修改时间使用创建时间
        if (!empty($time)) {
            return $time;
        }
        return date('Y-m-d H:i:s');
    }


}