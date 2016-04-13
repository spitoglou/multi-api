<?php
use Spitoglou\MultiApi\XmlArray;

class MultiApiTest extends PHPUnit_Framework_TestCase
{

    /** @test */
    public function itRunsPlaceholderTest()
    {
        $this->assertEquals(1, 1);
    }

    /** @test */
    public function itCanConvertArrayToXml()
    {
        $array = [
            [
                "alpha" => 1,
                "beta" => 2
            ],
            [
                "alpha" => 3,
                "beta" => 4
            ]
        ];
        $array2XML = new XmlArray($array);
        $result = $array2XML->createXmlFromArray('results');
        $this->assertEquals(
            <<<'TAG'
<?xml version="1.0" encoding="utf-8"?>
<results><result><alpha>1</alpha><beta>2</beta></result><result><alpha>3</alpha><beta>4</beta></result></results>

TAG
            ,
            $result
        );
    }

}