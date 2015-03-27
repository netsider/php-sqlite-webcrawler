<?php
/* update your path accordingly */
include_once 'simple_html_dom.php';
$url = "http://www.amazon.com/Halloween-Complete-Collection-Limited-Blu-ray/dp/B00KDU8HQQ/ref=sr_1_1?s=movies-tv&ie=UTF8&qid=1403099756&sr=1-1";
$html = file_get_html($url);
$ret =  $html->find('span[class=availOrange]');
foreach($ret as $story){
    echo $story->find('a', 0)->plaintext . "<br>";
    // echo $ret[0];
	}
?>