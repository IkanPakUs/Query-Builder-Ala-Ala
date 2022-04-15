<?php

class Model {

    public static $table, $select_column, $where_clause = [], $conn;

    public static function table($table) {
        self::$table = $table;

        $servername = "db";
        $username = "root";
        $password = "pemweb";
        $dbname = "WebApp";

        $conn = new mysqli($servername, $username, $password, $dbname);
        self::$conn = $conn;

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 

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

    public static function get() {
        $conn = self::$conn;
        $table = self::$table;
        $select_column = isset(self::$select_column) ? implode(", ", self::$select_column) : "*";
        $where_clause = self::$where_clause;

        $sql = "SELECT " . $select_column . " FROM " . $table;

        if (count($where_clause) > 0) {
            $sql .= self::setupWhereClause();
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
        // output data of each row
            while($row = $result->fetch_assoc()) {
                var_dump($row);
            }
        } else {
            echo "0 results";
        }
        $conn->close();
    }

    public static function insert($column_add) {
        $conn = self::$conn;
        $table = self::$table;

        
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
        // var_dump($sql);
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    public static function update($column_update) {
        $conn = self::$conn;
        $table = self::$table;

        $columns = array_map( function($key, $value)  {
            return $key .= " = '" . $value . "'";
        } , array_keys($column_update), $column_update);

        $columns = implode(", ", $columns);

        $where_clause = self::setupWhereClause();

        $sql = "UPDATE " . $table . " SET " . $columns . $where_clause;

        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    public static function delete() {
        $conn = self::$conn;
        $table = self::$table;
        $where_clause = self::setupWhereClause();

        $sql = "DELETE FROM " . $table . $where_clause;

        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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

}