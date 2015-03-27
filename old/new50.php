<?php
	ini_set('session.hash_function','whirlpool');
	session_start(); session_id();
	include_once('simple_html_dom.php');
	
	$conn = mysqli_connect('127.0.0.1', 'root', '');
	$database = 'updayte';
	mysqli_select_db($conn, $database);
	
	function new_index($table){
		global $conn;
		$conn = mysqli_connect('127.0.0.1', 'root', '');
		$database = 'updayte';
		mysqli_select_db($conn, $database);
		$q = "SELECT * FROM `links` WHERE paged = 1";
		$result = mysqli_query($conn, $q);
		$data = mysqli_fetch_row($result);
		return $data;
	}
	
	function next_index($table){
	$table = "links";
		global $conn;
		$q = 'SELECT * FROM `links` WHERE paged != 0'; // Gets the True Row
		$result = mysqli_query($conn, $q);
		$data = mysqli_fetch_row($result);
		$counter = $data[0];
		$v = "UPDATE $table SET paged = 0 WHERE id = '$counter'"; // Sets OLD ID to Zero
		mysqli_query($conn, $v); 
		$w = "select MAX(id) from $table";
		$max_id = mysqli_fetch_row(mysqli_query($conn, $w));
		$next_id = $counter + 1; 
		if($next_id > $max_id[0]){
		$next_id = 1;
		}
		$v = "UPDATE $table SET paged = 1 WHERE id = '$next_id'";
		mysqli_query($conn, $v); 
		$q = "SELECT * FROM `links` WHERE id = '$next_id' LIMIT 1";
		$result = mysqli_query($conn, $q);
		return $result;
	}
		function prev_index($table){
		$table = "links";
		global $conn;
		$q = 'SELECT * FROM `links` WHERE paged != 0'; // Gets the True Row
		$result = mysqli_query($conn, $q);
		$data = mysqli_fetch_row($result);
		$counter = $data[0];
		$v = "UPDATE $table SET paged = 0 WHERE id = '$counter'"; // Sets OLD ID to Zero
		mysqli_query($conn, $v); 
		$w = "select MAX(id) from $table";
		$max_id = mysqli_fetch_row(mysqli_query($conn, $w));
		$next_id = $counter - 1; 
		if($next_id == 1){
		$next_id = 1;
		}
		$v = "UPDATE $table SET paged = 1 WHERE id = '$next_id'";
		mysqli_query($conn, $v);  
		$q = "SELECT * FROM `links` WHERE id = '$next_id' LIMIT 1"; 
		$result = mysqli_query($conn, $q);
		return $result;
	}

	function crawl($target_url, $mhave, $mnot, $rurl, $index=0){
		if (strpos($target_url,'www.amazon.com') == 0 AND strpos($target_url,'http://') == 0) { // Add URL prefix if not exist
			$target_url = $rurl . $target_url;
			}else{
			$target_url = $target_url;
		}
		$html = new simple_html_dom();
		$html->load_file($target_url);
		$array = array();
		global $count;
		foreach($html->find('a') as $link)
		{
			if (empty($link->href) || empty($link)){
				continue;
			}
			if (strpos($link->href,'www.amazon.com') == 0 AND strpos($link->href,'http://') == 0) { // Add Link Prefix if not exist
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
			$index++;
			echo '</font>';
		}
	return $array;
	}
	
	
	$exclude = ['.mx', '.br', '.au', 'https', 'Media-Player', 'redirect', 'product-reviews', 'services.amazon', 'aws.amazon', '#', 'fresh.amazon', 'nav_a', 'onload=', 'void(0)', 'adobe.com', 'javascript', 'footer_logo', 'pd_pyml_rhf', 'gno_joinprmlogo', 'ref=gno_logo', 'Thread=', 'customer-media'];
	$include = ['www.amazon.com'];
	$root_url = 'http://www.amazon.com';

	
	if (!isset($index)){
		$index = 0;
	}
	if(isset($_SESSION['data'])){
		$data = $_SESSION['data'];
	}
	
	//$_POST
	if(isset($_POST['plus'])){
		// write('current2.txt', $current_index + 1);
		next_index('links');
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	if(isset($_POST['minus'])){
		prev_index('links');
		// if ($current_index > 0){
		// write('current2.txt', $current_index - 1);
		header('Location: ' . $_SERVER['PHP_SELF']);
	// }
	}
	if(isset($_POST['clear'])){
		$_SESSION = NULL;
		session_destroy();
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	if(isset($_SESSION['data'])){
		$index = count($_SESSION['data']);
	}
	if(isset($_POST['run'])){
	if(isset($_POST['url'])){
	$url = $_POST['url'];
	}
		// if (isset($_SESSION['data'])){
			// echo 'Using Session URL';
			// $url = $_SESSION['data'][$current_index]['original'];
			// $url = 'http://www.amazon.com';
	// }else{
			// $url = 'http://www.amazon.com/movies';
	// }
	
	$array1 = crawl($url, $include, $exclude, $root_url, $index);
	
	if (isset($data)){
		$_SESSION['data'] = array_merge($data, $array1);
		$data = $_SESSION['data'];
	}else{
		$_SESSION['data'] = $array1;
		$data = $_SESSION['data'];
	}
	}
	$current_index = new_index('links');
	$current_index = $current_index[0];
?>
	<html lang = "en">
	<head>
		<meta charset = "utf-8">
	</head>
	<body><center>
	<form method="post">
	<table border="1" width="50%" bordercolor="blue">
	<tr><td colspan=4>URL: <input type="text" size="75" name="url" value="http://www.amazon.com/movies"></tr></td>
	<tr>
	<td align=center><?PHP if (isset($index)){echo 'Links Gathered: ' . $index; } ?><td colspan=2 align=center><?PHP echo 'Counter Position: ' . $current_index; ?></td><td align=center>Database</td>
	</tr>
		<tr>
			<td align=center><input type="submit" name="run" value="Run" /><input type="submit" name="clear" value="Clear" /></td>
			<td align=center><input type="submit" name="plus" value="+" /></td>
			<td align=center><input type="submit" name="minus" value="-" /></td>
			<td><input type="submit" name="save" value="Save" /></td>
		</tr>
	</table>
	</form>
	<?php  
		echo 'Target URL: '; 
		echo '<span style="font-size:8px"><pre>';
		$url = new_index('links');
		print_r($url[2]);
		echo '</pre></span>';
	?>
	</center>
	</body>
</html>
<?PHP
if (isset($_POST['run'])){
		echo '<br/>';
		if(isset($_SESSION['data'])){
		echo '<pre>';
		echo '$_SESSION[data]';
		var_export($_SESSION['data']);
		echo '</pre>';
		}
		echo '<br/>';
		}
		
		
		if (isset($_POST['save'])){
		$data = $_SESSION['data'];
		foreach($data as $d){
		$url = $d['url'];
		$parent = $d['parent'];
		$original = $d['original'];
		
		$r4 = mysqli_query($conn, "SELECT * FROM `links` WHERE original = '$original'");
		$count1 = 0;
						if (mysqli_num_rows($r4) < 1) 
				{
				$count1++;
			mysqli_query($conn, "INSERT INTO `links`(url,original,parent) VALUES ('$url','$original','$parent')");
			echo 'Row Inserted!<br/>';
		}else{
		echo '<b>ERROR - Already Exists!</b><br/>';
		}
		}
		echo '<b>Rows Inserted: ' . $count1 . '</b><br/>';
		}
		mysqli_close($conn);
		echo "<br/>";

		
?>
