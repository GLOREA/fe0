<?php
/*
DB構造メモ書き
[Arms] 武器
-- id
-- name
-- icon_url_id

[Expansions] エキスパンション
-- id
-- name
-- expansion_type_id
-- number

[ExpansionTypes] エキスパンションタイプ
-- id
-- name
-- charactor

[CardArms]
-- card_id
-- arm_id

[Cards] カード
-- id
-- name
-- name_kana
-- flavor_text
-- unit_id
-- symbol_id
-- rarity_id
-- expansion_id
-- card_no
-- illustrator_id
-- attack_cost
-- classchange_cost
-- sex_id
-- class_id
-- warrior_id

[CardRanges] 射程
-- card_id
-- range

[CardSkills]
-- card_id
-- skill_id

[CardTypes]
-- card_id
-- type_id

[Classes] クラス
-- id
-- name

[Illustrators] イラスト
-- id
-- name

[Sexes] 性別
-- id
-- string
-- icon_url

[Skills] スキル
-- id
-- skill_type_id
-- cost (そのままのテキストを埋め込もうとは思うが…うーん…）
-- name
-- effect
-- level （0が基本。サブタイプにレベルアップがあって初めて意味を成す）

[SkillSkillSubTypes]
-- skill_id
-- skill_sub_type_id

[SkillTypes] スキルタイプ（起動・自動・常時・特殊）
-- id
-- name
-- name_ja
-- icon_url

[SkillSubTypes] スキルサブタイプ（クラスチェンジスキル・攻撃の紋章・防御の紋章・レベルアップスキル・カルネージフォーム）
-- id
-- name
-- name_ja
-- icon_url

[Symbols]
-- id
-- name
-- icon_url

[Rarities] レアリティ
-- id
-- name
-- is_foil （プラスかどうかで判断）

[Types] タイプ
-- id
-- name
-- icon_url

[Warriors] 兵種
-- id
-- name

[Units] ユニット
-- id
-- name
*/

class Database{
    static private $db = null;

    static public function get_pdo($init = false){
        if(isset(self::$db)){ return self::$db; }
        self::$db = new self($init);
    }

    static public function exec($query, $params = Array()){
        if(!isset(self::$db)){ self::$db = new self(false); }
        return self::$db->execute($query, $params);
    }

    static public function last_insert_id(){
        return self::$db->lastInsertId();
    }

    function __construct($init = false){
        // DB名とかは後で外から指定できるように修正する
        $this->pdo = new PDO('mysql:host=localhost;dbname=fe0', 'root', 'password');

        if($init){ $this->init(); }
    }

    function lastInsertId(){
        return $this->pdo->lastInsertId();
    }

    public function execute($query, $params = Array(), $fetch_style = PDO::FETCH_ASSOC){
        $sth = $this->pdo->prepare($query);
        $sth->execute($params);
        return $sth->fetchAll($fetch_style);
    }

    protected function drop_tables($table_names){
        if(!is_array($table_names)){ $table_names = Array($table_names); }
        $sth = $this->pdo->prepare('DROP TABLE IF EXISTS `' . join('`, `', $table_names) . '`;');
        $sth->execute();
        return $sth->fetchAll();
    }

    protected function create_table($table_name, $columns = Array(), $options = Array()){
        // プレースホルダー等使ってないが、使うとテーブル名など認識できないため
        $opt_queries = Array();
        foreach($columns as $col_name => $col_options){
            $col_query = Array('`' . $col_name . '`');
            if(is_array($col_options)){
                $col_query = array_merge($col_query, $col_options);
            }else{
                $col_query[] = $col_options;
            }
            $opt_queries[] = join(' ', $col_query);
        }
        foreach($options as $opt){
            if(!is_array($opt)){ $opt = Array($opt); }
            $opt_queries[] = join(' ', $opt);
        }
        $sth = $this->pdo->prepare('CREATE TABLE `' . $table_name . '` (' . join(', ', $opt_queries) . ');');
        $sth->execute();
        return $sth->fetchAll();
    }

