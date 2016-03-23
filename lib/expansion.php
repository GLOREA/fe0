<?php
require_once dirname(__FILE__) . '/master_base.php';
require_once dirname(__FILE__) . '/expansion_type.php';

class Expansion extends MasterBase {
    static protected $table_name = 'expansions';
    static protected $find_key = 'name';
    static protected $cache = null;

    static function find_or_create($name, $num){
        if($col = static::find_by_key($name)){ return $col; }

        $expansion_type = ExpansionType::find_or_create(substr($num, 0, 1));
        static::insert(
            Array(
                static::$find_key => $name,
                'expansion_type_id' => $expansion_type['id'],
                'number' => (int)substr($num, 1)
            )
        );
        foreach(Database::exec('SELECT * FROM `' . static::$table_name . '` WHERE `' . static::$find_key . '` = :' . static::$find_key . ';', Array(':' . static::$find_key => $name)) as $col){
            static::$cache[(int)$col['id']] = $col;
            return $col;
        }
    }
}
