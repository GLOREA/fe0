<?php
require_once dirname(__FILE__) . '/database.php';

class MasterBase {
    static public function read(){
        if(isset(static::$cache)){ return static::$cache; }

        static::$cache = Array();
        foreach(Database::exec('SELECT * FROM `' . static::$table_name . '`;') as $col){
            static::$cache[(int)$col['id']] = $col;
        }
        return static::$cache;
    }

    static function find_or_create($val){
        if($col = static::find_by_key($val)){ return $col; }

        static::insert(Array(static::$find_key => $val));
        foreach(Database::exec('SELECT * FROM `' . static::$table_name . '` WHERE `' . static::$find_key . '` = :' . static::$find_key . ';', Array(':' . static::$find_key => $val)) as $col){
            static::$cache[(int)$col['id']] = $col;
            return $col;
        }
    }

    static function find_by_key($val){
        foreach(static::read() as $col){
            if($col[static::$find_key] === $val){ return $col; }
        }
        return null;
    }

    static function insert($params){
        $cols = Array();
        $symbols = Array();
        $exec_params = Array();
        foreach($params as $key => $val){
            $symbol = ':' . $key;
            $cols[] = '`' . $key . '`';
            $symbols[] = $symbol;
            $exec_params[$symbol] = $val;
        }

        Database::exec(
            'INSERT INTO `' . static::$table_name . '` (' . join(', ', $cols) . ') values (' . join(', ', $symbols) . ');',
            $exec_params
        );

        return Database::last_insert_id();
    }
}
