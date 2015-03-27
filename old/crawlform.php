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
			if ($current_index > 0){
			write('current2.txt', $current_index - 1);
			}
		}
		if(isset($_POST['clear'])){
			unset($_SESSION['data']);
			write('current2.txt', 0);
		}
		if(isset($_SESSION['data'])){
			$index = count($_SESSION['data']);
		}
	}
	//END POST
	if(isset($_SESSION['data'])){
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
	<td><?PHP if (isset($index)){echo '$index: ' . $index; }else{ $index=0; } ?><td colspan=2><?PHP echo 'page_counter: ' . $current_index; ?></td>
	</tr>
		<tr>
			<td align=center><input type="submit" name="submit" value="Run" /><input type="submit" name="clear" value="Clear" /></td>
			<td align=center><input type="submit" name="plus" value="+" /></td>
			<td align=center><input type="submit" name="minus" value="-" /></td>
		</tr>
	</table>
	</form>
	<?php  
	if (isset($_SESSION['data']))
	{
	$next_url = $_SESSION['data'];
	echo '$nexturl:' . $data[$current_index]['url'];
	echo '<br/>';
	echo 'Two: '; 
	print_r($_SESSION['data'][$current_index]['url']);
	}
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
	
		echo '<pre>';
		echo '$_SESSION';
		echo '<br/>';
		var_export($_SESSION);
		echo '</pre>';
		
		// echo '<pre>';
		// echo '$_SESSION[data]';
		// echo '<br/>';
		// var_export($_SESSION['data']);
		// echo '</pre>';
	
		if (isset($data)){
		$_SESSION['data'] = array_merge($data, $array1);
		echo 'DATA SET';
		}else{
		$_SESSION['data'] = $array1;
		echo 'DATA NOT SET';
		}
		echo '<br/>';
		if (isset($_SESSION)){
		echo 'SESSION SET';
		}else{
		echo 'SESSION NOT SET';
		}
		
		
?>
