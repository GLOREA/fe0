<?php
require_once dirname(__FILE__) . '/master_base.php';

class UnitClass extends MasterBase {
    static protected $table_name = 'unit_classes';
    static protected $find_key = 'name';
    static protected $cache = null;
}
