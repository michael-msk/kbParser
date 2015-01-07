<?php
/**
 * @package kbLogger
 * @copyright Copyright (c) 2015 Michael V. Scherbinsky
 * @author Michael V. Scherbinsky <michael@kbnet.ru>
 * Created date: 06.01.15 Time: 14:06
 * @license
 */


class kbLogger {

    protected static $_instance;

    protected static $_isDebug;
    protected static $_fileName;

    public static function getInstance($fileName, $isDebug = false, $append = false) { // получить экземпл€р данного класса
        if (self::$_instance === null) { // если экземпл€р данного класса  не создан
            self::$_instance = new self($fileName, $isDebug, $append);  // создаем экземпл€р данного класса
        }
        return self::$_instance; // возвращаем экземпл€р данного класса
    }

    private function __construct($fileName, $isDebug = false, $append = false)
    {
        if ( empty($fileName) )
        {
            throw new \Exception('The name of the file can\'t be empty.' . PHP_EOL);
        }

        self::$_isDebug = $isDebug;
        self::$_fileName = $fileName;

        if ( $append === false )
        {
            //-- удал€ем предыдущий лог
            self::delete();
        }

        $text = '-- Start Log -- ' . date(DATE_RFC822) . ' --' . PHP_EOL;

        self::add( $text );
    }

    private function __clone() { }

    // private function __wakeup() { }

    public function __destruct()
    {
        $text = '-- End Log ---- ' . date(DATE_RFC822) . ' --' . PHP_EOL;

        self::add( $text );
    }

    private function delete()
    {
        if ( file_exists(self::$_fileName) )
        {
            unlink( self::$_fileName );
        }
    }

    public static function add( $text )
    {
        if ( file_put_contents(self::$_fileName, $text, FILE_APPEND) === false )
        {
            throw new \Exception('Write error to log. ' . self::$_fileName . ' < '. PHP_EOL);

        }
    }

    public static function addVariable($variable, $nameVariable = "", $nameFile = "", $row = "")
    {
        // $obj = self::$_instance;

        $text = "";

        if ( !empty($nameFile) )
        {
            $text .= ">> file: " . $nameFile . " -- row: " . $row . PHP_EOL;
        }

        if ( !empty($nameVariable) )
        {
            $text .= '$' . $nameVariable . ' = ';
        }

        if ( empty($variable) )
        {
            $variable = 'empty';
        }

        if ( is_array($variable) || is_object($variable) )
        {
            $text .= print_r($variable, true);
        } else {
            $text .= $variable;
        }

        self::add($text);
    }
}