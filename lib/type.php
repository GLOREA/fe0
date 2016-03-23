<?php
require_once dirname(__FILE__) . '/master_base.php';

class Type extends MasterBase {
    static protected $table_name = 'types';
    static protected $find_key = 'name';
    static protected $cache = null;
}
