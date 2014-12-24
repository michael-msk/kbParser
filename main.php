<?php
/**
 * Created by PhpStorm.
 * User: Name
 * Date: 20.12.14
 * Time: 11:59
 */

namespace kbnet\parser;

require_once('htmlPage.php');
require_once('mycurl.php');
require_once('container.php');

class main {

    private $_arConfig;

    private $_obPage;
    private $_obContainer;

    function __construct($arConfig)
    {
        $this->_arConfig = $arConfig;

        $this->_obPage = new htmlPage($this->_arConfig['START_PAGE']);
    }

    /**
     * @return \kbnet\parser\htmlPage
     */
    public function getPage()
    {
        return $this->_obPage;
    }

    /**
     * @param mixed $arConfig
     */
    public function setConfig($arConfig)
    {
        $this->_arConfig = $arConfig;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->_arConfig;
    }


    public function openContainer($name, $path)
    {
        $this->_obContainer = new container($name, $path);
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->_obContainer;
    }



    function test()
    {
        echo '<pre>'.'Work! 2 ('.__CLASS__.')</pre>';
    }
}