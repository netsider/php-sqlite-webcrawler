









<?php
	
	$cost = 0;
	$counter = 0;
	$needle = ['.mx', '.br', '.au', 'https', 'Media-Player'];
	include_once('simple_html_dom.php');
	$url = "http://www.amazon.com/b/ref=MoviesHP_H1_Preorders?ie=UTF8&node=7353051011&pf_rd_m=ATVPDKIKX0DER&pf_rd_s=merchandised-search-1&pf_rd_r=0V740W5F4HXYF8B5P3R0&pf_rd_t=101&pf_rd_p=1713805182&pf_rd_i=2625373011";
	
	// $counter = $counter + 1;
	
	
	
	function scrape($target_url, $counter, $needle, $arrayname, $cost){
	$arrayname = array();
	$counter++;
	$html = new simple_html_dom();
	$html->load_file($target_url);
	foreach($html->find('a') as $link)
	{
	if (strpos($link,'http://www.amazon.com') == false AND !strpos($link,'http://www')) {
	$newurl = "http://www.amazon.com/" . urldecode($link->href);
	}else{
	$newurl = $link->href;
	}
	if (!strpos($link,'http://www.amazon.com')){	
		continue;
	}
	$pattern  = '/' . implode('|', array_map('preg_quote', $needle)) . '/i';
    if(preg_match($pattern, $link)) {
    continue;
    }
			$mysql_host = '127.0.0.1';
		$mysql_user = 'root';
		$mysql_password = '';
		$mysql_database = 'updayte';
		$con = mysqli_connect($mysql_host, $mysql_user, $mysql_password);
				mysqli_select_db($con, $mysql_database);
			array_push($arrayname, $newurl);
			echo '$newurl: ' . $newurl;
			echo '<br/>';
			echo '$link->href Decode: ' . urldecode($link->href);
			echo '<br/><br/>';
			$r4 = mysqli_query($con, "SELECT * FROM `links` WHERE url='$newurl'");
			echo mysqli_num_rows($r4);
			if (mysqli_num_rows($r4) == 0) {
			mysqli_query($con, "INSERT INTO `links`(url,original,parent,cost) VALUES ('$newurl','$link->href','$target_url','$cost')");
			mysqli_close($con);
			}
	}
	return $arrayname;
	}
	$arrayname = 'arrayname' . $counter;
	$counter++;
	$arrayname = scrape($url, $counter, $needle, $arrayname, $cost);
	
	echo '<pre>';
	print_r($arrayname);
	echo '</pre>';
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