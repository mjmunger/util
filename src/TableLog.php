<?php

namespace hphio\util;

use \Exception;

/**
 * Represents a tabular log output for the CLI
 *
 * This class is used to display tabular data in the CLI.
 *
 * @package      HPHIO
 * @subpackage   Util
 * @category     Utilities
 * @author       Michael Munger <michael@highpoweredhelp.com>
 */

Class TableLog
{
    var $headers    = NULL;
    var $rows       = [];
    var $columnSize = [];
    var $sortColumn = 0;
    var $tableWidth = 0;
    var $offset     = 0;

    function __construct() {
        //TBD
    }

    function setHeader($headers) {
        $this->headers = $headers;
    }

    function addRow($row) {
        //Make sure that we are submitting a row with adequate / same columns.
        if(count($row) != count($this->headers)) throw new Exception('You rows must have the same number of columns as the header.');

        array_push($this->rows, $row);

        return count($this->rows);
    }

    function sortRows() {
        return array_multisort($this->rows);
    }

    /**
     * Calculates the column size for each column based on the largest input from any row in that column. This is automatically called when you display the log output.
     * Example:
     *
     * @return void
     * @param void
     * @author Michael Munger <michael@highpoweredhelp.com>
     **/
    function calculateColumnSize() {

        //Get the column count
        $columnCount = count($this->headers);
        $columnSize = [];

        foreach($this->headers as $field => $label) {
            $max = 0;
            foreach($this->rows as $row) {
                if(strlen($row[$field]) > $max) $max = strlen($field);
            }
            $columnSize[$field] = $max + 1;
        }

        //Calculate the width
        $width = 0;
        foreach($columnSize as $c) {
            $width += $c;
        }
        $this->tableWidth = $width;
        $this->columnSize = $columnSize;
    }

    /**
     * Shows the table directly in the CLI
     *
     * This is a helper function that first calls TableLog::makeTable(), and
     * then displays it immediately in the CLI.
     * Example:
     *
     * <code>
     * $TL->showTable();
     * </code>
     *
     * @return void
     * @author Michael Munger <michael@highpoweredhelp.com>
     **/

    function showTable() {
        echo $this->makeTable();
    }

    function getTableWidth() {
        $width = 0;
        foreach($this->headers as $field => $label) {
            $width += $this->columnSize[$field] + 2;
        }

        return $width;
    }

    /**
     * Creates the table for the CLI
     * Example:
     *
     * <code>
     * $table = $TL->makeTable();
     * </code>
     *
     * @return string The table ready to display in the CLI.
     * @author Michael Munger <michael@highpoweredhelp.com>
     **/
    function makeTable() {
        $table = [];

        $this->sortRows();

        $this->calculateColumnSize();

        //top of header
        $table[] = str_pad('', $this->getTableWidth(),'-');

        //Column headers
        $buffer = '';
        foreach($this->headers as $field => $label) {
            $buffer .= str_pad($label, $this->columnSize[$field]);
        }
        $table[] = $buffer;

        //Bottom of header.
        $table[] = str_pad('', $this->getTableWidth(),'-');

        //Print all the lines.
        foreach($this->rows as $row) {
            // Add the left offset.
            $buffer = '';
            foreach($this->headers as $field => $label) {
                $value = $row[$field];
                $length = $this->columnSize[$field];
                $buffer .= str_pad($value,$length);
            }
            $table[] = $buffer;
        }

        $table[] = PHP_EOL;

        return implode(PHP_EOL,$table);
    }
}
