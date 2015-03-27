<?php
	include_once('simple_html_dom.php');
	$url = 'http://www.amazon.com/movies';
	
	function crawl($target_url){
		$html = new simple_html_dom();
		$html->load_file($target_url);
		$count = 0;
		$exclude = ['.mx', '.br', '.au', 'https', 'Media-Player', 'redirect', 'product-reviews', 'services.amazon', 'aws.amazon', '#', 'fresh.amazon', 'nav_a', 'onload='];
		$include = ['www.amazon.com'];
		$array= array();
		$db_array = array();
		$mysql_database = 'updayte';
		$con = mysqli_connect('127.0.0.1', 'root', '');
		mysqli_select_db($con, $mysql_database);
	foreach($html->find('a') as $link)
	{
		if (empty($link->href) || empty($link)){
		continue;
		}
		
		if (strpos($link->href,'amazon.com') == 0 AND strpos($link->href,'http://') == 0) {
		$newurl = "http://www.amazon.com" . urldecode($link->href);
		echo '<font color=green>';
		}else{
		echo '<font color=blue>';
		$newurl = $link->href;
		}
	$pattern2  = '/' . implode('|', array_map('preg_quote', $include)) . '/i';
	if(preg_match($pattern2, $newurl) <> 1) {
    continue;
/* 	echo '<font color=brown>';
	echo 'linkhref: ' . $link->href;
	echo '<br/>';
	echo '$newurl: ' . $newurl;
	echo '</font><br/>'; */
    }
	$pattern  = '/' . implode('|', array_map('preg_quote', $exclude)) . '/i';
    if(preg_match($pattern, $link) > 0) {
    continue;
	/* echo '<font color=green>';
	echo 'linkhref: ' . $link->href;
	echo '<br/>';
	echo '$newurl: ' . $newurl;
	echo '</font><br/>'; */
    }
			// echo 'newurl: ' . $newurl;
			// echo '<br/>';
			// echo 'link: ' . $link;
			$link_href = $link->href;
			// array_push($array, $newurl);
			// $r4 = mysqli_query($con, "SELECT * FROM `links` WHERE url='$newurl'");
			// if($r4){
			// echo mysqli_num_rows($r4);
			// if (mysqli_num_rows($r4) == 0) {
			// mysqli_query($con, "INSERT INTO `links`(url,original,parent,cost) VALUES ('$newurl','$link->href','$target_url','$cost')");
			$db_array[$count]['url'] = $newurl;
			$db_array[$count]['parent'] = $target_url;
			$db_array[$count]['original'] = $link_href;
			$db_array[$count]['scraped'] = 0;
			$db_array[$count]['crawled'] = 0;
			// }}else{
			// echo '<br/>Failed to Insert into DB<br/>';
			// }
			$count++;
			echo '</font>';
	}
	mysqli_close($con);
	return $db_array;
	
	}
	echo '<br/><br/>';
		// Begin Webcrawler
		$array1= crawl($url);
		echo '<pre>';
		var_export($array1);
		echo '</pre>';
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
	// $newurl = "http://www.amazon.com/" . urldecode($link->href);
	// }else{
	// $newurl = $link->href;
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
			// echo '$newurl: ' . $newurl;
			// echo '<br/>';
			// echo '$link->href Decode: ' . urldecode($link->href);
			// echo '<br/><br/>';
			// $r4 = mysqli_query($con, "SELECT * FROM `links` WHERE url='$newurl'");
			// echo mysqli_num_rows($r4);
			// if (mysqli_num_rows($r4) == 0) {
			// mysqli_query($con, "INSERT INTO `links`(url,original,parent,cost) VALUES ('$newurl','$link->href','$target_url','$cost')");
			// }
			
			// mysqli_query($con, "UPDATE $current_table SET find ='$findurl' WHERE title = '$db_title'");
	
		// }
		
		
		
		
		
		
		
		
		
		
		// }}
	
?>