<?php
/**
 * Generates inclusive AND queries on one or more search terms for one or more table columns.
 * See README for docs.
 *
 * Date: 10/12/18
 * Time: 11:06 AM
 * @author Michael Munger <mj@hph.io>
 */

namespace hphio\util;


use PHPUnit\Framework\TestCase;

class WonderQueryBuilderTest extends TestCase
{

    function testAddSearchField() {
        $field = 'frRNvkrcVWFcN';
        $builder = new WonderQueryBuilder();
        $builder->addSearchField($field);
        $this->assertCount(1,$builder->getSearchFields());
        $this->assertSame($builder->getSearchFields()[0],  $field);
    }

    function testAddSelectField() {
        $field = 'IuvvhpQSgsL';
        $builder = new WonderQueryBuilder();
        $builder->addSelectField($field);
        $this->assertCount(1,$builder->getSelectFields());
        $this->assertSame($builder->getSelectFields()[0],  $field);
    }

    function testSetTable() {
        $table = "VwllNgbgmRhZGCRpPD";
        $builder = new WonderQueryBuilder();
        $builder->setTable($table);
        $this->assertSame($builder->getTable(), $table);
    }


    function testSetSql() {
        $sql = "sErokGamncxT";
        $builder = new WonderQueryBuilder();
        $builder->setSql($sql);
        $this->assertSame($builder->getSql(), $sql);
    }

    /**
     * @param $fields
     * @param $expected
     * @dataProvider providerTestGetFields
     */
    function testGetFields($expectedCount, $fields, $expectedString) {
        $builder = new WonderQueryBuilder();

        foreach($fields as $field) {
            $builder->addSelectField($field);
        }

        $this->assertCount( $expectedCount, $builder->getSelectFields() );

        $this->assertSame( $builder->renderFields(), $expectedString );

    }

    public function providerTestGetFields() {
        return  [ [ 0, [                    ] , " * "             ]
                , [ 1, ['foo'               ] , ' `foo` '           ]
                , [ 2, ['foo', 'bar'        ] , ' `foo`, `bar` '      ]
                , [ 3, ['foo', 'bar', 'baz' ] , ' `foo`, `bar`, `baz` ' ]
                ];
    }

    /**
     * @param $searchFields
     * @param $terms
     * @param $expectedWhere
     * @dataProvider providerTestRenderWhere
     */

    function testRenderWhere($searchFields, $terms, $expectedWhere) {

        $builder = new WonderQueryBuilder();

        foreach($searchFields as $field) {
            $builder->addSearchField($field);
        }

        $where = $builder->renderWhere($terms);

        $this->assertSame($expectedWhere, $where);

    }

    public function providerTestRenderWhere() {

        return  [ // $search                  //$terms   //$expectedWhere
                  [ [ 'foo' ]               , ['bar'                 ] , 'WHERE ( `foo` LIKE "%bar%" )'                                                                                                                                                                                                                                             ]
                , [ [ 'foo' ]               , ['baz', 'bar'          ] , 'WHERE ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%bar%" )'                                                                                                                                                                                                                  ]
                , [ [ 'foo' ]               , ['baz', 'bar', 'boom'  ] , 'WHERE ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%bar%" ) AND ( `foo` LIKE "%boom%" )'                                                                                                                                                                                      ]
                , [ [ 'foo', 'bar' ]        , ['baz'                 ] , 'WHERE ( `foo` LIKE "%baz%" ) OR ( `bar` LIKE "%baz%" )'                                                                                                                                                                                                                   ]
                , [ [ 'foo', 'bar', 'boom' ] ,['baz'                 ] , 'WHERE ( `foo` LIKE "%baz%" ) OR ( `bar` LIKE "%baz%" ) OR ( `boom` LIKE "%baz%" )'                                                                                                                                                                                        ]
                , [ [ 'foo', 'bar'         ] ,['baz', 'boom'         ] , 'WHERE ( ( ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%boom%" ) ) OR ( ( `bar` LIKE "%baz%" ) AND ( `bar` LIKE "%boom%" ) ) )'                                                                                                                                               ]
                , [ [ 'foo', 'bar'         ] ,['baz', 'boom', 'blau' ] , 'WHERE ( ( ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%boom%" ) AND ( `foo` LIKE "%blau%" ) ) OR ( ( `bar` LIKE "%baz%" ) AND ( `bar` LIKE "%boom%" ) AND ( `bar` LIKE "%blau%" ) ) )'                                                                                       ]
                , [ [ 'foo', 'bar', 'baz'  ] ,['boom', 'blau', 'pow' ] , 'WHERE ( ( ( `foo` LIKE "%boom%" ) AND ( `foo` LIKE "%blau%" ) AND ( `foo` LIKE "%pow%" ) ) OR ( ( `bar` LIKE "%boom%" ) AND ( `bar` LIKE "%blau%" ) AND ( `bar` LIKE "%pow%" ) ) OR ( ( `baz` LIKE "%boom%" ) AND ( `baz` LIKE "%blau%" ) AND ( `baz` LIKE "%pow%" ) ) )' ]
                ];
    }


