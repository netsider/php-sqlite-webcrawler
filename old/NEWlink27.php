<?php
	session_start();
	$page_counter = read('current2.txt');
	
	if(isset($data)){
	$index = count($data);
	}else{
	$index = 0;
	}
	
	function write($fn, $text){
		$file = fopen($fn, 'w');
		fwrite($file, $text);
		fclose($file);
	}
	function read($fn){
	$file = fopen($fn,"r");
	$contents = fread($file,filesize($fn));
	fclose($file);
	return $contents;
	}
	if(isset($_POST['plus'])){
	write('current2.txt', $page_counter + 1);
	}
	if(isset($_POST['minus'])){
	if ($page_counter > 1){
	write('current2.txt', $page_counter - 1);
	}}
		if(isset($_POST['submit'])){
		if(isset($_SESSION)){
			$data = $_SESSION['data'];
		}
	$index = count($data);
	}else{
		$index = 0;
	}
?>
	<html lang = "en">
	<head>
		<meta charset = "utf-8">
	</head>
	<body><center>
	<form method="post">
	<table border="1" width="25%" bordercolor="blue">
	<tr>
	<td></td><?PHP if (isset($index)){echo 'Count: ' . $index; } ?><td><td></td></td>
	<td></td>
	</tr>
		<tr>
			<td align=center><input type="submit" name="submit" value="Run" /></td>
			<td align=center><input type="submit" name="plus" value="+" /></td>
			<td align=center><input type="submit" name="minus" value="-" /></td>
		</tr>
	</table>
	</form>
	<?php  $urla = $_SESSION['data'];
	echo $urla[$page_counter]['url'];
	?>
	</center>
	</body>
</html>
<?PHP
	include_once('simple_html_dom.php');
	$url = 'http://www.amazon.com/movies';
	$exclude = ['.mx', '.br', '.au', 'https', 'Media-Player', 'redirect', 'product-reviews', 'services.amazon', 'aws.amazon', '#', 'fresh.amazon', 'nav_a', 'onload='];
	$include = ['www.amazon.com'];
	$root_url = 'http://www.amazon.com';
	function crawl($target_url, $mhave, $mnot, $rurl, $index){
		$html = new simple_html_dom();
		$html->load_file($target_url);
		$array= array();
		global $count;
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
			$array[$index]['url'] = $url;
			$array[$index]['parent'] = $target_url;
			$array[$index]['original'] = $link_href;
			$array[$index]['scraped'] = 0;
			$array[$index]['crawled'] = 0;
			$index++;
			echo '</font>';
		}
	return $array;
	}
		if(empty($index)){
		$index = 0;
		}
	// Make it take the next URL in the array, and increment it each time.
		$array1 = crawl($url, $include, $exclude, $root_url, $index);
	
		echo '<pre>';
		var_export($_SESSION);
		echo '</pre>';
	
		if (isset($data)){
		$_SESSION['data'] = array_merge($data, $array1);
		echo '<pre>';
		// var_export($data);
		echo '</pre>';
		}else{
		$_SESSION['data'] = $array1;
		}
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
