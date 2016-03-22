<?php
class Skill {
    function __construct($name, $text){
        $this->type = null;
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
        if($this->type){
            $this->effect = $text;
            return;
        }

        preg_match('|(<img[^>]+src=["\']/img/cards/icon/supportskill/icon_([^_\.]+).png["\'][^>]*>)<img[^>]+src=["\']/img/cards/icon/skill/icon_([^_]+)_clear.png["\'][^>]*>(<img[^>]+src="/img/cards/icon/skill/icon_([^_]+)_clear.png["\'][^>]*>)?(【(.+)】)?(.+)|', $text, $match);
        switch($match[2]) {
            case 'ccs' :
                $this->sub_types[] = 'ClassChange';
                break;
        }
        switch($match[3]) {
            case 'kidou' :
                $this->type = 'Activated';
                break;
            case 'zyouzi' :
                $this->type = 'Static';
                break;
            case 'zidou' :
                $this->type = 'Triggered';
                break;
        }
        switch($match[5]) {
            case 'once' :
                $this->sub_types[] = 'Once';
                break;
        }

        $this->cost = $match[7];
        $this->effect = $match[8];
    }
}
