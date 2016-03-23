<?php
require_once dirname(__FILE__) . '/master_base.php';

class Warrior extends MasterBase {
    static protected $table_name = 'warriors';
    static protected $find_key = 'name';
    static protected $cache = null;
}
