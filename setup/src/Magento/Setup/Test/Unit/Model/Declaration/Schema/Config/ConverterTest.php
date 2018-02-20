<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Test\Unit\Model\Declaration\Schema\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Setup\Model\Declaration\Schema\Config\Converter;

/**
 * Test for Converter class.
 *
 * @package Magento\Setup\Test\Unit\Model\Declaration\Schema\Config.
 */
class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->converter = $this->objectManager->getObject(
            Converter::class
        );
    }

    /**
     * Test converting table schema to array.
     */
    public function testConvert()
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            '<?xml version="1.0"?>
            <schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                xsi:noNamespaceSchemaLocation="urn:magento:setup:Model/Declaration/Schema/etc/schema.xsd">
                <table name="test_table" resource="default">
                    <column xsi:type="int" name="id" nullable="false" identity="true" comment="Id"/>
                    <column xsi:type="varchar" name="data" length="100" identity="false" comment="Data"/>
                    <constraint xsi:type="primary" name="PRIMARY">
                        <column name="id"/>
                    </constraint>
                </table>
            </schema>'
        );
        $result = $this->converter->convert($dom);
        $this->assertEquals(
            [
                'table' => [
                    'test_table' => [
                        'column' => [
                            'id' => [
                                'type' => 'int',
                                'name' => 'id',
                                'nullable' => 'false',
                                'identity' => 'true',
                                'comment' => 'Id',
                            ],
                            'data' => [
                                'type' => 'varchar',
                                'name' => 'data',
                                'length' => '100',
                                'identity' => 'false',
                                'comment' => 'Data',
                            ],
                        ],
                        'constraint' => [
                            'PRIMARY' => [
                                'column' => [
                                    'id' => 'id',
                                ],
                                'type' => 'primary',
                                'name' => 'PRIMARY',
                            ],
                        ],
                        'name' => 'test_table',
                        'resource' => 'default',
                    ],
                ],
            ],
            $result
        );
    }
}