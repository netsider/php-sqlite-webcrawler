
<?php

// Make it search the current array.
	ini_set('session.hash_function','whirlpool');
	session_start();
	include_once('simple_html_dom.php');
	
	$conn = mysqli_connect('127.0.0.1', 'root', '', 'updayte');
	
	function crawl($target_url, $mhave, $mnot, $rurl, $index=0){
		if (strpos($target_url,'www.amazon.com') == 0 AND strpos($target_url,'http://') == 0) { // Add URL prefix if not exist
			$target_url = 'http://' . $rurl . $target_url;
			}else{
			$target_url = $target_url;
		}
		$html = new simple_html_dom();
		$html->load_file($target_url);
		$array = array();
		foreach($html->find('a') as $link)
		{
			if (empty($link->href) || empty($link)){
				continue;
			}
			if (strpos($link->href,'www.amazon.com') == 0 AND strpos($link->href,'http://') == 0) { // Add Link Prefix if not exist
				$url = 'http://' . $rurl . urldecode($link->href);
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
			$index++;
			echo '</font>';
		}
	return $array;
	}
	
	$exclude = ['.mx', '.br', '.au', 'https', 'Media-Player', 'redirect', 'product-reviews', 'services.amazon', 'aws.amazon', '#', 'fresh.amazon', 'nav_a', 'onload=', 'void(0)', 'adobe.com', 'javascript', 'footer_logo', 'pd_pyml_rhf', 'gno_joinprmlogo', 'ref=gno_logo', 'Thread=', 'customer-media', 'ref=nav_logo', 'nav_joinprmlogo', 'access', 'ntpoffrw'];
	$include = ['www.amazon.com'];
	$root_url = 'www.amazon.com';

	if (!isset($index)){
		$index = 0;
	}
	if(isset($_SESSION['data'])){
		$data = $_SESSION['data'];
	}
	
	if(isset($_POST['plus'])){
		next_index('links');
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	if(isset($_POST['minus'])){
		prev_index('links');
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	if(isset($_POST['clear'])){
		session_destroy();
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	if(isset($_SESSION['data'])){
		$index = count($_SESSION['data']);
	}
	if (!isset($url)){
	$url = 'http://www.amazon.com';
	}
?>
	<html lang = "en">
	<head>
		<meta charset = "utf-8">
	</head>
	<body><center>
	<form method="post">
	<table border="1" width="50%" bordercolor="blue">
	<tr><td colspan=4>URL: <input type="text" size="75" name="url" value=
	<?PHP
	echo '"' . trim($url) . '"';
	?>
	></tr></td>
	<tr>
	<td align=center><?PHP if (isset($index)){echo 'Links in Session: ' . $index; } ?><td colspan=1 align=center></td><td align=center>Database</td>
	</tr>
		<tr>
			<td align=center><input type="submit" name="run" value="Crawl URL" /><input type="submit" name="runrecursive" value="Crawl URLS in Array" /><input type="submit" name="clear" value="Clear Session" /><input type="submit" name="show" value="Show All in Session" /></td>
			<td><input type="submit" name="save" value="Save" /></td>
		</tr>
	</table>
	</form>
	<?php  
		echo 'URL Crawled: ' . $url;
	?>
	</center>
	</body>
</html>
<?PHP
if (isset($_POST['run'])){
if(isset($_POST['url'])){
	$url = trim($_POST['url']);
	}

	$array1 = crawl($url, $include, $exclude, $root_url, $index);
	echo '<pre>';
	print_r($array1);
	echo '</pre>';
	if (isset($data)){
		$_SESSION['data'] = array_merge($data, $array1);
		$data = $_SESSION['data'];
	}else{
		$_SESSION['data'] = $array1;
		$data = $_SESSION['data'];
	}
	}
	
	if (isset($_POST['runrecursive'])){
	$data = $_SESSION['data'];
	foreach($data as $d){ // Loop through each URL in browser session;
	$d = $d['original'];
	$array1 = crawl($d, $include, $exclude, $root_url, $index); // Crawl URL using function crawl() above;
	echo '<pre>';
	print_r($array1);
	echo '</pre>';
	if (isset($data)){
		$_SESSION['data'] = array_merge($data, $array1);
		$data = $_SESSION['data'];
	}else{
		$_SESSION['data'] = $array1;
		$data = $_SESSION['data'];
	}}}
	
		
		echo '<br/>';
		if(isset($_POST['show'])){
		echo '<pre>';
		echo '$_SESSION[data]';
		$data = $_SESSION['data'];
		var_export($data);
		echo '</pre>';
		}
		
		$count1 = 0;
		if (isset($_POST['save'])){
		$data = $_SESSION['data'];
		
		foreach($data as $d){
		$url = $d['url'];
		$parent = $d['parent'];
		$original = $d['original'];
		
		$firstpart = substr($url,22, 1);
		ctype_upper($firstpart);
		if (ctype_upper($firstpart)){
		$r4 = mysqli_query($conn, "SELECT * FROM `newlinks` WHERE url = '$url'");
				if (mysqli_num_rows($r4) === 0) 
				{
				// mysqli_query($conn, "INSERT INTO `links`(url,original,parent) VALUES ('$url','$original','$parent')");
				mysqli_query($conn, "INSERT INTO `newlinks`(url) VALUES ('$url')");
				echo 'Row Inserted: '  . $original . '<br/>';
				$count1 = $count1 + 1;
		}else{
		echo '<b>ERROR - Already Exists!</b><br/>';
		}
		}}
		echo '<b>Rows Inserted: ' . $count1 . '</b><br/>';
		}
		mysqli_close($conn);
		echo "<br/>";
?>
