<?php

namespace App\Caching;

use App\Core\Request\Request;

class Cache
{
    private static $request;
    private static $cacheFileName;
    private static $CacheEnabled;

    public function __construct()
    {
        self::$request = new Request;
        self::$CacheEnabled = $_ENV['CACHE_ENABLED'];
    }

    private static function init()
    {
        if (self::$request->method != "GET") {
            self::$CacheEnabled = 0;
        }
        self::flush();

        self::$cacheFileName = $_ENV['CACHE_DIR'] . md5(self::$request->uri) . 'json';
    }

    public static function start()
    {
        self::init();
        if (!self::$CacheEnabled)
            return;

        if (file_exists(self::$cacheFileName) && fileatime(self::$cacheFileName) < (fileatime(self::$cacheFileName) + $_ENV['CACHE_EXPIRE_TIME'])) {
            readfile(self::$cacheFileName);
            die();
        }
        ob_start();
    }

    public static function end()
    {
        if (!self::$CacheEnabled) {
            return;
        }
        file_put_contents(self::$cacheFileName, ob_get_contents());
        ob_end_flush();
    }

    private static function flush()
    {
        $files = glob($_ENV['CACHE_DIR'], '*');
        foreach ($files as $file) {
            if (fileatime($file) > (fileatime(self::$cacheFileName) + $_ENV['CACHE_EXPIRE_TIME'])) {
                unlink($file);
            }
        }
    }
}