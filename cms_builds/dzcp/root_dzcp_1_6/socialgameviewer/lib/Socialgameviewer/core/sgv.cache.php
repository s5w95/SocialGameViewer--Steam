<?php
class SgvCache {

    private static $file_destination;

    public static function init() {
        self::$file_destination = SGV_BASE.'_local.cache/';
        if (!is_writable(self::$file_destination)) {
            die("Cache directory not exist or writable need 777 on: ".self::$file_destination );
        }
    }

    public static function set($object, $name) {
        $handle = fopen(self::$file_destination.$name.'.che', "w");
        fwrite($handle, $object);
        fclose($handle);
    }

    public static function get($name, $time = NULL) {
        $file = self::$file_destination.$name.'.che';
        if (file_exists($file)) {
            if ($time != NULL) {
                if (time()-filemtime($file) > $time) return NULL;
            }
            return file_get_contents($file);
        }
        return NULL;
    }

    public static function get_filemtime($name) {
        $file = self::$file_destination.$name.'.che';
        if (file_exists($file)) {
            return filemtime($file);
        }
        return false;
    }
}