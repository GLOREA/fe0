<?php
require_once dirname(__FILE__) . '/master_base.php';

class Symbol extends MasterBase {
    static protected $table_name = 'symbols';
    static protected $find_key = 'name';
    static protected $cache = null;
}
