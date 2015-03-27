<?php
	$counter = 1;
	$url_array = array();
	include_once('simple_html_dom.php');
	$target_url = "http://www.amazon.com/b/ref=MoviesHP_H1_Preorders?ie=UTF8&node=7353051011&pf_rd_m=ATVPDKIKX0DER&pf_rd_s=merchandised-search-1&pf_rd_r=0V740W5F4HXYF8B5P3R0&pf_rd_t=101&pf_rd_p=1713805182&pf_rd_i=2625373011";
	$html = new simple_html_dom();
	$html->load_file($target_url); // Loads the URL above into $html
	foreach($html->find('a') as $link) //Loops through each instance of "a" in $html, assigns it to $link
	{
	if (strpos($link->href,'www.amazon.com') <> true) {
	continue;
	}
	$newurl = $link->href;
	$newpage = file_get_html($newurl);
 	$avail =  $newpage->find('span[class=availOrange]');
	// if (strpos(implode("-", $avail), 'released') <> false) {
    echo $link->href;
	echo $counter . ' ' . $avail . "<br />";
	echo gettype($link);
	array_push($url_array, implode("-", $link->href->plaintext));
	$counter++;
	// }
	}
	implode(" ",$arr);
	echo '<pre>';
	// print_r($url_array);
	echo '</pre>';
?>
// Use a database counter to keep track of position in DB