    protected function init(){
        // DBを初期化する
        $this->drop_tables(Array(
            'arms',
            'expansions',
            'expansion_types',
            'card_arms',
            'cards',
            'card_ranges',
            'card_skills',
            'card_types',
            'unit_classes',
            'illustrators',
            'sexes',
            'skills',
            'skill_skill_sub_types',
            'skill_types',
            'skill_sub_types',
            'symbols',
            'rarities',
            'types',
            'warriors',
            'units',
            'image_urls',
            'icons'
        ));

        $this->create_table(
            'arms',
            Array(
                'id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'icon_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)',
                'unique (`name`)'
            )
        );

        $this->create_table(
            'expansions',
            Array(
                'id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'expansion_type_id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'number' => Array(
                    'smallint',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'expansion_types',
            Array(
                'id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'DEFAULT \'Unknown\'',
                    'NOT NULL'
                ),
                'charactor' => Array(
                    'varchar(1)'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'card_arms',
            Array(
                'card_id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'arm_id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                )
            )
        );

        $this->create_table(
            'cards',
            Array(
                'id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'name_kana' => Array(
                    'varchar(128)'
                ),
                'flavor_text' => Array(
                    'text'
                ),
                'unit_id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'symbol_id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'rarity_id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'expansion_id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'card_no' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'illustrator_id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'attack_cost' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'classchange_cost' => Array(
                    'tinyint',
                    'UNSIGNED'
                ),
                'attack' => Array(
                    'smallint',
                    'UNSIGNED'
                ),
                'status' => Array(
                    'smallint',
                    'UNSIGNED'
                ),
                'sex_id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'unit_class_id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'warrior_id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'mini_image_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                ),
                'main_image_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'card_ranges',
            Array(
                'card_id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'range' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL'
                )
            )
        );

        $this->create_table(
            'card_skills',
            Array(
                'card_id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'skill_id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL'
                )
            )
        );

        $this->create_table(
            'card_types',
            Array(
                'card_id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'type_id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL'
                )
            )
        );

        $this->create_table(
            'unit_classes',
            Array(
                'id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'illustrators',
            Array(
                'id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'sexes',
            Array(
                'id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(8)',
                    'NOT NULL'
                ),
                'icon_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'skills',
            Array(
                'id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'skill_type_id' => Array(
                    'tinyint',
                    'UNSIGNED'
                ),
               'cost' => Array(
                    'text'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'effect' => Array(
                    'text',
                    'NOT NULL'
                ),
                'level' => Array(
                    'tinyint',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'skill_skill_sub_types',
            Array(
                'skill_id' => Array(
                    'int(11)',
                    'UNSIGNED',
                    'NOT NULL'
                ),
                'skill_sub_type_id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL'
                )
            )
        );

        $this->create_table(
            'skill_types',
            Array(
                'id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'DEFAULT \'Unknown\'',
                    'NOT NULL'
                ),
                'name_en' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'icon_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'skill_sub_types',
            Array(
                'id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'DEFAULT \'Unknown\'',
                    'NOT NULL'
                ),
                'name_en' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'icon_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'symbols',
            Array(
                'id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'icon_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'rarities',
            Array(
                'id' => Array(
                    'tinyint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'abbreviation' => Array(
                    'varchar(8)',
                    'NOT NULL'
                ),
                'name' => Array(
                    'varchar(128)',
                    'DEFAULT \'Unknown\'',
                    'NOT NULL'
                ),
                'is_foil' => Array(
                    'boolean'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'types',
            Array(
                'id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'icon_url_id' => Array(
                    'int(11)',
                    'UNSIGNED'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'warriors',
            Array(
                'id' => Array(
                    'smallint',
                    'UNSIGNED',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'units',
            Array(
                'id' => Array(
                    'int(11)',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                )
            ),
            Array(
                'primary key(`id`)',
                'unique (`name`)'
            )
        );

        $this->create_table(
            'image_urls',
            Array(
                'id' => Array(
                    'int(11)',
                    'NOT NULL',
                    'auto_increment'
                ),
                'url' => Array(
                    'text',
                    'NOT NULL'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );

        $this->create_table(
            'icons',
            Array(
                'id' => Array(
                    'int(11)',
                    'NOT NULL',
                    'auto_increment'
                ),
                'name' => Array(
                    'varchar(128)',
                    'NOT NULL'
                ),
                'type' => Array(
                    'varchar(16)',
                    'NOT NULL'
                ),
                'regexp' => Array(
                    'varchar(256)',
                    'NOT NULL'
                )
            ),
            Array(
                'primary key(`id`)'
            )
        );
    }
}

// Database::get_pdo(true);
