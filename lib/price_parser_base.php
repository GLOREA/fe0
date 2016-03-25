<?php
require_once dirname(__FILE__) . '/simple_html_dom.php';

class PriceParserBase {
    function __construct(){
        $html = file_get_html('http://www.amenitydream.com/page/46');
        foreach($html->find('div[class=freebox]') as $div){
            foreach($div->find('a') as $anchor){
                if($anchor->plaintext === '全てのカード'){
                    $this->hoge($anchor->href);
                }
// PRカード
            }
        }
        $html->clear();
        unset($html);
    }

    function hoge($url){
        $html = file_get_html($url);
        foreach($html->find('div[class=item_data]') as $item){
            var_dump($item->find('span[class=goods_name]', 0)->plaintext);
            var_dump($item->find('span[class=figure]', 0)->plaintext);
            var_dump($item->find('p[class=stock]', 0)->plaintext);
        }
    }
}

new PriceParserBase();