<?php
require_once dirname(__FILE__) . '/master_base.php';

class Unit extends MasterBase {
    static protected $table_name = 'units';
    static protected $find_key = 'name';
    static protected $cache = null;
}
