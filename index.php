<?php
include('simple_html_dom.php');
$file = 'pdd.csv';
$html = file_get_html('https://pdd-russia.com/pdd-russia/pdd/pdd/russia.html');

foreach($html->find('div.box') as $item) {
    foreach ($item->find('a') as $e) {
        $link = mb_substr($e->href, 2);
        $link = "https://pdd-russia.com/pdd-russia/pdd".$link;
        getArticle($link,$file);
    }
}
function getArticle($url,$file){ // текст статьи, например 1
    $htmlArticle = file_get_html($url);

    foreach($htmlArticle->find('div.box') as $item) {
        foreach ($item->find('div') as $el) {   //  ставим вместо картинок -  *** дорожный знак
            $el->outertext = '*** дорожный знак';
            $item->save();
        }
        $text_item_article = ''; // текст  подстатьи, например 1.4
        $text_article = $item->innertext;
        $text_article = str_replace(['<hr>','<p>','</p>','<ul>','</ul>','<li>','</li>'], '', $text_article);
        $array_articles = explode("<span>", $text_article);
        foreach ($array_articles as $item_article) {


            $str = strpos($item_article, "</span>");
            $rest = substr($item_article, 0, $str);

            if (stripos($rest, '.')) {
                $item_article =  substr($item_article,  $str+7);
                if ($number_article){
                    $tofile = "$number_article;$text_item_article\n";
                    echo '<br>$tofile = ' . $tofile . '<br><br>';
                    $bom = "\xEF\xBB\xBF";
                    @file_put_contents($file, $bom .file_get_contents($file). $tofile);
                }

                $number_article = $rest; // запоминаем номер подстатьи
                $text_item_article = ''; // очищаем тест подстатьи, теперь сюда будет писаться новая подстатья
                $text_item_article .= $item_article;
            } else {
                $text_item_article .= $item_article;
            }
        }
    }
}

