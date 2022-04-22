<?php

class Model {

    public static $table, $select_column, $where_clause = [], $limit, $conn, $dbname;

    public static function table($table) {
        self::$table = $table;
        self::dbConnect();

        return new static();
    }

    public static function select() {
        $num_args = func_num_args();

        if ($num_args > 0) {
            $select_column = func_get_args();
            self::$select_column = $select_column;
        }

        return new static();
    }

    public static function where($column, $operator, $comparison) {
        $where_statemen = [ "type" => "AND", "clause" => [$column, $operator, $comparison]];
        self::$where_clause[] = $where_statemen;

        return new static();
    }

    public static function orWhere($column, $operator, $comparison) {
        $where_statemen = [ "type" => "OR", "clause" => [$column, $operator, $comparison]];
        self::$where_clause[] = $where_statemen;

        return new static();
    }

    public static function limit($count) {
        self::$limit = $count;

        return new static();
    }

    public static function find($id) {
        self::limit(1);
        self::where('id', '=', $id);

        $result = self::queryGet();

        if ($result) {
            $result = $result[0];
        }

        return $result;
    }

    public static function get() {
        return self::queryGet();
    }

    public static function insert($column_add) {
        $conn = self::$conn;
        $table = self::$table;

        self::queryValidator();
        
        if (count($column_add) == count($column_add, COUNT_RECURSIVE)) {
            $column = implode(", ", array_keys($column_add));

            $value = implode(", ", array_map( function($column) {
                return "'" . $column . "'";
            }, $column_add));

            $value = " ( " . $value . " )";
        }  else {
            $column = implode(", ", array_keys($column_add[0]));

            $value = array_map( function($row) {
                $value_string = implode(", ", array_map( function($column) {
                    return "'" . $column . "'";
                }, $row));

                return " ( " . $value_string . " )";
            }, $column_add);

            $value = implode(", ", $value);
        }

        $sql = "INSERT INTO " . $table . " ( " . $column . " ) VALUES" . $value;

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    public static function update($column_update) {
        $conn = self::$conn;
        $table = self::$table;

        self::queryValidator();

        $columns = array_map( function($key, $value)  {
            return $key .= " = '" . $value . "'";
        } , array_keys($column_update), $column_update);

        $columns = implode(", ", $columns);

        $where_clause = self::setupWhereClause();

        $sql = "UPDATE " . $table . " SET " . $columns . $where_clause;

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    public static function delete() {
        $conn = self::$conn;
        $table = self::$table;
        $where_clause = self::setupWhereClause();

        self::queryValidator();

        $sql = "DELETE FROM " . $table . $where_clause;

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    protected static function queryGet() {
        $conn = self::$conn;
        $table = self::$table;
        $select_column = isset(self::$select_column) ? implode(", ", self::$select_column) : "*";
        $where_clause = self::$where_clause;
        $limit = self::$limit;

        self::queryValidator();

        $sql = "SELECT " . $select_column . " FROM " . $table;

        if (count($where_clause) > 0) {
            $sql .= self::setupWhereClause();
        }

        if (isset($limit)) {
            $sql .= " LIMIT " . $limit;
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return false;
    }

    protected static function setupWhereClause() {
        $where_clause = self::$where_clause;

        $where_clause = array_map( function($key, $value) {
            $sql = "";
            if ($key != 0) {
                $sql .= " " . $value["type"] . " ";
            }
            
            $value["clause"][2] = "'" . $value["clause"][2] . "'";

            $string_clause = implode(" ", $value["clause"]);
            $sql .= " " . $string_clause;

            return $sql;
            
        } , array_keys($where_clause), $where_clause);
        
        $sql = " WHERE " . implode(" ", $where_clause);

        return $sql;
    }

    protected static function queryValidator() {        
        $tables = self::getAllTable();

        try {
            $tables_arr = array_map( function ($table) {
                return $table[0]; 
            }, array_values($tables));
    
            if (!isset(self::$table) || !in_array(self::$table, $tables_arr)) {
                echo "No table selected <br>";
            }
            
        } catch (\Throwable $th) {
            echo $th;
        }

    }

    protected static function getAllTable() {
        $conn = self::$conn;
        $dbname = self::$dbname;

        $sql = "SHOW TABLES FROM " . $dbname;
        if (!isset($conn)) {
            self::dbConnect();
        }

        $tables = $conn->query($sql);

        if ($tables->num_rows > 0) {
            return $tables->fetch_all();
        } else {
            throw new Exception("Table is not available in this database");
        }
    }

    protected static function dbConnect() {
        $servername = "db";
        $username = "root";
        $password = "pemweb";
        self::$dbname = $dbname = "WebApp";

        $conn = new mysqli($servername, $username, $password, $dbname);
        self::$conn = $conn;

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

    }

}
