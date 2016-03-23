<?php
require_once dirname(__FILE__) . '/master_base.php';

class Illustrator extends MasterBase {
    static protected $table_name = 'illustrators';
    static protected $find_key = 'name';
    static protected $cache = null;
}
