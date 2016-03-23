<?php
require_once dirname(__FILE__) . '/master_base.php';

class SkillSubType extends MasterBase {
    static protected $table_name = 'skill_sub_types';
    static protected $find_key = 'name_en';
    static protected $cache = null;
}
