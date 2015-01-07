<?php
/**
 * @package kbParser
 * @copyright Copyright (c) 2015 Michael V. Scherbinsky
 * @author Michael V. Scherbinsky <michael@kbnet.ru>
 * Created date: 06.01.15 Time: 14:06
 * @license
 */

namespace kbnet\parser;


class logger {

    private $_isDebug;
    private $_fileName;

    function __construct($fileName, $isDebug = false, $append = false)
    {
        if ( empty($fileName) )
        {
            throw new \Exception('The name of the file can\'t be empty.' . PHP_EOL);
        }

        $this->_isDebug = $isDebug;
        $this->_fileName = $fileName;

        if ( $append === false )
        {
            //-- удаляем предыдущий лог
            $this->delete();
        }

        $text = '-- Start Log -- ' . date(DATE_RFC822) . ' --' . PHP_EOL;

        $this->add( $text );
    }

    function __destruct()
    {
        $text = '-- End Log ---- ' . date(DATE_RFC822) . ' --' . PHP_EOL;

        $this->add( $text );
    }

    function delete()
    {
        if ( file_exists($this->_fileName) )
        {
            unlink( $this->_fileName );
        }
    }

    function add( $text )
    {
        if ( file_put_contents($this->_fileName, $text, FILE_APPEND) === false )
        {
            throw new \Exception('Write error to log.' . PHP_EOL);

        }
    }

    function addVariable($variable, $nameVariable = "", $nameFile = "", $row = "")
    {
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

        $this->add($text);
    }
}