<?php
require_once dirname(__FILE__) . '/master_base.php';

class Sex extends MasterBase {
    static protected $table_name = 'sexes';
    static protected $find_key = 'name';
    static protected $cache = null;
}
