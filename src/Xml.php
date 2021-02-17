<?php

namespace darkfriend\helpers;

/**
 * Class Xml
 * @package darkfriend\php7-xml
 * @author darkfriend <hi@darkfriend.ru>
 * @version 2.0.0
 */
class Xml
{
    protected static $root = '<root/>';

    /**
     * Convert array to xml
     * @param mixed $data
     * @param array $params = [
     *     'root' => '<root/>',
     *     'exception' => false,
     *     'header' => true,
     *     'prolog' => [],
     * ]
     * @return string
     * @throws XmlException
     */
    public static function encode($data, $params = [])
    {
        \libxml_use_internal_errors(true);

        if(empty($params['root'])) {
            $params['root'] = self::$root;
        }
        if(!isset($params['header'])) {
            $params['header'] = true;
        }
        if(!isset($params['prolog'])) {
            $params['prolog'] = [];
        }

        $xml = new SimpleXMLElement(
            ($params['header'] ? self::getProlog($params['prolog']) : '')
            . $params['root']
        );
        $xml = self::generateXml($xml, $data, $params);

        if(!static::checkException($params)) {
            return '';
        }

        return $xml->asXML();
    }

    /**
     * @param array $params prolog attributes and elements
     * @return string
     * @since 2.0.0
     */
    public static function getProlog($params = [])
    {
        $attributes = \array_merge(
            [
                'version' => '1.0',
                'encoding' => 'utf-8',
            ],
            $params['attributes'] ?? []
        );

        $attr = [];
        foreach ($attributes as $attrKey=>$attrVal) {
            $attr[] = "$attrKey=\"$attrVal\"";
        }

        $prolog = '<?xml '.\implode(' ', $attr).'?>';

        if(!empty($params['elements'])) {
            foreach ($params['elements'] as $element) {
                $prolog .= $element;
            }
        }

        return $prolog;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param mixed $data
     * @param array $params
     * @return SimpleXMLElement
     */
    public static function generateXml($xml, $data, $params = [])
    {
        /** @var $xml SimpleXMLElement */
        if(\is_array($data)) {
            foreach ($data as $key => $item) {
                self::addChild($xml, $key, $item, $params);
            }
        } else {
            self::addChild($xml, $data, '', $params);
        }
        return $xml;
    }

    /**
     * Add child
     * @param SimpleXMLElement $xml
     * @param string $name
     * @param array|string $params
     * @param array $options
     * @return SimpleXMLElement
     */
    public static function addChild($xml, $name, $params = '', array $options = [])
    {
        if(\is_array($params)) {
            if(!empty($params[0])) {
                foreach ($params as $key => $item) {
                    self::addChild($xml, $name, $item);
                }
                return $xml;
            }

            $value = null;
            if(\array_key_exists('value',$params) && !\is_array($params['value'])) {
                if(\strpos($params['value'], \PHP_EOL) === false) {
                    $value = $params['value'];
                    unset($params['value']);
                } else {
                    $params['cdata'] = $params['value'];
                    unset($params['value']);
                }
            }

            $namespace = null;
            if(\array_key_exists('namespace',$params)) {
                $namespace = $params['namespace'];
                unset($params['namespace']);
            }

            $child = $xml->addChild($name, $value, $namespace);

            if(\array_key_exists('@attributes',$params)) {
                foreach ($params['@attributes'] as $keyAttr=>$attr) {
                    $child->addAttribute($keyAttr, $attr);
                }
                unset($params['@attributes']);
            }

            if(\array_key_exists('cdata',$params)) {
                $child->addCData(\str_replace('  ', '', $params['cdata']));
                unset($params['cdata']);
            }

            if(!empty($params['value']) && \is_array($params['value'])) {
                foreach ($params['value'] as $key => $item) {
                    if(\is_array($item)) {
                            self::addChild($child, $key, $item);
//                        foreach ($item as $tag => $tagItem) {
//                            self::addChild($child,$tag,$tagItem);
//                        }
                    } else {
                        $child->addChild($key,$item);
                    }
                }
            }
        } else {
            $child = $xml->addChild($name, $params);
        }

        if(!empty($options['afterChild'])) {
            $child = \call_user_func($options['afterChild'], $xml, $child, $name, $params);
        }

        return $child;
    }

    /**
     * Decode XML string
     * @param string $data
     * @param array $params = [
     *     'convert' => true,
     *     'exception' => false,
     * ]
     * @return \darkfriend\helpers\SimpleXMLElement|array
     * @throws XmlException
     */
    public static function decode($data, $params = [])
    {
        \libxml_use_internal_errors(true);

        $xml = \simplexml_load_string(
            $data,
            '\darkfriend\helpers\SimpleXMLElement',
            \LIBXML_NOCDATA
        );

        if(!static::checkException($params)) {
            return [];
        }

        if(!isset($params['convert'])) {
            $params['convert'] = true;
        }

        if($params['convert']) {
            return self::convertSimpleXml($xml);
        } else {
            return $xml;
        }
    }

    /**
     * Convert tree SimpleXMLElement
     * @param SimpleXMLElement $xml
     * @return array
     */
    public static function convertSimpleXml($xml)
    {
        $res = [];
        /** @var SimpleXMLElement $item */
        foreach ($xml as $key=>$item) {
            $rowItem = self::convertSimpleXmlItem($item);
            if($item->count()>0) {
                /** @var SimpleXMLElement $childItem */
                foreach ($item->children() as $childrenKey => $childItem) {
                    $subRowItem = self::convertSimpleXmlItem($childItem);
                    $children = $childItem->children();
                    if(!empty($children) && \is_array($children)) {
                        $subRowItem['value'] = self::convertSimpleXml($children);
                    }
                    $rowItem['value'][$childrenKey][] = $subRowItem;
                }
            }
            $res[$key][] = $rowItem;
        }
        return $res;
    }

    /**
     * Convert item SimpleXMLElement
     * @param SimpleXMLElement $item
     * @return array
     */
    public static function convertSimpleXmlItem($item)
    {
        /** @var SimpleXMLElement $item */
        $attr = $item->attributes();

        if(!empty($attr)) {
            $element = (array) $attr;
            $children = $item->children();
            if(!empty($children) && empty(@\current($children))) {
                $element['value'] = self::getXmlItemValue($item);
            }
        } else {
            $element = [
                'value' => self::getXmlItemValue($item),
            ];
        }
        return $element;
    }

    /**
     * @param SimpleXMLElement|string $item
     * @return string
     * @since 2.0.0
     */
    public static function getXmlItemValue($item)
    {
        return \trim((string) $item);
    }

    /**
     * Check error
     * @param array $params = [
     *     'exception' => true,
     * ]
     * @return bool if $params['exception'] === false
     * @throws XmlException if $params['exception'] === true
     */
    public static function checkException($params = [])
    {
        $e = \libxml_get_errors();

        if(!$e) {
            return true;
        }

        $strError = '';
        foreach($e as $key => $xmlError) {
            $strError .= "$key:".$xmlError->message . "\n";
        }

        if($params['exception']) {
            throw new XmlException("XML error: $strError", 100, __FILE__, __LINE__);
        }

        return true;
    }

    /**
     * Set root element
     * @param string $root
     */
    public static function setRootElement($root = '<root/>')
    {
        self::$root = $root;
    }

    /**
     * Get root element
     * @return string
     */
    public static function getRootElement()
    {
        return self::$root;
    }
}