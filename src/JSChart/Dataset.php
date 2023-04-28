<?php
/**
 * @namspace hphio\util\JSChart
 * @name Dataset
 * Summary: Manipulate data into a format JSChart can use.
 *
 * This class was designed to help convert database results into arrays that can be used
 * by the [JSChart library](https://www.chartjs.org/docs/latest/charts/line.html).
 * It is not a general purpose data manipulation class.
 *
 * Example:
 *
 * $data = $stmt->fetch(PDO::FETCH_ASSOC);
 * $dataset = new Dataset();
 * $dataset->setData($data);
 * $dataset->setLabelColumn('formatted_date');
 * $dataset->setDataColumn('draft_packages_created');
 * $labels = $dataset->getLabels();
 * $values = $dataset->getValues();
 *
 * Date: 2023-04-27
 * Time: 5:16 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\JSChart;

use Exception;

/**
 * A dataset manipulator for translating data into arrays JSChart can use.
 */
class Dataset
{

    protected ?array $data = null;
    protected ?string $labelColumn = null;
    protected ?string $dataColumn = null;

    /**
     * @param array|null $data
     * @return Dataset
     */
    public function setData(?array $data): Dataset
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param string|null $labelColumn
     * @return Dataset
     * @throws Exception
     */
    public function setLabelColumn(?string $labelColumn): Dataset
    {
        $this->checkDataNotNull();
        $this->dataFormatCorrect();
        $this->verifyKeyExists($labelColumn);
        $this->labelColumn = $labelColumn;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabelColumn(): ?string
    {
        return $this->labelColumn;
    }

    private function checkDataNotNull(): void
    {
        if (is_null($this->data)) throw new Exception('No data has been set in your dataset. Please use setData() to set the data.', 500);
    }

    private function verifyKeyExists(?string $column): void
    {
        foreach($this->data as $row) {
            if (!array_key_exists($column, $row)) throw new Exception("The key you specified (\"$column\") was not found in the dataset. Please use an associative array with the key you specified being a column name from the resulting data.", 500);
        }
    }

    private function dataFormatCorrect(): void
    {
        foreach ($this->data as $row) {
            if (!is_array($row)) throw new \Exception('Data is invalid. Data must be an array of arrays compatible with PDO::FETCH_ASSOC.', 500);
        }
    }

    /**
     * @throws Exception
     */
    public function setDataColumn(string $column): Dataset
    {
        $this->verifyKeyExists($column);
        $this->dataColumn = $column;
        return $this;
    }

    public function getDataColumn(): ?string
    {
        return $this->dataColumn;
    }

    public function getLabels(): array {
        $labels = [];
        foreach($this->data as $row) {
            $labels[] = $row[$this->labelColumn];
        }
        return $labels;
    }

    public function getValues(): array {
        $values = [];
        foreach($this->data as $row) {
            $values[] = $row[$this->dataColumn];
        }
        return $values;
    }
}
