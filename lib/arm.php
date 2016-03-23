<?php
require_once dirname(__FILE__) . '/master_base.php';

class Arm extends MasterBase {
    static protected $table_name = 'arms';
    static protected $find_key = 'name';
    static protected $cache = null;
}
