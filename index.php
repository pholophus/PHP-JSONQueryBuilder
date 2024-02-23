<!DOCTYPE html>
<html>

<body>

<?php
    // Provided JSON data
    $jsonData = '{
   "name": "products",
   "description": "Features product list",
   "vendor":{
      "name": "Computer Source BD",
      "email": "info@example.com",
      "website":"www.example.com"
   },
   "users":[
      {"id":1, "name":"Johura Akter Sumi", "location": "Barisal"},
      {"id":2, "name":"Mehedi Hasan Nahid", "location": "Barisal"},
      {"id":3, "name":"Ariful Islam", "location": "Barisal"},
      {"id":4, "name":"Suhel Ahmed", "location": "Sylhet"},
      {"id":5, "name":"Firoz Serniabat", "location": "Gournodi"},
      {"id":6, "name":"Musa Jewel", "location": "Barisal", "visits": [
         {"name": "Sylhet", "year": 2011},
         {"name": "Coxs Bazar", "year": 2012},
         {"name": "Bandarbar", "year": 2014}
      ]}
   ],
   "products": [
      {"id":1, "user_id": 2, "city": "bsl", "name":"iPhone", "cat":1, "price": 80000},
      {"id":2, "user_id": 2, "city": null, "name":"macbook pro", "cat": 2, "price": 150000},
      {"id":3, "user_id": 2, "city": "dhk", "name":"Redmi 3S Prime", "cat": 1, "price": 12000},
      {"id":4, "user_id": 1, "city": null, "name":"Redmi 4X", "cat":1, "price": 15000},
      {"id":5, "user_id": 1, "city": "bsl", "name":"macbook air", "cat": 2, "price": 110000},
      {"id":6, "user_id": 2, "city": null, "name":"macbook air 1", "cat": 2, "price": 81000}
   ]
}';

    $data = json_decode($jsonData, true);

    class QueryBuilder
    {
        private $data;
        private $table;
        private $whereConditions = [];

        public function __construct($data)
        {
            $this->data = $data;
        }

        public function from($table)
        {
            $this->table = $table;
            return $this;
        }

        public function where($column, $operator, $value)
        {
            $this->whereConditions[] = [
                'column' => $column,
                'operator' => $operator,
                'value' => $value,
            ];

            return $this;
        }

        public function whereNested($nestedColumn, $column, $operator, $value)
        {
            $this->whereConditions[] = [
                'nested_column' => $nestedColumn,
                'column' => $column,
                'operator' => $operator,
                'value' => $value,
            ];

            return $this;
        }

        public function getRecursive($data, $conditions)
        {
            $results = [];

            foreach ($data as $item) {
                $matchAllConditions = true;

                foreach ($conditions as $condition) {
                    if (isset($condition['nested_column'])) {
                        // echo("mula2 sini 1\n");
                        // If it's a nested condition, call getRecursive recursively
                        $nestedData = $item[$condition['nested_column']] ?? [];
                        $nestedConditions = [
                            [
                                'column' => $condition['column'],
                                'operator' => $condition['operator'],
                                'value' => $condition['value'],
                            ],
                        ];
                        $nestedMatch = $this->getRecursive($nestedData, $nestedConditions);

                        if (!$nestedMatch) {
                            $matchAllConditions = false;
                            break;
                        }
                    } else {
                        // echo("pastu sini 2\n");
                        // Regular condition for the current level
                        $column = $condition['column'];
                        $operator = $condition['operator'];
                        $value = $condition['value'];

                        if (
                            !isset($item[$column])
                            || ($operator === '=' && $item[$column] != $value)
                            || ($operator === '!=' && $item[$column] == $value)
                        ) {
                            $matchAllConditions = false;
                            break;
                        }
                    }
                }

                if ($matchAllConditions) {
                    $results[] = $item;
                }
            }

            return $results;
        }

        public function get()
        {
            return $this->getRecursive($this->data[$this->table], $this->whereConditions);
        }

    public function insertData($table, $key, $value)
    {
        // Check if the specified table exists in the data
        if (!isset($this->data[$table])) {
            throw new Exception("Table '$table' does not exist in the data.");
        }

        $this->data[$table] = $this->recursiveInsert($this->data[$table], $key, $value);

        // Save the changes if needed
        // $this->saveChanges();

        return $this;
    }

    private function recursiveInsert($data, $key, $value)
    {
        foreach ($data as &$item) {
            if (is_array($item)) {
                $item = $this->recursiveInsert($item, $key, $value);
            }
        }

        // Add the new key-value pair to the current level
        $data[$key] = $value;

        return $data;
    }
    }

    // Example usage for nested query
    $q = new QueryBuilder($data);

    // $nestedRes = $q->from('users')
    //     ->whereNested('visits', 'name', '=', 'Sylhet')
    //     ->get();

    // var_dump($nestedRes);

    // Example usage for get and update
    // $res = $q->from('users')
    //     ->where('location', '=', 'Barisal')
    //     ->get();

    // var_dump($res);

    // $updated = $q->from('products')
    //     ->where('cat', '=', 2)
    //     ->update(['city' => 'new_city', 'price' => 200000]);

    // var_dump($updated);

    // $allProducts = $q->from('products')
    //     ->get();

    // var_dump($allProducts);
    
    // Inserting a key-value pair inside the "vendor" section
    $newData = $q->insertData('vendor', 'new_key', 'new_value');
    
    // Print the updated data
    var_dump($newData);

    ?>


</body>

</html>