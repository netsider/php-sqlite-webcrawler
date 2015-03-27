<?php
	session_start();
	//FUNCTIONS -> START
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
	function crawl($target_url, $mhave, $mnot, $rurl, $index=0){
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
	//END FUNCTIONS
	//START POST
	$current_index = read('current2.txt');
	if(isset($_POST)){
		if(isset($_POST['submit'])){
		
		}
		if(isset($_POST['plus'])){
			write('current2.txt', $current_index + 1);
		}
		if(isset($_POST['minus'])){
			if ($current_index > 1){
			write('current2.txt', $current_index - 1);
			}
		}
		$index = count($_SESSION['data']);
	}
	//END POST
	if(isset($_SESSION)){
		$data = $_SESSION['data'];
	}	
	$current_index = read('current2.txt');
?>
	<html lang = "en">
	<head>
		<meta charset = "utf-8">
	</head>
	<body><center>
	<form method="post">
	<table border="1" width="25%" bordercolor="blue">
	<tr>
	<td></td><?PHP if (isset($index)){echo '$index: ' . $index; }else{ $index=0; } ?><td><td></td></td>
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
	echo 'page_counter: ' . $current_index;
	echo '<br/>';
	echo 'One:' . $urla[$current_index]['url'];
	// echo 'Two:' . $_SESSION[$current_index]['data'];
	echo 'Three:'; 
	print_r($_SESSION['data'][$current_index]['url']);
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

	// Make it take the next URL in the array, and increment it each time.
		$array1 = crawl($url, $include, $exclude, $root_url, $index);
	
		// echo '<pre>';
		// var_export($_SESSION);
		// echo '</pre>';
		
		echo '<pre>';
		var_export($_SESSION['data']);
		echo '</pre>';
	
		if (isset($data)){
		$_SESSION['data'] = array_merge($data, $array1);
		echo 'DATA SET';
		}else{
		$_SESSION['data'] = $array1;
		echo 'DATA NOT SET';
		}
?>
