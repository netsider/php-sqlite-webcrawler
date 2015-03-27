<?php
	session_start();
	if(isset($_POST['submit'])){
	$_SESSION['data'] = serialize($array1);
	}
	if(isset($_SESSION)){
		$data = unserialize($_SESSION['data']);
	}else{
		$count = 0;
	}
?>
<html lang = "en">
	<head>
		<meta charset = "utf-8">
	</head>
	<body><center>
	<form method="post">
	<table border="1" width="25%" bordercolor="green">
	<tr>
	<td></td><td></td>
	</tr>
		<tr>
			<td>Title:</td>
			<td align=center><input type="submit" name="submit" value="Run" /></td>
		</tr>
	</table>
	</form>
	</body>
</html></center>
<?php
	include_once('simple_html_dom.php');
	$url = 'http://www.amazon.com/movies';
	$exclude = ['.mx', '.br', '.au', 'https', 'Media-Player', 'redirect', 'product-reviews', 'services.amazon', 'aws.amazon', '#', 'fresh.amazon', 'nav_a', 'onload='];
	$include = ['www.amazon.com'];
	$root_url = 'http://www.amazon.com';
	function crawl($target_url, $mhave, $mnot, $rurl){
		global $count;
		$html = new simple_html_dom();
		$html->load_file($target_url);
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
			$array[$count]['url'] = $url;
			$array[$count]['parent'] = $target_url;
			$array[$count]['original'] = $link_href;
			$array[$count]['scraped'] = 0;
			$array[$count]['crawled'] = 0;
			$count++;
			echo '</font>';
		}
	return $array;
	}
		echo '<br/>';
		$array1= crawl($url, $include, $exclude, $root_url);
		
		echo '<pre>';
		var_export($data);
		echo '</pre>';
		// $con = mysqli_connect('127.0.0.1', 'root', '', 'updayte');
		// echo 'array_length: ' . $a_length = count($array1) - 1;
		// for ($x=0; $x<=$a_length; $x++) {
		  // echo 'URL(' . $x . ')->' . $array1[$x]['url'] . '<br/><br/>';
		// }
		
		
		// foreach ($array1 as $L){
		// $cost = mysqli_query($con, "SELECT cost FROM `links` WHERE url='$L'"); // Get Cost of Current URL Being Read
		// $array2 = crawl($L);
		// }
	
			// $nextbatch = mysqli_query($con, "SELECT url FROM `links` WHERE cost='$cost'"); // Finds links with the cost from the first pass.
			// while($links = mysqli_fetch_array($nextbatch)){
			// foreach ($links as $L){
			
			// $r4 = mysqli_query($con, "SELECT * FROM `links` WHERE url='$url'");
			// echo mysqli_num_rows($r4);
			// if (mysqli_num_rows($r4) == 0) {
			// mysqli_query($con, "INSERT INTO `links`(url,original,parent,cost) VALUES ('$url','$link->href','$target_url','$cost')");
			// }
			
			// mysqli_query($con, "UPDATE $current_table SET find ='$findurl' WHERE title = '$db_title'");
?>