    public function testUseLimit() {
        $builder = new WonderQueryBuilder();
        $builder->setLimit(100);

        $this->assertTrue($builder->useLimit);
        $this->assertSame(100, $builder->limit);
    }

    public function testSetOrderBy() {
        $builder = new WonderQueryBuilder();
        $builder->setOrderBy('foo', WonderQueryBuilder::ORDER_ASC);

        $this->assertTrue($builder->useOrderBy);
        $this->assertSame(WonderQueryBuilder::ORDER_ASC, $builder->order);
        $this->assertSame('foo', $builder->orderByField);

        $builder->setOrderBy('foo', WonderQueryBuilder::ORDER_DESC);
        $this->assertSame(WonderQueryBuilder::ORDER_DESC, $builder->order);

    }

    /**
     * @param $fields
     * @param $expectedFields
     * @dataProvider providerTestRenderFields
     */

    public function testRenderFields($fields, $expectedFields) {
        $builder = new WonderQueryBuilder();

        foreach($fields as $field) {
            $builder->addSelectField($field);
        }

        $this->assertSame($expectedFields, $builder->renderFields());

    }

    public function providerTestRenderFields() {
        return  [ [ []                       , ' * '           ]
                , [ ['foo'         ]         , ' `foo` '       ]
                , [ ['foo'  , 'bar']         , ' `foo`, `bar` ']
                , [ ['foo'  , 'bar', 'baz' ] , ' `foo`, `bar`, `baz` ']
                ];
        return $return;
    }

    /**
     * @param $searchFields
     * @param $table
     * @param $terms
     * @param $expectedSql
     * @dataProvider providerRenderSql
     */

    public function testRenderSql($select, $searchFields, $terms, $expectedSql) {
        $builder = new WonderQueryBuilder();

        $builder->setTable('footable');

        foreach($select as $field) {
            $builder->addSelectField($field);
        }

        foreach($searchFields as $field) {
            $builder->addSearchField($field);
        }

        $this->assertSame($expectedSql, $builder->renderSql($terms));

    }

