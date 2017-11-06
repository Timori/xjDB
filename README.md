# xjDB - Flat File Database Script

### About
xjDB is a php script which can create and manipulate xml-"databases". It intends to be an alternative for databases such as MySQL, PostgreSQL or Sqlite. You can add, remove and update rows and columns. One XML File is the equvalent to a table in a Database. So each XML-File respresents a Database Table.
### Recommendations
- PHP > 5.6
- Write and Read-permissions to handle files
- PHP Extension: [libxml](http://pl1.php.net/manual/en/book.libxml.php)
---
### Basic Usage
##### Using the Script:
```php
<?php
require "path/to/file/xjDB.php"
$fileDb = new \Timori\xjDB('file.xml');

// You can now use the script
?>
```
If you want to add the file to a specific folder, you can use the constructor like this:
```php
$fileDb = new \Timori\xjDB('file.xml', 'src/databases');
```
*Note: You must provide no trailing slash for the folder.*
##### Insert Data (SQL Equvalent: INSERT INTO):
You can insert data into the file very easely. You need an associative array, where the attribute name holds the value. Every entry is plain string in the xml file.
*Note: Rows can have different "columns" or attributes per row*
```php
// ... require etc.
$fileDb->insert(array(
  "id" => 1,
  "name" => "Thomas",
  "email" => "thomas@domain.td"
));
```
##### Getting Data (SQL Equivalent: SELECT):
You can get one or many results. You can set up multiple "WHERE" filters by using an associative array. 

- ##### Single Row:
    The following example shows, how you can get one row. It will always return the first result, if there are more than one. This is very useful if you have IDs for example, where every entry is unique.
    ```php
    // ... require, insert etc.
    $attribute = "name";
    $value = "Thomas";
    $row = $fileDb->row($attribute, $value);
    
    echo $row->name;    //output: Thomas
    echo $row->id;      //Output: 1
    echo $row->email;   //Output: thomas@domain.td
    ```
- ##### Multiple Rows
    The following example shows, how you can get more rows, based on given criteria. Only those rows matching all criteria will be in the result.
    *Note: Currently, you can only filter for whole strings. Features like "LIKE" are planned.*
    ```php
    // ... require, insert etc.
    $fileDb->insert(array(
      "id" => 2,
      "name" => "Mary",
      "email" => "mary.maksman@domain.td"
    ));
    
    $fileDb->insert(array(
      "id" => 3,
      "name" => "Mary",
      "email" => "mary.thomsen@domain.td"
    ));
    
    $allRows = $fileDb->rows();
    $matchRows = $fileDb->rows(array(
        'name' => 'Mary',
    ));
    ```
    - print_r of $allRows:
        ```
        Array
        (
            [0] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [id] => 1
                            [name] => Thomas
                            [email] => thomas@domain.td
                        )
                )
            [1] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [id] => 2
                            [name] => Mary
                            [email] => mary.maksman@domain.td
                        )
                )
        
            [2] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [id] => 3
                            [name] => Mary
                            [email] => mary.thomsen@domain.td
                        )
                )
        )
        ```
    - print_r of $matchRows
        ```
        Array
        (
            [0] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [id] => 2
                            [name] => Mary
                            [email] => mary@domain.td
                        )
                )
        
            [1] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [id] => 3
                            [name] => Mary
                            [email] => mary@domain.td
                        )
                )
        )
        ```
- ##### Looping through results
    If you have multiple results, you can loop through them by using foreach for example.
    ```php
    // ... require, insert etc.
    $allRows = $fileDb->rows();
    foreach($allRows as $row)
    {
        $attributes = $row->attributes();
        echo "Id: ".$attributes->id.
          ", Name: ".$attributes->name.
          ", E-Mail: ".$attributes->email.
          "<br />";
    }
    
    //Output:
    //Id: 1, Name: Thomas, E-Mail: thomas@domain.td
    //Id: 2, Name: Mary, E-Mail: mary.maksman@domain.td
    //Id: 3, Name: Mary, E-Mail: mary.thomsen@domain.td
    ```
##### Update Values (SQL Equivalent: UPDATE):
You can update the wanted value with a new one by providing the where criteria, attribute and value. It is recommended to use this on unique entrys like ids or equivalents. The where criteria have the same form as the rows method.
```php
// ... require, insert etc.
$fileDb->update(array(
  'id' => 3,
), "name", "Mary Thomsen");
```
##### Delete Data (SQL Equivalent: DELETE)
You can also delete data, either by attribute and value or you can clear the whole file to the basic XML-Structure.
- ##### Delete all Entrys:
    This function deletes all rows.
    *Note: There is no confirmation! Use this function carefully.*
    ```php
    // ... require, insert, update etc.
    $fileDb->clearAll();
    ```
- ##### Delete Entrys by attribute and value:
    Deletes all rows, matching the given attribute and value.
    ```php
    // ... require, insert, update etc.
    $fileDb->clear("name", "Mary");
    ```
---
### API Documentation
You can find the API Documentation here:
TODO: Server structure changed. Docs link will appear soon.
### License
This Script uses the BSD 3-Clause License.
