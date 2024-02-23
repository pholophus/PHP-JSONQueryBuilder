# QueryBuilder Class Readme

The `QueryBuilder` class is designed to provide a flexible and dynamic way to query and manipulate JSON data. It allows you to perform various operations such as selecting, updating, and inserting data into nested structures. Below are examples demonstrating the usage of the `QueryBuilder` class.

## Getting Data

To retrieve data from a specified table with specific conditions, you can use the `from` and `where` methods:

```php
$q = new QueryBuilder($data);

// Example: Get users from Barisal
$res = $q->from('users')
    ->where('location', '=', 'Barisal')
    ->get();

var_dump($res);
```

## Nested Queries

The `whereNested` method allows you to perform nested queries, useful for searching within nested arrays:

```php
$q = new QueryBuilder($data);

// Example: Get users who visited Sylhet
$nestedRes = $q->from('users')
    ->whereNested('visits', 'name', '=', 'Sylhet')
    ->get();

var_dump($nestedRes);
```

## Updating Data

To update data in a table based on certain conditions, you can use the `where` method along with the `update` method:

```php
$q = new QueryBuilder($data);

// Example: Update products with category 2
$updated = $q->from('products')
    ->where('cat', '=', 2)
    ->update(['city' => 'new_city', 'price' => 200000]);

var_dump($updated);
```

## Inserting Data

To insert new key-value pairs into a specified table, you can use the `insertData` method:

```php
$q = new QueryBuilder($data);

// Example: Insert a new key-value pair into the "vendor" section
$newData = $q->insertData('vendor', 'new_key', 'new_value');

// Print the updated data
var_dump($newData);
```

Please note that the `update` and `insertData` methods currently modify the data in-memory and do not persist the changes. If needed, you can extend the class to include functionality for saving changes to the original data source.

Feel free to customize and expand upon this `QueryBuilder` class based on your specific use case and requirements.