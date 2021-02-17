# PHP7 library for encode/decode xml

``composer require darkfriend/php7-xml``

* Array to XML (``` XML::encode() ```)
* XML to Array (``` XML::decode() ```)

### Other php version
* [Xml encode/decode for php5](https://github.com/darkfriend/php5-xml)

## How to install

```
composer require darkfriend/php7-xml
```

## Array Structure

* elementName - basic example
    * @attributes
        * `key => value` (array)
    * cdata - multiline text
    * value - text or tree elements
* elementName - example element with attachments
    * @attributes
        * `key => value` (array)
    * value
        * elementName
            * @attributes
                * `key => value` (array)
            * value - text or tree elements
        * elementName
            * @attributes
                * `key => value` (array)
            * value - text or tree elements
* elementName - example element with duplicate attachments
    * @attributes
        * `key => value` (array)
    * value
        * elementName - duplicate element (array)
            * 0
                * @attributes
                    * `key => value` (array)
                * value - text or tree elements
            * 1
                * @attributes
                    * `key => value` (array)
                * value - text or tree elements
* elementName - example element duplicate
    * 0
        * @attributes
            * `key => value` (array)
        * value - text or tree elements
    * 1
        * @attributes
            * `key => value` (array)
        * value - text or tree elements 

### Encode Events

* afterChild - execute function when added child element

## How to use

### Array to XML (encode)

```php
$array = [
    'bar' => 'value bar',
    'foo' => 'value foo',
    'der' => [
        '@attributes' => [
            'at1' => 'at1val',
            'at2' => 'at2val',
        ],
        'cdata' => 'this is long text',
    ],
    'qpo' => [
        '@attributes' => [
            'test1' => 'valTest',
        ],
        'value' => [
            'sub1' => [
                'value' => [
                    'sub2'=>'val'
                ]
            ],
            'multi-sub2' => [
                [
                    '@attributes' => [
                        'at1' => 'val'
                    ],
                    'value' => [
                        'multi-sub2-sub3' => [
                            'value' => 'value multi-sub2'
                        ]
                    ]
                ],
                [
                    'value' => 'value multi-sub2'
                ],
            ],
        ],
    ],
    'duplicateElement' => [
        [
            '@attributes' => [
                'atDuplicate1' => 'val'
            ],
            'value' => 'valueDuplicateElement1',
        ],
        [
            '@attributes' => [
                'atDuplicate2' => 'val'
            ],
            'value' => 'valueDuplicateElement2',
        ],
        [
            '@attributes' => [
                'atDuplicate3' => 'val'
            ],
            'value' => [
                'subElement' => 'val'
            ],
        ],
    ]
];

echo \darkfriend\helpers\Xml::encode($array);

// example with use events
\darkfriend\helpers\Xml::encode($array, [
    'afterChild' => function($xml, $child, $name, $params) {
        // your code
        return $child;
    },
]);
```

#### Result encode

```xml
<?xml version="1.0" encoding="utf-8"?>
<root>
    <bar>value bar</bar>
    <foo>value foo</foo>
    <der at1="at1val" at2="at2val">
        <![CDATA[ this is long text ]]>
    </der>
    <qpo test1="valTest">
        <sub1>
            <sub2>val</sub2>
        </sub1>
        <multi-sub2 at1="val">
            <multi-sub2-sub3>value multi-sub2</multi-sub2-sub3>
        </multi-sub2>
        <multi-sub2>value multi-sub2</multi-sub2>
    </qpo>
    <duplicateElement atDuplicate1="val">valueDuplicateElement1</duplicateElement>
    <duplicateElement atDuplicate2="val">valueDuplicateElement2</duplicateElement>
    <duplicateElement atDuplicate3="val">
        <subElement>val</subElement>
    </duplicateElement>
</root>
```

### Xml string to Array (decode)

```php
$xml = '<?xml version="1.0" encoding="utf-8"?>
<root>
    <bar>value bar</bar>
    <foo>value foo</foo>
    <der at1="at1val" at2="at2val">
        <![CDATA[ this is long text ]]>
    </der>
    <qpo test1="valTest">
        <sub1>
            <sub2>val</sub2>
        </sub1>
        <multi-sub2 at1="val">
            <multi-sub2-sub3>value multi-sub2</multi-sub2-sub3>
        </multi-sub2>
        <multi-sub2>value multi-sub2</multi-sub2>
    </qpo>
    <duplicateElement atDuplicate1="val">valueDuplicateElement1</duplicateElement>
    <duplicateElement atDuplicate2="val">valueDuplicateElement2</duplicateElement>
    <duplicateElement atDuplicate3="val">
        <subElement>val</subElement>
    </duplicateElement>
</root>';

var_dump(\darkfriend\helpers\Xml::decode($xml));
```

#### Result decode

```
array(5) {
  ["bar"]=>
  array(1) {
    [0]=>
    array(1) {
      ["value"]=>
      string(9) "value bar"
    }
  }
  ["foo"]=>
  array(1) {
    [0]=>
    array(1) {
      ["value"]=>
      string(9) "value foo"
    }
  }
  ["der"]=>
  array(1) {
    [0]=>
    array(2) {
      ["@attributes"]=>
      array(2) {
        ["at1"]=>
        string(6) "at1val"
        ["at2"]=>
        string(6) "at2val"
      }
      ["value"]=>
      string(17) "this is long text"
    }
  }
  ["qpo"]=>
  array(1) {
    [0]=>
    array(2) {
      ["@attributes"]=>
      array(1) {
        ["test1"]=>
        string(7) "valTest"
      }
      ["value"]=>
      array(2) {
        ["sub1"]=>
        array(1) {
          [0]=>
          array(1) {
            ["value"]=>
            string(0) ""
          }
        }
        ["multi-sub2"]=>
        array(2) {
          [0]=>
          array(1) {
            ["@attributes"]=>
            array(1) {
              ["at1"]=>
              string(3) "val"
            }
          }
          [1]=>
          array(1) {
            ["value"]=>
            string(16) "value multi-sub2"
          }
        }
      }
    }
  }
  ["duplicateElement"]=>
  array(3) {
    [0]=>
    array(2) {
      ["@attributes"]=>
      array(1) {
        ["atDuplicate1"]=>
        string(3) "val"
      }
      ["value"]=>
      string(22) "valueDuplicateElement1"
    }
    [1]=>
    array(2) {
      ["@attributes"]=>
      array(1) {
        ["atDuplicate2"]=>
        string(3) "val"
      }
      ["value"]=>
      string(22) "valueDuplicateElement2"
    }
    [2]=>
    array(2) {
      ["@attributes"]=>
      array(1) {
        ["atDuplicate3"]=>
        string(3) "val"
      }
      ["value"]=>
      array(1) {
        ["subElement"]=>
        array(1) {
          [0]=>
          array(1) {
            ["value"]=>
            string(3) "val"
          }
        }
      }
    }
  }
}
```

### Custom \<?xml \?> attributes and custom \<root\>

```php
$array = [
    'bar' => 'value bar',
    'foo' => 'value foo',
    'der' => [
        '@attributes' => [
            'at1' => 'at1val',
            'at2' => 'at2val',
        ],
        'value' => 'this is long text',
    ],
];

echo \darkfriend\helpers\Xml::encode(
    $array,
    [
        'root' => '<main atExample="atValue"/>', // custom root element with custom attribute atExample
        'prolog' => [
            'attributes' => [
                'version' => '1.0',
                'encoding' => 'utf-8',
            ],
            'elements' => [ // additional elements for prolog
                /*'<?xml-stylesheet type="text/css" href="/style/design"?>'*/
                '<!-- This is a comment --> '
            ], 
        ],
    ]
);
```

```xml
<?xml version="1.0" encoding="utf-8"?>
<!--  This is a comment  -->
<main atExample="atValue">
    <bar>value bar</bar>
    <foo>value foo</foo>
    <der at1="at1val" at2="at2val">this is long text</der>
</main>
```

### Custom root element

```php
$array = [
    'bar' => 'value bar',
    'foo' => 'value foo',
    'der' => [
        'cdata' => 'this is long text',
        '@attributes' => [
            'at1' => 'at1val',
            'at2' => 'at2val',
        ],
    ]
];

echo \darkfriend\helpers\Xml::encode(
    $array,
    [
        'root' => '<response/>',
    ]
);
```

```xml
<?xml version="1.0" encoding="utf-8"?>
<response>
    <bar>value bar</bar>
    <foo>value foo</foo>
    <der at1="at1val" at2="at2val"><![CDATA[this is long text]]></der>
</response>
```