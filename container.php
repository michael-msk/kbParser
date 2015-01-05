<?php
/**
 * Created by PhpStorm.
 * User: Michael V. Schrebinsky
 * Mail: 9448800@gmail.com
 * Site: www.kbnet.ru
 * Date: 24.12.14
 * Time: 1:47
 */

namespace kbnet\parser;


class container {

    private $_name;
    private $_path;

    private $_readOnly = false;
    private $_isNull = false;

   private $_isNewContainer = false;

    private $_arVariables = array();

    function __construct($name, $path = '')
    {
        $this->_path = $path;

        if ($this->isCorrectName($name))
        {
            $this->_name = $name;

            if (!$this->open())
            {
                if ($this->create() === true)
                {
                    $this->_isNewContainer = true;
                } else {
                    throw new \Exception('Error of creation of the container - "'.$name.'".' . PHP_EOL);
                }
            }
        } else {
            throw new \Exception('Inadmissible name of the container - "'.$name.'".' . PHP_EOL);
        }
    }

    function __destruct()
    {
        $this->close();
    }

    private function open()
    {
        if ($this->isLock())
        {
            $this->_readOnly = true;
        }

        $this->setLock();

        if ($this->readVariables())
        {
            return true;
        }

        return false;
    }

    private function create()
    {
        if ( $this->setLock() )
        {
            if ( $this->saveVariables() )
            {
                return true;
            }
        }

        return false;
    }

    public function close()
    {
        if ($this->_isNull)
        {
            return true;
        }

        if ($this->_readOnly)
        {
            return true;
        }

        if ( $this->saveVariables() )
        {
            if ($this->unLock())
            {
                return true;
            }
        }

        return false;
    }

    public function delete()
    {
        $filename = $this->_path . '/' . $this->_name;

        if (file_exists($filename))
        {
            if ( unlink($filename) )
            {
                if ( $this->unLock() )
                {
                    $this->_isNull = true;
                }
            }
        }

        return false;
    }

    private function isLock()
    {
        $filename = $this->_path . '/~' . $this->_name;

        if (file_exists($filename))
        {
            return true;
        }

        return false;
    }

    private function setLock()
    {
        $filename = $this->_path  . '/~' . $this->_name;

        //-- устанавливаем дату блокировки
        $currentTime = array("date" => date("Y-m-d H:i:s"));
        $file = json_encode($currentTime);
        if ( $file !== false )
        {
            if ( file_put_contents($filename, $file) !== false )
            {
                return true;
            } else {
                throw new \Exception('Write error - "'.$filename.'".'. PHP_EOL);
            }
        }

        throw new \Exception('Error of installation of blocking of the container - "'.$filename.'".'. PHP_EOL);
    }

    private function unLock()
    {
        $filename = $this->_path  . '/~' . $this->_name;

        if ( file_exists($filename) )
        {
            if ( !unlink($filename) )
            {
                throw new \Exception('Error of removal of blocking of the container - "'.$filename.'".'. PHP_EOL);
            }
        }

        return true;
    }

    private function readVariables()
    {
        $filename = $this->_path  . '/' . $this->_name;
        if ( file_exists($filename) )
        {
            $file = file_get_contents($filename);

            $this->_arVariables = json_decode($file, true);

            return true;
        }

        return false;
    }

    private function saveVariables()
    {
        $filename = $this->_path  . '/' . $this->_name;
        if ( file_exists($filename) )
        {
            if (!unlink($filename))
            {
                throw new \Exception('Error of removal of the file - "'.$filename.'".'. PHP_EOL);
            }
        }

        $file = json_encode($this->_arVariables);
         if ( $file !== false )
         {
             $current = file_put_contents($filename, $file);
             if ( $current === false )
             {
                 throw new \Exception('Write error file - "'.$filename.'".'. PHP_EOL);
             } else {
                 return true;
             }
         } else {
             throw new \Exception('Error json_encode - $this->_arVariables'. PHP_EOL);
         }

        // return false;
    }

    private function isCorrectName($name)
    {
        //-- добавить дополнительные проверки !!!
        if (!empty($name))
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $arVariables
     */
    public function setVariables($arVariables)
    {
        $this->_arVariables = $arVariables;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->_arVariables;
    }

    /**
     * @return boolean
     */
    public function getIsNewContainer()
    {
        return $this->_isNewContainer;
    }

}