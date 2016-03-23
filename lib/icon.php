<?php
require_once dirname(__FILE__) . '/master_base.php';

class Icon extends MasterBase {
    static protected $table_name = 'units';
    static protected $find_key = 'name';
    static protected $cache = null;

    static function replace($str){

    }

    static function init(){
        static::insert(
            Array(
                'name' => 'リバース1',
                'type' => 'cost',
                'regexp' => '|<img[^>]+src=[\'"]/img/cards/icon/skill/icon_rev1([^\.]+)?.png[\'"][^>]*>|',
            )
        );
        static::insert(
            Array(
                'name' => 'リバース2',
                'type' => 'cost',
                'regexp' => '|<img[^>]+src=[\'"]/img/cards/icon/skill/icon_rev2([^\.]+)?.png[\'"][^>]*>|',
            )
        );
        static::insert(
            Array(
                'name' => 'リバース3',
                'type' => 'cost',
                'regexp' => '|<img[^>]+src=[\'"]/img/cards/icon/skill/icon_rev3([^\.]+)?.png[\'"][^>]*>|',
            )
        );
        static::insert(
            Array(
                'name' => 'リバース4',
                'type' => 'cost',
                'regexp' => '|<img[^>]+src=[\'"]/img/cards/icon/skill/icon_rev4([^\.]+)?.png[\'"][^>]*>|',
            )
        );
        static::insert(
            Array(
                'name' => 'リバース5',
                'type' => 'cost',
                'regexp' => '|<img[^>]+src=[\'"]/img/cards/icon/skill/icon_rev5([^\.]+)?.png[\'"][^>]*>|',
            )
        );
        static::insert(
            Array(
                'name' => 'アクション',
                'type' => 'cost',
                'regexp' => '|<img[^>]+src=[\'"]/img/cards/icon/skill/icon_act([^\.]+)?.png[\'"][^>]*>|',
            )
        );
    }
}


