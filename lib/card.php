<?php
require_once dirname(__FILE__) . '/simple_html_dom.php';
require_once dirname(__FILE__) . '/skill.php';
require_once dirname(__FILE__) . '/arm.php';
require_once dirname(__FILE__) . '/illustrator.php';
require_once dirname(__FILE__) . '/rarity.php';
require_once dirname(__FILE__) . '/symbol.php';
require_once dirname(__FILE__) . '/sex.php';
require_once dirname(__FILE__) . '/unit_class.php';
require_once dirname(__FILE__) . '/warrior.php';
require_once dirname(__FILE__) . '/type.php';
require_once dirname(__FILE__) . '/expansion.php';
require_once dirname(__FILE__) . '/unit.php';

class Card extends MasterBase  {
    static protected $table_name = 'cards';
    static protected $find_key = 'name';
    static protected $cache = null;

    function __construct($url, $mini_image_src = null){
        $this->url = $url;
        $this->mini_image_src = $mini_image_src;

        // カード情報を取得してバラす
        $html = file_get_html($url);

        $this->main_image_src = $html->find('p[class=card_img]', 0)->find('img', 0)->src;

        $card_name = $html->find('div[class=card_name]', 0);
        $this->card_name = $card_name->find('h1', 0)->plaintext;
        $this->card_kana = $card_name->find('p', 0)->plaintext;
        $card_name->clear();
        unset($card_name);

        $this->flavor_text = $html->find('p[class=card_phrase]', 0)->plaintext;

        $card_status = $html->find('table[class=card_status]', 0);
        $this->rarity = $card_status->find('td[class=card_rarity]', 0)->plaintext;
        $expansion_name = split(' ', $card_status->find('td[class=card_records]', 0)->plaintext);
        $this->expansion_name = $expansion_name[1];
        $card_no = split('-', $card_status->find('td[class=card_no]', 0)->plaintext);
        $this->expansion_no = $card_no[0];
        $this->card_no = $this->str2int($card_no[1]);

        $illustrator = split('：', $html->find('p[class=illustrator]', 0)->plaintext);
        $this->illustrator = $illustrator[1];

        foreach($html->find('div[class=card_spec]', 0)->find('dl') as $dl){
            switch($dl->find('dt', 0)->find('img', 0)->alt){
                case '出撃コスト' :
                    $this->attack_cost = $this->str2int($dl->find('dd', 0)->plaintext);
                    break;
                case 'クラスチェンジコスト' :
                    $this->classchange_cost = $this->str2int($dl->find('dd', 0)->plaintext);
                    break;
                case '戦闘力' :
                    $this->attack = $this->str2int($dl->find('dd', 0)->plaintext);
                    break;
                case '支援力' :
                    $this->status = $this->str2int($dl->find('dd', 0)->plaintext);
                    break;
                case 'クラス' :
                    $this->unit_class = $dl->find('dd', 0)->plaintext;
                    break;
                case '兵種' :
                    $this->warrior = $dl->find('dd', 0)->plaintext;
                    break;
                case '性別' :
                    $this->sex = $dl->find('dd', 0)->find('img', 0)->alt;
                    break;
                case 'シンボル' :
                    $this->symbol = $dl->find('dd', 0)->find('img', 0)->alt;
                    break;
                case '射程' :
                    $this->ranges = Array();
                    foreach(split('-', $dl->find('dd', 0)->plaintext) as $range){
                        if($range = $this->str2int($range)){ $this->ranges[] = $range; }
                    }
                    break;
                case 'タイプ' :
                    $this->types = Array();
                    foreach($dl->find('dd', 0)->find('img') as $img){
                        $this->types[] = $img->alt;
                    }
                    break;
                case '武器' :
                    $this->arms = Array();
                    foreach($dl->find('dd', 0)->find('img') as $img){
                        $this->arms[] = $img->alt;
                    }
                    break;
            }
        }
        $card_names = explode(' ', $this->card_name, 2); 
        $this->unit_name = $card_names[1];

        $this->skills = Array();
        foreach($html->find('div[class=skill]', 0)->find('dl') as $dl){
            $dds = $dl->find('dd');
            foreach($dl->find('dt') as $id => $dt){
                $this->skills[] = new Skill($dt->innertext, $dds[$id]->innertext);
            }
        }
        $html->clear();
        unset($html);
    }

    public function save(){
        // 取り込んだデータを保存する

        // 武器
        $arm_ids = Array();
        foreach($this->arms as $arm_name){
            $arm = Arm::find_or_create($arm_name);
            $arm_ids[] = $arm['id'];
        }

        // タイプ
        $type_ids = Array();
        foreach($this->types as $type_name){
            $type = Type::find_or_create($type_name);
            $type_ids[] = $type['id'];
        }

        // スキル
        $skill_ids = Array();
        foreach($this->skills as $skill){
            $skill_ids[] = $skill->save();
        }

        // シンボル
        $symbol = Symbol::find_or_create($this->symbol);

        // 性別
        $sex = Sex::find_or_create($this->sex);

        // 兵種
        $warrior = Warrior::find_or_create($this->warrior);

        // クラス
        $unit_class = UnitClass::find_or_create($this->unit_class);

        // レアリティ
        $rarity = Rarity::find_or_create($this->rarity);

        // イラストレーター
        $illustrator = Illustrator::find_or_create($this->illustrator);

        // ユニット名
        $unit = Unit::find_or_create($this->unit_name);

        // エキスパンション
        $expansion = Expansion::find_or_create($this->expansion_name, $this->expansion_no);

        // カード情報を保存
        $card_id = static::insert(
            Array(
                'name' => $this->card_name,
                'name_kana' => $this->card_kana,
                'flavor_text' => $this->flavor_text,
                'unit_id' => $unit['id'],
                'symbol_id' => $symbol['id'],
                'rarity_id' => $rarity['id'],
                'expansion_id' => $expansion['id'],
                'card_no' => $this->card_no,
                'illustrator_id' => $illustrator['id'],
                'attack_cost' => $this->attack_cost,
                'classchange_cost' => $this->classchange_cost,
                'attack' => $this->attack,
                'status' => $this->status,
                'sex_id' => $sex['id'],
                'unit_class_id' => $unit_class['id'],
                'warrior_id' => $warrior['id']
            )
        );

        // 武器をひも付
        foreach($arm_ids as $arm_id){
            Database::exec(
                'INSERT INTO `card_arms` (`card_id`, `arm_id`) VALUES(:card_id, :arm_id)',
                Array(
                    ':card_id' => $card_id,
                    ':arm_id' => $arm_id
                )
            );
        }

        // タイプをひも付
        foreach($type_ids as $type_id){
            Database::exec(
                'INSERT INTO `card_types` (`card_id`, `type_id`) VALUES(:card_id, :type_id)',
                Array(
                    ':card_id' => $card_id,
                    ':type_id' => $type_id
                )
            );
        }

        // 射程をひも付
        foreach($this->ranges as $range){
            Database::exec(
                'INSERT INTO `card_ranges` (`card_id`, `range`) VALUES(:card_id, :range)',
                Array(
                    ':card_id' => $card_id,
                    ':range' => $range
                )
            );
        }

        // スキルをひも付
        foreach($skill_ids as $skill_id){
            Database::exec(
                'INSERT INTO `card_skills` (`card_id`, `skill_id`) VALUES(:card_id, :skill_id)',
                Array(
                    ':card_id' => $card_id,
                    ':skill_id' => $skill_id
                )
            );
        }
    }

    protected function str2int($var){
        if(is_numeric($var)){
            return (int)$var;
        }
        return null;
    }
}
