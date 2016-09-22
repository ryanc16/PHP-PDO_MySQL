# PDO_MySQL PHP class
Author: Ryan Conklin  
Date: 9/22/2016  
<<<<<<< HEAD
Email: [rwc4@pct.edu](mailto:rwc4@pct.edu)  
=======
<<<<<<< HEAD
Email: [rwc4@pct.edu](mailto:rwc4@pct.edu)  
=======
Email: [mailto:rwc4@pct.edu](rwc4@pct.edu)  
>>>>>>> origin/master
>>>>>>> origin/master
Repository: [https://github.com/ryanc16/PHP-PDO_MySQL](https://github.com/ryanc16/PHP-PDO_MySQL)  
Original filename: pdo_mysql_class.php  
Version: 1.0

## Description
This is a PDO based, easy to use class that aims to be a nearly drop-in replacement for mysqli_* functions that allows for a more secure interaction with a MySQL database in a website or web application that runs PHP. This will allow anyone to develop applications that utilize a MySQL database to have access to prepared statements and the accompanying functionality that is needed in order to build their application with a security-oriented approach.  
**Note:** This is an object-oriented implementation of this library and its contained functions.

## Contents
###### Public Methods
- [**`change_database`**](#change_database)  
- [**`change_host`**](#change_host)  
- [**`change_user`**](#change_user)  
- [**`connect`**](#connect)  
- [**`create_connection`**](#create_connection)  
- [**`fetch_row`**](#fetch_row)  
- [**`field_names`**](#field_names)  
- [**`has_rows`**](#has_rows)  
- [**`is_connected`**](#is_connected)  
- [**`num_rows`**](#num_rows)  
- [**`pdo_self`**](#pdo_self)  
- [**`query`**](#query)  
- [**`query_prepared`**](#query_prepared)  
- [**`reconnect`**](#reconnect)  
- [**`set_testing`**](#set_testing)  
- [**`set_err_mode`**](#set_err_mode)  
- [**`set_err_mode_default`**](#set_err_mode_default)  

###### Private Methods
- [**`handle_exception`**](#handle_exception)  

## Methods
___
### Public Methods
___
### `change_database`
**Sets the database name to the new value and disconnects the current session.**
```php
bool function change_database(string $database)
```
#### Parameters
`$database` - name of the new database.
#### Return Values
`bool` - Returns true if successful and false if unsuccessful.

___
### `change_host`
**Sets the host to a new value and disconnects the current session.**
```php
bool function change_host(string $hostname)
```
#### Parameters
`$hostname` - url/address of database server.
#### Return Values
`bool` - Returns true if successful and false if unsuccessful.

___
### `change_user`
**Sets the username and password to new values and disconnects the current session.**
```php
bool function change_user(string $username, string $password)
```
#### Description
Disconnects the current user from the database connection and sets the username and password to new values that will be used to create a new connection.
#### Parameters
`$username` - username of database user.  
`$password` - password for the user.
#### Return Values
`bool` - Returns true if successful and false if unsuccessful.

___
### `connect`
**Used for creating a new connection to a MySQL database.**
```php
bool function connect(string $host, string $user, string $pass, string $database)
```
#### Parameters
`$host` - url/address of database server.  
`$user` - username of database user.  
`$pass` - password for the user.  
`$database` - name of the database.  
#### Return Values
`bool` - Returns true if connection is successful and false if it is not.

___
### `create_connection`
**This is a static constructor that can be used for a single line instantiation and connection.**
```php
PDO_MySQL static function create_connection(string $host, string $user, string $pass, string $database)
```
#### Description
A static constructor that internally calls the `connect` method. Can be used for quickly creating a new PDO_MySQL object with an active database connection.
#### Parameters
`$host` - url/address of database server.  
`$user` - username of database user.  
`$pass` - password for the user.  
`$database` - name of the database.  
#### Return Values
`PDO_MySQL` - Returns a new PDO_MySQL object with an active connection.
#### Examples
```php
$pdo = PDO_MySQL::create_connection("localhost","username","password","db");
```
___
### `fetch_row`
**Used to return one record from a result set**
```php
object[] function fetch_row(object[,] &$result_set)
```
#### Parameters
`$result_set` - a multi-dimensional array result set returned by either query_prepared() or query() functions passed by reference.
#### Return Values
`object[]` - Returns the next row in the result set and removes it from the set. If no rows are left in the result set, it returns false.
#### Examples
Can be used in a while loop to iterate over all rows until result set is empty.
```php
while($row = $db->fetch_row($result)){
//do work in here util all records in the result set have been extracted.
}
```
___
### `field_names`
**Allows for determining the field names (column names) in the database from which the values were pulled from.**
```php
string[] function field_names(object[,] $result_set)
```
#### Parameters
`$result_set` - a multi-dimensional array result set returned by either query_prepared() or query() functions.
#### Return Values
`string[]` - Returns a string array of the field names present in the result set.

___
### `has_rows`
**Determines if any rows were returned in a result set**
```php
bool function has_rows(object[,] $result_set)
```
#### Description
Uses a result set generated by either `query_prepared()` or `query()` functions to indicate whether any results were retuned by the query.
#### Paramaters
`$result_set` - a multi-dimensional array result set returned by either `query_prepared()` or `query()` functions.
#### Return Values
`bool` - Returns true if the result set contained at least 1 row, otherwise returns false.

___
### `is_connected`
**Checks to see if there is a current connection to a MySQL database.**
```php
bool function is_connected( void )
```
#### Return Values
`bool` - Returns a boolean which determines whether there is a current connection to a database

___
### `num_rows`
**Determines how many rows were returned in a result set.**
```php
int function num_rows(object[,] $result_set)
```
#### Description
Uses a result set generated by either `query_prepared()` or `query()` functions to indicate the numbers of results that were returned by the query.
#### Parameters
`$result_set` - a multi-dimensional array result set returned by either `query_prepared()` or `query()` functions.
#### Return Values
`int` - Returns the number of rows contained in the result set.

___
### `pdo_self`
**Returns the PDO class itself for if/when more methods and functionality is desired.**
```php
PDO function pdo_self( void )
```
#### Return Values
`PDO` - The actual internal PHP PDO class.

___
### `query`
**Uses current connection to query an unprepared SQL statment.**
```php
object[,] function query(string $sql [, string $array_type])
```
#### Description
This is used to query the database using the current connection without providing any variables or using any ? in the sql statement.  
**NOTICE THIS CANNOT UTILIZE PREPARED STATEMENTS.**
#### Parameters
`$sql` - the sql string to be prepared.  
`$array_type` - the type of keys to be used in the returned result set. Accepts `"NUM"`, `"ASSOC"` or `"BOTH"`. If no value is given, the keys will default to `"ASSOC"`.
#### Return Values
`object[,]` - Returns a multi-dimensional array as a result set to be used with other functions that use result sets.

___
### `query_prepared`
**Uses current connection to prepare a SQL query, then queries the database.**
```php
object[,] function query_prepared(string $sql, array() $valuesIN [, string $array_type])
```
#### Description
This is used to automatically prepare a SQL statement, bind values to to the ? in the prepared statement, and then query the database using the current connection.
#### Parameters
`$sql` - the sql string to be prepared and queried.  
`$valuesIN` - an array containing values to be binded to in place of the ? in the prepared statement.  
`$array_type` - the type of keys to be used in the returned result set. Accepts `"NUM"`, `"ASSOC"` or `"BOTH"`. If no value is given, the keys will default to `"ASSOC"`.
#### Return Values
`object[,]` - Returns a multi-dimensional array as a result set to be used with other functions that use result sets.

___
### `reconnect`
**Attempts to reconnect using current connection settings.**
```php
bool function reconnect( void )
```
#### Description
Creates a new PDO object and attempts a connection using the current values of HOSTNAME, USERNAME, PASSWORD, and DATABASE. This is useful for reconnecting after using change_host(), change_user(), or
change_database(). Disconnects current connection if already connected.
#### Return Values
`bool` - Returns true if connection is successful and false if it is not.
#### Examples  
```php
$db = new PDO_MySQL();
$admin          = "admin";       //a user with all privileges
$regular_user   = "regular_user";//a user with only select privileges
$db->connect("localhost",$admin,"adminpass","hr_db");
//do some privileged database queries here
//then later...
$db->change_user($regular_user,"userspass");
if($db->reconnect()){
    //ensures that database access at this point is limited by this user's privileges
}
```
___
### `set_testing`
**Sets the internal testing variable to true or false.**
```php
void function set_testing(bool $istesting)
```
#### Description
Use this for setting wether this is being used in a testing environment or a live environment.
If testing is set to true, warning and error messages will be shown in the browser.
Otherwise, when set to false, as would be desired of a live environment, no warning or error messages will be shown.
#### Parameters
`$istesting` - true for testing or false for live environment.

___
### `set_err_mode`
**Manually overrides the the error mode to one of the predefined error modes.**
```php
bool function set_err_mode(int $mode)
```
```php
bool function set_err_mode(string $mode)
```
#### Description
**NOTE:** Will do this regardless if testing has been set to true or false for testing or live environment. This means the user will have to try/catch their own exceptions! If a database connection is already present, it will set the connection to the new mode immediately. Otherwise it will set it upon first successful connection.
#### Parameters
`$mode` - an int or string representation of the error mode to be used from the $ERR_MODES array.  
Valid values are `"SILENT"` or `0`, `"WARNING"` or `1`, `"EXCEPTION"` or `2`.
#### Return Values  

`bool` - Returns true if a valid mode is used and throws exception if previous error mode is not silent.

___
### `set_err_mode_default`
**Resumes normal error mode determination, displaying, and exception throwing.**
```php
void function set_err_mode_default( void )
```
___

### Private Methods
___
### `handle_exception`
**Handles exceptions based on the `ERR_MODE`.**
```php
void function handle_exception(Exception $e)
```
#### Description
Used internally to handle exceptions when they are thrown. Behaves differently according to `ERR_MODE`.