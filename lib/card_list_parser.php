<?php
require_once dirname(__FILE__) . '/simple_html_dom.php';
require_once dirname(__FILE__) . '/card.php';

class CardListParser {
    function __construct(){
        // 最終ページの番号を取得しておく
        $this->get_last_page_number();
    }

    public function parse(){
        $this->card_list_parse();

        for($i = 2; $i <= $this->last_page_number; $i++){
            $this->card_list_parse($i);
        }
    }

    protected function card_list_parse($page_number = null){
        $html = $this->get_card_list_page($page_number);
        foreach($html->find('td[class=cell_img]') as $id => $td){
            $card = new Card($td->find('a', 0)->href, $td->find('img', 0)->src);
            $card->save();
            var_dump($card->card_name);
        }
        $html->clear();
        unset($html);
    }

    protected function get_card_list_page($page_number = null){
        if($page_number == null){
            $url = 'http://fecipher.jp/cards/';
        }else{
            $url = 'http://fecipher.jp/cards/index_' . $page_number . '.html';
        }
        return file_get_html($url);
    }

    protected function get_last_page_number(){
        $html = $this->get_card_list_page();
        $this->last_page_number = 1;
        foreach($html->find('div[class=pagenate]', 0)->find('a') as $card_list){
            if(preg_match('|http://fecipher.jp/cards/index_([0-9]+).html|', $card_list->href, $match) === false) { continue; }
            if($this->last_page_number < (int)$match[1]) { $this->last_page_number = (int)$match[1]; }
        }
        $html->clear();
    }
}

// $test = new CardListParser();
// $test->parse();
// print( file_get_contents());
