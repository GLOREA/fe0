<?php
require_once dirname(__FILE__) . '/simple_html_dom.php';
require_once dirname(__FILE__) . '/skill.php';

class Card {
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
                    $this->class = $dl->find('dd', 0)->plaintext;
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
        $card_names = split(' ', $this->card_name); 
        $this->unit_name = $card_names[1];

        $this->skills = Array();
        foreach($html->find('div[class=skill]', 0)->find('dl') as $dl){
echo $dl;
            $dds = $dl->find('dd');
            foreach($dl->find('dt') as $id => $dt){
                $this->skills[] = new Skill($dt->innertext, $dds[$id]->innertext);
            }
        }
        $html->clear();
        unset($html);
    }

    protected function str2int($var){
        if(is_numeric($var)){
            return (int)$var;
        }
        return null;
    }
}
