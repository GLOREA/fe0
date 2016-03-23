<?php
class Skill {
    function __construct($name, $text){
        $this->type = null;
        $this->cost = null;
        $this->sub_types = Array();
        $this->set_skill_name($name);
        $this->set_skill($text);
    }

    protected function set_skill_name($text){
        if(preg_match('|<img[^>]+src=["\']/img/cards/icon/supportskill/icon_([^_]+)_clear.png["\'][^>]*>(.+)|', $text, $match)){
            switch($match[1]) {
                case 'attack' :
                    $this->type = 'AttackSupport';
                    break;
                case 'deffence' :
                    $this->type = 'DeffenceSupport';
                    break;
            }
            $this->name = $match[2];
            $this->sub_types[] = 'Symbol';
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
                    break;
                case 'deffence' :
                    $this->type = 'DeffenceSupport';
                    break;
            }
            if($this->type){
                $this->sub_types[] = 'Symbol';
                $this->effect = $match[2];
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
