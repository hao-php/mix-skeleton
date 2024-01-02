<?php

if (!function_exists('env')) {
    /**
     * @param string $key
     * @param null $default
     * @return array|bool|string|null
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

/**
 * 获取配置
 * @param $key
 * @param $default
 * @return mixed
 */
function config($key, $default = null)
{
    return \App\Container\Config::instance()->get($key, $default);
}

/**
 * 获取毫秒
 */
function getMillisecond()
{
    list($s1, $s2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}

/**
 * 格式化展示毫秒时间
 * @param $time
 * @return string
 */
function getMillisecondText($time)
{
    $second = floor($time / 1000);
    $mill = $time % 1000;
    return $second . '秒' . $mill . '毫秒';
}

/**
 * @return string
 */
function generateUniqid(int $length = 7)
{
    if ($length > 32 || $length < 1) {
        throw new \InvalidArgumentException('The uid length must be an integer between 1 and 32');
    }
    return substr(bin2hex(random_bytes((int)ceil($length / 2))), 0, $length);
}

/**
 * @param $data
 * @return false|string
 */
function enJson($data)
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * @param string $json
 * @return mixed
 */
function deJson($json)
{
    if (empty($json)) {
        return [];
    }
    return @json_decode($json, true);
}
