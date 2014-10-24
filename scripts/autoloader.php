<?php
class Autoloader {
    static public function loader($className) {
        $filename =  getcwd() .'/src/'. str_replace('\\', '/', $className) . ".php";
        if (file_exists($filename)) {
           require_once($filename);
            if (class_exists($className)) {
                return TRUE;
            }
        }
        return FALSE;
    }
}