    public function providerRenderSql() {

        return  [ //$select                                                                                                         $search                  //$terms   //$expectedWhere
                  [ []                                                                                                              , [ 'foo' ]               , ['bar'                 ] , 'SELECT  *  FROM footable WHERE ( `foo` LIKE "%bar%" )'                                                                                                                                                                                                                    ]
                , [ ['xssR']                                                                                                        , [ 'foo' ]               , ['bar'                 ] , 'SELECT  `xssR`  FROM footable WHERE ( `foo` LIKE "%bar%" )'                                                                                                                                                                                                                    ]
                , [ []                                                                                                              , [ 'foo' ]               , ['baz', 'bar'          ] , 'SELECT  *  FROM footable WHERE ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%bar%" )'                                                                                                                                                                                                                  ]
                , [ ['ydueRFeEbxSWu', 'BfOnc']                                                                                      , [ 'foo' ]               , ['baz', 'bar', 'boom'  ] , 'SELECT  `ydueRFeEbxSWu`, `BfOnc`  FROM footable WHERE ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%bar%" ) AND ( `foo` LIKE "%boom%" )'                                                                                                                                                                                      ]
                , [ []                                                                                                              , [ 'foo', 'bar' ]        , ['baz'                 ] , 'SELECT  *  FROM footable WHERE ( `foo` LIKE "%baz%" ) OR ( `bar` LIKE "%baz%" )'                                                                                                                                                                                                                   ]
                , [ ['PgIGXBIHG', 'vxpaOhREZLFNwYhhdu', 'EsFrTNClck']                                                               , [ 'foo', 'bar', 'boom' ] ,['baz'                 ] , 'SELECT  `PgIGXBIHG`, `vxpaOhREZLFNwYhhdu`, `EsFrTNClck`  FROM footable WHERE ( `foo` LIKE "%baz%" ) OR ( `bar` LIKE "%baz%" ) OR ( `boom` LIKE "%baz%" )'                                                                                                                                                                                        ]
                , [ []                                                                                                              , [ 'foo', 'bar'         ] ,['baz', 'boom'         ] , 'SELECT  *  FROM footable WHERE ( ( ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%boom%" ) ) OR ( ( `bar` LIKE "%baz%" ) AND ( `bar` LIKE "%boom%" ) ) )'                                                                                                                                               ]
                , [ ['DVdzzJZSqTouy', 'xjMLICgLCD', 'LTiaaWx', 'CabsNiSeJqlFvYGtGQTt', 'mZJLGOdaJJaHXBhVV', 'MUsrYGezvSADnk']       , [ 'foo', 'bar'         ] ,['baz', 'boom', 'blau' ] , 'SELECT  `DVdzzJZSqTouy`, `xjMLICgLCD`, `LTiaaWx`, `CabsNiSeJqlFvYGtGQTt`, `mZJLGOdaJJaHXBhVV`, `MUsrYGezvSADnk`  FROM footable WHERE ( ( ( `foo` LIKE "%baz%" ) AND ( `foo` LIKE "%boom%" ) AND ( `foo` LIKE "%blau%" ) ) OR ( ( `bar` LIKE "%baz%" ) AND ( `bar` LIKE "%boom%" ) AND ( `bar` LIKE "%blau%" ) ) )'                                                                                       ]
                , [ []                                                                                                              , [ 'foo', 'bar', 'baz'  ] ,['boom', 'blau', 'pow' ] , 'SELECT  *  FROM footable WHERE ( ( ( `foo` LIKE "%boom%" ) AND ( `foo` LIKE "%blau%" ) AND ( `foo` LIKE "%pow%" ) ) OR ( ( `bar` LIKE "%boom%" ) AND ( `bar` LIKE "%blau%" ) AND ( `bar` LIKE "%pow%" ) ) OR ( ( `baz` LIKE "%boom%" ) AND ( `baz` LIKE "%blau%" ) AND ( `baz` LIKE "%pow%" ) ) )' ]
                ];
    }

    /**
     * @param $orderByField
     * @param $useOrderBy
     * @param $order
     * @param $expectedResults
     * @dataProvider providerAddOrderBy
     */
    public function testAddOrderBy($orderByField, $useOrderBy, $order, $expectedResults) {
        $builder = new WonderQueryBuilder();
        $builder->setOrderBy($orderByField, $order);
        $builder->useOrderBy = $useOrderBy;

        $this->assertSame($expectedResults, $builder->addOrderBy([]));
    }

    public function providerAddOrderBy() {
        return  [ [ 'orderbyfield', false , 'ASC' , []                               ]
                , [ 'orderbyfield', true  , 'ASC' , ['ORDER BY orderbyfield' , 'ASC'] ]
                , [ 'orderbyfield', true  , 'DESC', ['ORDER BY orderbyfield', 'DESC'] ]
                ];
    }

    /**
     * @param $useLimit
     * @param $limit
     * @param $expectedResults
     * @dataProvider providerAddLimit
     */

    public function testAddLimit($useLimit, $limit, $expectedResults) {
        $builder = new WonderQueryBuilder();
        $builder->setLimit($limit);
        $builder->useLimit = $useLimit;

        $this->assertSame($expectedResults, $builder->addLimit([]));
    }

    public function providerAddLimit() {
        return  [ [ false, 10  , [] ]
                , [ true , 10  , ['LIMIT', 10  ] ]
                , [ true , 100 , ['LIMIT', 100 ] ]
                , [ true , 84  , ['LIMIT', 84  ] ]
                ];
    }
}
