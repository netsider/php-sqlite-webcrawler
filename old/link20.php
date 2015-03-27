<?php
	include_once('simple_html_dom.php');
	$url = 'http://www.amazon.com/movies';
	$exclude = ['.mx', '.br', '.au', 'https', 'Media-Player', 'redirect', 'product-reviews', 'services.amazon', 'aws.amazon', '#', 'fresh.amazon', 'nav_a', 'onload='];
	$include = ['www.amazon.com'];
	$root_url = 'http://www.amazon.com';
	function crawl($target_url, $mhave, $mnot, $rurl){
		$html = new simple_html_dom();
		$html->load_file($target_url);
		$count = 0;
		$array= array();
		foreach($html->find('a') as $link)
		{
			if (empty($link->href) || empty($link)){
				continue;
			}
			if (strpos($link->href,$rurl) == 0 AND strpos($link->href,'http://') == 0) {
				$url = $rurl . urldecode($link->href);
			}else{
				$url = $link->href;
			}
			$pattern2  = '/' . implode('|', array_map('preg_quote', $mhave)) . '/i';
			if(preg_match($pattern2, $url) <> 1) {
				continue;
			}
			$pattern  = '/' . implode('|', array_map('preg_quote', $mnot)) . '/i';
			if(preg_match($pattern, $link) > 0) {
				continue;
			}
			$link_href = $link->href;
			// array_push($array, $url);
			// $r4 = mysqli_query($con, "SELECT * FROM `links` WHERE url='$url'");
			// if($r4){
			// echo mysqli_num_rows($r4);
			// if (mysqli_num_rows($r4) == 0) {
			// mysqli_query($con, "INSERT INTO `links`(url,original,parent,cost) VALUES ('$url','$link->href','$target_url','$cost')");
			$array[$count]['url'] = $url;
			$array[$count]['parent'] = $target_url;
			$array[$count]['original'] = $link_href;
			$array[$count]['scraped'] = 0;
			$array[$count]['crawled'] = 0;
			// }}else{
			// echo '<br/>Failed to Insert into DB<br/>';
			// }
			$count++;
			echo '</font>';
		}
	return $array;
	}
		echo '<br/><br/>';
		// $con = mysqli_connect('127.0.0.1', 'root', '', 'updayte');
		$array1= crawl($url, $include, $exclude, $root_url);
		echo '<pre>';
		var_export($array1);
		echo '</pre>';
		// echo 'array_length: ' . $a_length = count($array1) - 1;
		// for ($x=0; $x<=$a_length; $x++) {
		  // echo 'URL(' . $x . ')->' . $array1[$x]['url'] . '<br/><br/>';
		// }
		// $mysql_host = '127.0.0.1';
		// $mysql_user = 'root';
		// $mysql_password = '';
		// $mysql_database = 'updayte';
		// foreach ($array1 as $L){
		// $con = mysqli_connect($mysql_host, $mysql_user, $mysql_password);
		// mysqli_select_db($con, $mysql_database);
		// FInD L's cost in DB!
		// Echo out the parent.
		// $cost = mysqli_query($con, "SELECT cost FROM `links` WHERE url='$L'"); // Get Cost of Current URL Being Read
		// $array2 = crawl($L);
		// }
	
	
	
	
	
		// This script loads many from the DB and searches them for new links, depending on the cost.
		// echo 'Starting Second Pass';
		// $nextbatch = mysqli_query($con, "SELECT url FROM `links` WHERE cost='$cost'"); // Finds links with the cost from the first pass.
		// while($links = mysqli_fetch_array($nextbatch)){
		// foreach ($links as $L){
		// echo '<pre>';
		// print_r($L);
		// echo '</pre>';
		// echo '<br/><br/>';
		
	// $html2 = new simple_html_dom();
	// $html2->load_file($L);
	// foreach($html2->find('a') as $link)
	// {
	// if (strpos($link,'http://www.amazon.com') == false AND !strpos($link,'http://www')) {
	// $url = "http://www.amazon.com/" . urldecode($link->href);
	// }else{
	// $url = $link->href;
	// }
	// if (!strpos($link,'http://www.amazon.com')){	
		// continue;
	// }
	// $pattern  = '/' . implode('|', array_map('preg_quote', $needle)) . '/i';
    // if(preg_match($pattern, $link)) {
    // continue;
    // }
	
			// $cost = $cost + 1;
			// mysqli_select_db($con, $mysql_database);
			// echo '$url: ' . $url;
			// echo '<br/>';
			// echo '$link->href Decode: ' . urldecode($link->href);
			// echo '<br/><br/>';
			// $r4 = mysqli_query($con, "SELECT * FROM `links` WHERE url='$url'");
			// echo mysqli_num_rows($r4);
			// if (mysqli_num_rows($r4) == 0) {
			// mysqli_query($con, "INSERT INTO `links`(url,original,parent,cost) VALUES ('$url','$link->href','$target_url','$cost')");
			// }
			
			// mysqli_query($con, "UPDATE $current_table SET find ='$findurl' WHERE title = '$db_title'");
	
		// }
		
		
		
		
		
		
		
		
		
		
		// }}
	
?>