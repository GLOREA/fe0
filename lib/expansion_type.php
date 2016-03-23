<?php
require_once dirname(__FILE__) . '/master_base.php';

class ExpansionType extends MasterBase {
    static protected $table_name = 'expansion_types';
    static protected $find_key = 'charactor';
    static protected $cache = null;
}
