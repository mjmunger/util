# util
Utility classes for doing various tasks.

# WonderQueryBuilder

Example use. Let's say you wanted to find the user id of all the users that had a substring of "com" and "nel"
in their email address. You need to produce this query:
````
SELECT
    users_id
FROM
    users
WHERE
    (users_email LIKE '%com%'
        AND users_email LIKE '%nel%')
        OR ((users_email LIKE '%com%'
        AND users_email LIKE '%nel%'))
````

You would create it this way:
````
$builder = new WonderQueryBuilder();
$builder->setsetTable('users');
$builder->addSelectField('users_id');
$builder->addSearchField('users_email');
$sql = $builder->renderSQL([ 'com', 'nel']);
````

It supports multiple fields and multiple search terms.

## Complex example

Suppose I want to return results where any of the following fields
contain all of the search terms I want, and only return certain fields:

````
$builder = new WonderQueryBuilder();
$builder->setsetTable('footable');
$builder->addSelectField('field1');
$builder->addSelectField('field2');
$builder->addSelectField('field3');
$builder->addSelectField('field4');
$builder->addSearchField('foo');
$builder->addSearchField('bar');
$sql = $builder->renderSql(['baz', 'boom', 'blau']);

```` 
Will produce the following query:

````
SELECT 
    `field1`,
    `field2`,
    `field3`,
    `field4`,
FROM
    footable
WHERE
    (
        (
            (`foo` LIKE '%baz%') AND (`foo` LIKE '%boom%') AND (`foo` LIKE '%blau%')
        ) OR (
            (`bar` LIKE '%baz%') AND (`bar` LIKE '%boom%') AND (`bar` LIKE '%blau%')
        )
    )
````