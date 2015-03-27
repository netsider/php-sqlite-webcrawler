<?php
	$needle = ['.mx', '.br', '.au'];
	include_once('simple_html_dom.php');
	$target_url = "http://www.amazon.com/b/ref=MoviesHP_H1_Preorders?ie=UTF8&node=7353051011&pf_rd_m=ATVPDKIKX0DER&pf_rd_s=merchandised-search-1&pf_rd_r=0V740W5F4HXYF8B5P3R0&pf_rd_t=101&pf_rd_p=1713805182&pf_rd_i=2625373011";
	$html = new simple_html_dom();
	$html->load_file($target_url);
	foreach($html->find('a') as $link)
	{
	if (strpos($link,'https') > 0){
		continue;
	}
	if (strpos($link,'http://www.amazon.com') == false AND !strpos($link,'http://www')) {
	$newurl = "http://www.amazon.com/" . urldecode($link->href);
	}else{
	$newurl = $link->href;
	}
	if (strpos($link,'http://www.amazon.com') == false){	
		continue;
	}
	
	$pattern  = '/' . implode('|', array_map('preg_quote', $needle)) . '/i';
    if(preg_match($pattern, $link)) {
    continue;
    }
		
	// $newpage = file_get_html($newurl);
	 echo '$newurl: ' . $newurl;
	echo '<br/>';
    echo '$link->href Decode: ' . urldecode($link->href);
	echo '<br/><br/>';
	}
?>