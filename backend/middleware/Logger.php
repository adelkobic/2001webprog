<?php

class Logger {
    public static function info($message) {
        self::write('info.log', 'INFO', $message);
    }

    public static function error($message) {
        self::write('error.log', 'ERROR', $message);
    }

    private static function write($file, $level, $message) {
        $path = __DIR__ . '/../logs/' . $file;
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $level . ' ' . $message . PHP_EOL;
        file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }
}
