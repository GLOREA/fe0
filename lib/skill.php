<?php
require_once dirname(__FILE__) . '/skill_type.php';
require_once dirname(__FILE__) . '/skill_sub_type.php';

class Skill extends MasterBase {
    static protected $table_name = 'skills';
    static protected $find_key = 'name';
    static protected $cache = null;

    function __construct($name, $text){
        $this->type = null;
        $this->cost = null;
        $this->effect = null;
        $this->sub_types = Array();
        $this->set_skill_name($name);
        $this->set_skill($text);
    }

    public function save(){
        // 取り込んだデータを保存する

        // タイプ
        $type = SkillType::find_or_create($this->type);

        // サブタイプ
        $sub_type_ids = Array();
        foreach($this->sub_types as $sub_type_name){
            $sub_type = SkillSubType::find_or_create($sub_type_name);
            $sub_type_ids[] = $sub_type['id'];
        }

        // 同一の情報が記録されてるか検索して、あれば保存しない
        if(empty($this->cost)){
            $query = 'SELECT * FROM `' . static::$table_name . '` WHERE `skill_type_id` = :skill_type_id AND IS NULL `cost` AND `name` = :name AND `effect` = :effect';
            $params = Array(
                ':skill_type_id' => $type['id'],
                ':name' => $this->name,
                ':effect' => $this->effect
            );
        }else{
            $query = 'SELECT * FROM `' . static::$table_name . '` WHERE `skill_type_id` = :skill_type_id AND `cost` = :cost AND `name` = :name AND `effect` = :effect';
            $params = Array(
                ':skill_type_id' => $type['id'],
                ':cost' => $this->cost,
                ':name' => $this->name,
                ':effect' => $this->effect
            );
        }

        $select = Database::exec($query, $params);
        if(isset($select[0])) { return $select[0]['id']; }

        // カード情報を保存
        $skill_id = static::insert(
            Array(
                'skill_type_id' => $type['id'],
                'cost' => $this->cost,
                'name' => $this->name,
                'effect' => $this->effect,
                'level' => (isset($this->level) ? $this->level : null)
            )
        );

        // サブタイプをひも付
        foreach($sub_type_ids as $sub_type_id){
            Database::exec(
                'INSERT INTO `skill_skill_sub_types` (`skill_id`, `skill_sub_type_id`) VALUES(:skill_id, :skill_sub_type_id)',
                Array(
                    ':skill_id' => $skill_id,
                    ':skill_sub_type_id' => $sub_type_id
                )
            );
        }

        return $skill_id;
    }

    protected function set_skill_name($text){
        if(preg_match('|<img[^>]+src=["\']/img/cards/icon/supportskill/icon_([^_]+)_clear.png["\'][^>]*>(.+)|', $text, $match)){
            switch($match[1]) {
                case 'attack' :
                    $this->type = 'AttackSupport';
                    $this->sub_types[] = 'AttackSymbol';
                    break;
                case 'deffence' :
                    $this->type = 'DeffenceSupport';
                    $this->sub_types[] = 'DeffenceSymbol';
                    break;
            }
            $this->name = $match[2];
        }else{
            $this->name = $text;
        }
    }

    protected function set_skill($text){
        // 攻撃の紋章・防御の紋章の記述場所が弾によって異なるのでこっちにも書いておく（ひどくない？）
        if(preg_match('|<img[^>]+src=["\']/img/cards/icon/supportskill/icon_([^_]+)_clear.png["\'][^>]*>(.+)|', $text, $match)){
            switch($match[1]) {
                case 'attack' :
                    $this->type = 'AttackSupport';
                    $this->sub_types[] = 'AttackSymbol';
                    $this->effect = $match[2];
                    break;
                case 'deffence' :
                    $this->type = 'DeffenceSupport';
                    $this->sub_types[] = 'DeffenceSymbol';
                    $this->effect = $match[2];
                    break;
            }
        }

        // set_skill_name 関数で type が設定されている場合もあるので、ここでチェックする
        if($this->type){
            if(is_null($this->effect)){ $this->effect = $text; }
            return;
        }

        preg_match('|(<img[^>]+src=["\']/img/cards/icon/skill/icon_lev([0-9]+)_clear.png["\'][^>]*>)?(<img[^>]+src=["\']/img/cards/icon/supportskill/icon_([^_\.]+)(_clear)?.png["\'][^>]*>)?<img[^>]+src=["\']/img/cards/icon/skill/icon_([^_]+)_clear.png["\'][^>]*>(<img[^>]+src="/img/cards/icon/skill/icon_([^_]+)_clear.png["\'][^>]*>)?(【(.+)】)?(.+)|', $text, $match);
        if(isset($match[2])){
            if(($level = (int)$match[2]) > 0){
                $this->sub_types[] = 'Level' . $match[2];
                $this->level = $level;
            }
        }

        if(isset($match[4])){
            switch($match[4]) {
                case 'ccs' :
                    $this->sub_types[] = 'ClassChange';
                    break;
                case 'fs' :
                    $this->sub_types[] = 'FormationSkill';
                    break;
                case 'cf' :
                    $this->sub_types[] = 'CarnageForm';
                    break;
            }
        }

        if(isset($match[6])){
            switch($match[6]) {
                case 'kidou' :
                    $this->type = 'Activated';
                    break;
                case 'zyouzi' :
                    $this->type = 'Static';
                    break;
                case 'zidou' :
                    $this->type = 'Triggered';
                    break;
                case 'tokusyu' :
                    $this->type = 'CharacteristicDefining';
                    break;
            }
        }

        if(isset($match[8])){
            switch($match[8]) {
                case 'once' :
                    $this->sub_types[] = 'Once';
                    break;
            }
        }
        if(isset($match[10])){ $this->cost = $match[10]; }
        if(isset($match[11])){ $this->effect = $match[11]; }
    }
}
