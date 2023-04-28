<?php
/**
 * @namspace tests\JSChart
 * @name DatasetTest
 * Summary: #$END$#
 *
 * Date: 2023-04-27
 * Time: 5:22 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\JSChart;

use Exception;
use hphio\util\JSChart\Dataset;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DatasetTest extends TestCase
{

    public function testGetData()
    {
        $data = $this->loadDataset();
        $dataset = new Dataset();
        $dataset->setData($data);
        $this->assertSame($data, $dataset->getData());
    }

    public function testSetData()
    {
        $data = $this->loadDataset();
        $dataset = new Dataset();
        $dataset->setData($data);

        $reflectionClass = new ReflectionClass(Dataset::class);
        $reflectionProperty = $reflectionClass->getProperty('data');
        $reflectionProperty->setAccessible(true);
        $this->assertSame($data, $reflectionProperty->getValue($dataset));

    }

    private function loadDataset()
    {
        $path = dirname(__FILE__) . '/fixtures/DatasetTest.json';
        $this->assertFileExists($path);
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    /**
     * @return void
     * @dataProvider providerSetLabelColumnExceptions
     */
    public function testSetLabelColumnExceptions($data, $column, $message, $code)
    {
        $dataset = new Dataset();
        $dataset->setData($data);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode($code);
        $dataset->setLabelColumn($column);

    }

    public function testSetLabelColumn()
    {
        $dataset = new Dataset();
        $data = $this->loadDataset();
        $dataset->setData($data);
        $dataset->setLabelColumn('formatted_date');
    }

    public function testGetLabelColumn()
    {
        $dataset = new Dataset();
        $data = $this->loadDataset();
        $dataset->setData($data);
        $dataset->setLabelColumn('formatted_date');
        $this->assertSame('formatted_date', $dataset->getLabelColumn());
    }

    public function providerSetLabelColumnExceptions(): array
    {
        return [
            $this->noData(),
            $this->dataFormatInCorrect(),
            $this->keyNotFound()
        ];
    }

    private function noData(): array
    {
        $data = null;
        $message = 'No data has been set in your dataset. Please use setData() to set the data.';
        $code = 500;
        $column = 'test';
        return [$data, $column, $message, $code];
    }

    private function dataFormatInCorrect(): array
    {
        $data = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10
        ];
        $column = 'test';
        $message = 'Data is invalid. Data must be an array of arrays compatible with PDO::FETCH_ASSOC.';
//        $message = 'The key you specified ("test") was not found in the dataset. Please use an associative array with the key you specified being a column name from the resulting data.';
        $code = 500;
        return [$data, $column, $message, $code];
    }

    private function keyNotFound(): array
    {
        $data = [
            [
                'testx' => 123456
            ]
        ];
        $column = 'test';
        $message = 'The key you specified ("test") was not found in the dataset. Please use an associative array with the key you specified being a column name from the resulting data.';
        $code = 500;
        return [$data, $column, $message, $code];
    }

    /**
     * @throws Exception
     * @depends testSetData
     */
    public function testSetDataColumn()
    {
        $dataset = new Dataset();
        $data = $this->loadDataset();
        $dataset->setData($data);
        $dataset->setDataColumn('draft_packages_created');
        $this->assertSame('draft_packages_created', $dataset->getDataColumn());
    }

    /**
     * @return void
     * @depends testSetDataColumn
     * @depends testSetLabelColumn
     * @depends testSetData
     */
    public function testGetLabels() {
        $path = dirname(__FILE__) . '/fixtures/testGetLabels.json';
        $this->assertFileExists($path);
        $json = file_get_contents($path);
        $expectedJson = json_decode($json, true);
        $expectedData = $expectedJson['data'];

        $dataset = new Dataset();
        $data = $this->loadDataset();
        $dataset->setData($data);
        $dataset->setLabelColumn('formatted_date');
        $dataset->setDataColumn('draft_packages_created');
        $this->assertSame($expectedData, $dataset->getLabels());
    }
    public function testGetValues() {
        $path = dirname(__FILE__) . '/fixtures/testGetValues.json';
        $this->assertFileExists($path);
        $json = file_get_contents($path);
        $expectedJson = json_decode($json, true);
        $expectedData = $expectedJson['data'];

        $dataset = new Dataset();
        $data = $this->loadDataset();
        $dataset->setData($data);
        $dataset->setLabelColumn('formatted_date');
        $dataset->setDataColumn('draft_packages_created');
        $this->assertSame($expectedData, $dataset->getValues());
    }
}
