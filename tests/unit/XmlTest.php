<?php 
class XmlTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $encodeArray = [
        'bar' => 'value bar',
        'bar2' => [
            'value' => 'value bar2',
        ],
        'foo' => [
            [
                'value' => 'value foo',
            ],
            [
                'value' => 'value foo2',
            ],
        ],
        'der' => [
            '@attributes' => [
                'at1' => 'at1val',
                'at2' => 'at2val',
            ],
            'cdata' => 'this is long text
multiline',
        ],
        'qpo' => [
            '@attributes' => [
                'channel' => '11',
            ],
            'value' => [
                'sub-value' => [
                    [
                        '@attributes' => [
                            'channel' => '11',
                        ],
                        'value'=>'val',
                    ],
                    [
                        '@attributes' => [
                            'channel' => '12',
                        ],
                        'value'=>'val2'
                    ],
                ],
            ],
        ],
        'mlp' => [
            [
                'value' => [
                    'sub1' => [
                        [
                            'value' => [
                                'sub2' => [
                                    [
                                        'value' => 'val'
                                    ]
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    protected $resultArray = [
        'bar' => [
            [
                'value' => 'value bar',
            ]
        ],
        'bar2' => [
            [
                'value' => 'value bar2',
            ]
        ],
        'foo' => [
            [
                'value' => 'value foo',
            ],
            [
                'value' => 'value foo2',
            ],
        ],
        'der' => [
            [
                '@attributes' => [
                    'at1' => 'at1val',
                    'at2' => 'at2val',
                ],
                'value' => 'this is long text
multiline',
            ]
        ],
        'qpo' => [
            [
                '@attributes' => [
                    'channel' => '11',
                ],
                'value' => [
                    'sub-value' => [
                        [
                            '@attributes' => [
                                'channel' => '11',
                            ],
                            'value'=>'val',
                        ],
                        [
                            '@attributes' => [
                                'channel' => '12',
                            ],
                            'value'=>'val2'
                        ],
                    ],
                ],
            ]
        ],
        'mlp' => [
            [
                'value' => [
                    'sub1' => [
                        [
                            'value' => [
                                'sub2' => [
                                    [
                                        'value' => 'val'
                                    ]
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    protected $xml = '<?xml version="1.0" encoding="utf-8"?>
<root><bar>value bar</bar><bar2>value bar2</bar2><foo>value foo</foo><foo>value foo2</foo><der at1="at1val" at2="at2val"><![CDATA[this is long text
multiline]]></der><qpo channel="11"><sub-value channel="11">val</sub-value><sub-value channel="12">val2</sub-value></qpo><mlp><sub1><sub2>val</sub2></sub1></mlp></root>
';
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testEncodeXml()
    {
        $xml = \darkfriend\helpers\Xml::encode($this->encodeArray);

        $this->assertEquals($xml, $this->xml);
    }

    public function testDecodeXml()
    {
        $this->assertTrue(
            \darkfriend\helpers\Xml::decode($this->xml) === $this->resultArray
        );
    }

    public function testEncodeDecodeXml()
    {
        $xml = \darkfriend\helpers\Xml::encode($this->encodeArray);
        $array = \darkfriend\helpers\Xml::decode($xml);

        $this->assertTrue($this->resultArray === $array);
    }
}