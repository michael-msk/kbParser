<?php
/**
 * @package kbParser
 * @copyright Copyright (c) 2015 Michael V. Scherbinsky
 * @author Michael V. Scherbinsky <michael@kbnet.ru>
 * Created date: 20.12.14 Time: 12:17
 * @license
 */

namespace kbnet\parser;


class htmlPage {

    private $_url;
    private $_dom;
    private $_xpath;

    function __construct($url = "", $domVersion = '1.0', $encoding = 'UTF-8')
    {
        $this->_dom = new \DomDocument($domVersion, $encoding);

        if (!empty($url))
        {
            $this->_url = $url;
            $this->loadHtml();
            $this->_xpath = new \DomXPath( $this->_dom );
        }

    }

    /**
     * @throws \Exception
     */
    private function loadHtml()
    {
        if (mb_substr($this->_url, 0, 7) == 'http://')
        {
            $curl = new mycurl( $this->_url );
            $curl->createCurl();

            $httpStatus = $curl->getHttpStatus();

            if ($httpStatus == '200' )
            {
                if (!$this->_dom->loadHTML( $curl ))
                {
                    throw new \Exception('mistake when loading $dom->loadHTML('.$this->_url.')');
                }

            } else {
                throw new \Exception('Error open page - '.$httpStatus['http_code'].' createCurl('.$this->_url.')');
            }


        } elseif (file_exists( $this->_url ))
        {
            if (!$this->_dom->loadHTMLFile( $this->_url ))
            {
                throw new \Exception('mistake when loading $dom->loadHTMLFile('.$this->_url.')');
            }
        } else {
            throw new \Exception('File not found ('.$this->_url.')');
        }
    }

    public function xPath( $arConfig )
    {
        $res = $this->_xpath->query( $arConfig['ITEMS']['XPATH_LIST'] );

        $count = $res->length;

        $arResult = array();

        for ($i = 1; $i <= $count; $i++)
        {
            $arRes = $this->getProperty($arConfig['ITEMS'], $i);

            if (!empty($arConfig['DETAIL_ITEMS']))
            {
                $detailUrl = $this->getDetailUrl($arConfig['DETAIL_ITEMS'], $i);

                $obDetailPage = new htmlPage($detailUrl);

                // $xpathDetail = new \DomXPath( $this->loadHtml($_SERVER['DOCUMENT_ROOT'].'/test/items.htm') );
                // $xpathDetail = new \DomXPath( $this->loadHtml($detailUrl) );

                $arResDetail = $obDetailPage->getProperty($arConfig['DETAIL_ITEMS']);

                if (!empty($arResDetail))
                {
                    $arRes += $arResDetail;
                }
            }

            $arResult[] = $arRes;
        }

        return $arResult;
    }

    private function getProperty($arConfigItems, $n = 1)
    {
        $arResult = array();

        if (!empty($arConfigItems['PROPERTIES']))
        {
            foreach ($arConfigItems['PROPERTIES'] as $propertyCode => $arProperty)
            {
                $xPathQuery = str_replace("%COUNT%", $n, $arProperty['XPATH_PROPERTY']);

                $res = $this->_xpath->query( $xPathQuery );
                $arResult[$propertyCode] = trim( $res->item(0)->nodeValue );
            }
        }

        if (!empty($arConfigItems['LIST_PROPERTIES']))
        {
            $res = $this->_xpath->query( $arConfigItems['LIST_PROPERTIES']['XPATH_LIST'] );
            $count = $res->length;

            // echo "{$arConfigItems['LIST_PROPERTIES']['XPATH_LIST']} | n = $count <br/>";

            $xPathQueryName = $arConfigItems['LIST_PROPERTIES']['XPATH_PROPERTY_NAME'];
            $xPathQueryValue = $arConfigItems['LIST_PROPERTIES']['XPATH_PROPERTY_VALUE'];

            $resName = $this->_xpath->query( $xPathQueryName );
            $resValue = $this->_xpath->query( $xPathQueryValue );

            for ($i = 0; $i < $count; $i++)
            {
                if (!empty( $resName->item($i)->nodeValue ) )
                {
                    $arResult[trim( utf8_decode($resName->item($i)->nodeValue) )] = trim( utf8_decode($resValue->item($i)->nodeValue) );
                }
            }
        }

        return $arResult;
    }

    function getDetailUrl($arConfigItems, $n = 1)
    {
        if (!empty($arConfigItems["XPATH_URL"]))
        {
            $xPathQuery = str_replace("%COUNT%", $n, $arConfigItems["XPATH_URL"]);

            $res = $this->_xpath->query($xPathQuery);

            $detailUrl = $res->item(0)->nodeValue;

            if (!empty($detailUrl))
            {
                return $detailUrl;
            } else {
                throw new \Exception('$detailUrl is EMPTY');
            }

            // return $_SERVER['DOCUMENT_ROOT'].'/test/items.htm';

        } else {
            throw new \Exception('$arConfigItems["XPATH_URL"] is EMPTY');
        }
    }

    /**
     * @return mixed
     */
    public function getXpath()
    {
        return $this->_xpath;
    }

    /**
     * @return \DomDocument
     */
    public function getDom()
    {
        return $this->_dom;
    }



    function test()
    {
        echo '<pre>'.'Work! ('.__CLASS__.')</pre>';
    }
}