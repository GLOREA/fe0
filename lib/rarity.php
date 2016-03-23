<?php
require_once dirname(__FILE__) . '/master_base.php';

class Rarity extends MasterBase {
    static protected $table_name = 'rarities';
    static protected $find_key = 'abbreviation';
    static protected $cache = null;
}
