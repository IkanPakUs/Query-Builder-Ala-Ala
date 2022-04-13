<?php

class Model {

    public static $db, $select_column, $where_clause = [];

    public static function db($db) {
        self::$db = $db;

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
        $db = self::$db;
        $select_column = isset(self::$select_column) ? implode(" ", self::$select_column) : "*";
        $where_clause = self::$where_clause;

        $sql = "SELECT " . $select_column . " FROM " . $db;

        if (count($where_clause) > 0) {
            $sql .= self::setupWhereClause();
        }

        var_dump($sql);
    }

    public static function insert($column_add) {
        $db = self::$db;

        $column = implode(", ", array_keys($column_add));
        $value = implode(", ", $column_add);

        $sql = "INSERT INTO " . $db . " ( " . $column . " ) VALUES ( " . $value . " )";

        var_dump($sql);
    }

    public static function update($column_update) {
        $db = self::$db;

        $columns = array_map( function($key, $value)  {
            return $key .= " = " . $value;
        } , array_keys($column_update), $column_update);

        $columns = implode(", ", $columns);

        $where_clause = self::setupWhereClause();

        $sql = "UPDATE " . $db . " SET " . $columns . $where_clause;

        var_dump($sql);
    }

    public static function delete() {
        $db = self::$db;
        $where_clause = self::setupWhereClause();

        $sql = "DELETE FROM " . $db . $where_clause;

        var_dump($sql);
    }

    protected static function setupWhereClause() {
        $where_clause = self::$where_clause;

        $where_clause = array_map( function($key, $value) {
            $sql = "";
            if ($key != 0) {
                $sql .= " " . $value["type"] . " ";
            }

            $string_clause = implode(" ", $value["clause"]);
            $sql .= " " . $string_clause;

            return $sql;
            
        } , array_keys($where_clause), $where_clause);
        
        $sql = " WHERE " . implode(" ", $where_clause);

        return $sql;
    }

}