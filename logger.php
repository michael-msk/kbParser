<?php
/**
 * Created by PhpStorm.
 * User: Name
 * Date: 05.01.15
 * Time: 13:22
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

        $text = '-- Start Log -- ' . date(DATE_RFC822) . '--' . PHP_EOL;

        $this->add( $text );
    }

    function __destruct()
    {
        $text = '-- End Log -- ' . date(DATE_RFC822) . '--' . PHP_EOL;

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
}