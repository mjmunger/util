<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 10/12/18
 * Time: 11:05 AM
 * @author Michael Munger <mj@hph.io>
 */

namespace hphio\util;


class WonderQueryBuilder
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    private $search = [];
    private $fields = [];
    private $table  = null;
    private $sql    = null;

    public $useLimit = false;
    public $limit    = 10;

    public $useOrderBy = false;
    public $order = 'ASC';
    public $orderByField = null;

    public function setLimit($limit) {
        $this->useLimit = true;
        $this->limit = $limit;
    }
    public function getSelectFields() {
        return $this->fields;
    }

    /**
     * Add fields to the SELECT statement. If no fields are added, "*" will be rendered.
     * @param $field
     */
    public function addSelectField($field) {
        array_push($this->fields, $field);
    }

    public function setTable($table) {
        $this->table = $table;
    }

    public function getTable() {
        return $this->table;
    }

    public function setSql($sql) {
        $this->sql = $sql;
    }

    public function getSql() {
        return $this->sql;
    }

    public function addSearchField($field) {
        $this->search[] = $field;
    }

    public function getSearchFields() {
        return $this->search;
    }

    private function backtickFields($element) {
        return "`$element`";
    }

    public function renderFields() {
        if(count($this->fields) === 0) return " * ";
        if(count($this->fields) === 1) return sprintf(" `%s` " , array_shift($this->fields));

        $list = array_map([$this,'backtickFields'], $this->fields);

        $list = implode(", ", $list);
        return sprintf(" %s ", $list);
    }

    private function encloseParens($element) {
        return "( $element )";
    }

    private function buildAnd($field, $terms) {
        $buffer = [];
        foreach($terms as $term ) {
            $buffer[] = sprintf('( `%s` LIKE "%%%s%%" )', $field, $term);
        }

        return implode( " AND ", $buffer);
    }

    public function shouldGroup($buffer) {
        if(count($buffer) <2 ) return false;

        foreach($buffer as $element) {
            if( strstr($element, 'AND') === false) return false;
        }

        return true;
    }

    public function renderWhere($terms) {
        $buffer = [];
        foreach($this->search as $searchField) {
            $buffer[] = $this->buildAnd($searchField, $terms);
        }

        $ands = ($this->shouldGroup($buffer) ? array_map([$this,'encloseParens'],$buffer) : $buffer);

        $statement = implode( " OR ", $ands);

        $statement = ($this->shouldGroup($buffer) ? "( $statement )" : $statement);

        $statement = sprintf( "WHERE %s", $statement);

        return $statement;

    }

    public function setOrderBy($field, $order) {
        $this->useOrderBy = true;
        $this->orderByField = $field;
        $this->order = $order;
    }

    public function addOrderBy($sql) {
        if($this->useOrderBy === false) return $sql;
        $sql[] = sprintf("ORDER BY %s",$this->orderByField);
        $sql[] = $this->order;
        return $sql;
    }

    public function addLimit($sql) {
        if($this->useLimit === false) return $sql;
        $sql[] = "LIMIT";
        $sql[] = $this->limit;
        return $sql;
    }

    public function renderSql($terms) {
        $sql = [];
        $sql[] = "SELECT";
        $sql[] = $this->renderFields();
        $sql[] = "FROM";
        $sql[] = $this->getTable();
        $sql[] = $this->renderWhere($terms);
        $sql   = $this->addOrderBy($sql);
        $sql   = $this->addLimit($sql);

        return implode( " ", $sql);
    }
}