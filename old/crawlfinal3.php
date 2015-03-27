<?php
	//AMAZON WEB CRAWLER
	//BY: RUSSELL ROUNDS (NOT FOR USE WITHOUT PERMISSION)
	//WRITTEN IN PHP FOR PHP5.5, MYSQL, NGINX (WINDOWS) 
	//7-11-2014
	
	//[CONSTANTS]
	const DATAB = 'updayte'; // Quicker Constant
	//[FUNCTIONS]
	function access($host = '127.0.0.1'){
		$c = mysqli_connect($host, 'root', '');
		mysqli_select_db($c, DATAB);
		return $c;
	}
	function array_push_assoc($a, $k, $v){
		$a[$k] = $v;
		return $a;
	}
	function write($fn, $text){
		$file = fopen($fn, 'w');
		fwrite($file, $text);
		fclose($file);
	}
	function read($fn){
		$f = fopen($fn,"r");
		$contents = fread($f,filesize($fn));
		fclose($f);
		return $contents;
	}
	function valid_date($input){
		$date_format = 'Y/m/d';
		$input = trim($input);
		$time = strtotime($input);
		$is_valid = date($date_format, $time) === $input;
		return ($is_valid ? 'yes' : 'no');
	}
	function title_clean($badtitle){
		$newtitle = str_replace("DVD", "", $badtitle);
		$newtitle = str_replace("UltraViolet", "", $newtitle);
		$newtitle = str_replace("+", "", $newtitle);
		$newtitle = str_replace("'", "", $newtitle);
		$newtitle = str_replace("Digital HD", "", $newtitle);
		$newtitle = str_replace("Blu-ray", '', $newtitle);
		$newtitle = str_replace("[", '', $newtitle);
		$newtitle = str_replace("]", '', $newtitle);
		$newtitle = str_replace("/", '', $newtitle);
		$newtitle = str_replace("Combo Pack", '', $newtitle);
		$newtitle = str_replace("-", '', $newtitle);
		$newtitle = str_replace("(2014)", '', $newtitle);
		$newtitle = str_replace("(2013)", '', $newtitle);
		$newtitle = str_replace("3D", '', $newtitle);
		$newtitle = str_replace("BluRay", '', $newtitle);
		$newtitle = str_replace(")", '', $newtitle);
		$newtitle = str_replace("(", '', $newtitle);
		$newtitle = str_replace("DIGITAL HD with", '', $newtitle);
		return $newtitle;
	}
	function platform_clean($array){
		if($val1 = array_search('&nbsp;', $array)){
			unset($array[$val1]);
		}
		if(in_array('Multi-Format', $array)){
			$val2 = array_search('Multi-Format', $array);
			unset($array[$val2]);
		}
		$array = array_values($array);
		return $array;
	}
	function date_clean($input_date){
		$date2 = str_replace("This title will be released on ", "", $input_date);
		$date3 = str_replace(",", "", $date2);
		$date4 = str_replace(".", "", $date3);
		$date5 = ltrim($date4,'<span class="availOrange">');
		$date6 = rtrim($date5,'</span>');
		$date7 = trim($date6);
		$db_date = date('Y/m/d', strtotime($date7));
		return $db_date;
	}
	$lastdb = read('lastdb.txt');
	$lastdb2 = read('lastdb2.txt');
	$page_counter = read('current.txt');
	//[$SCRIPT VARIABLES]
	date_default_timezone_set("America/New_york");
	ini_set('display_errors', 'On');
	ini_set('max_execution_time', 0);
	
	?>
<html><head></head><body>
<center><b><font color="green">Amazon Web Scraping Utility: By Russell Rounds</b></font><br/><br/>
<form method="post"><table border="1" width="40%" bordercolor="green">
	<tr><td># to Scrape:</td><td>Place Into Database: 
	<?php
	$q = "SHOW TABLES";
	$con = access();
	$result = mysqli_query($con, $q);
	echo '<select name="table_target">';
	while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
	// if($row[0] != 'queue' AND $row[0] != 'users'){
	echo '<option value=' . $row[0];
	if ($lastdb == $row[0]){
	echo ' selected="selected"';
	}			
	echo '>' . $row[0] . '</option>';
	}
	?>
	</select></td></tr>
	<tr><td align=center><input type="text" name="page" size="10" value="<?php echo $page_counter ?>"/></td><td>
	Read from: 
	<select name="table_source">
	<?php
	$q = "SHOW TABLES";
	$con = access();
	$result = mysqli_query($con, $q);
	while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
	// if($row[0] != 'queue' AND $row[0] != 'users'){
	echo '<option value=' . $row[0];
	if ($lastdb2 == $row[0]){
	echo ' selected="selected"';
	}			
	echo '>' . $row[0] . '</option>';
}
	?></select>
	</td></tr>
	<tr><td align=center></td>
	<td align=center><input type="submit" name="submit" value="Start/Begin" /></td></tr></table></form>
	</center>
<?php
		include_once('simple_html_dom.php');
		if (isset($_POST['table_target'])){		
			$current_table = $_POST['table_target'];
			write('lastdb.txt', $current_table);
		}
		if (isset($_POST['table_source'])){	
			$current_source = $_POST['table_source'];
			write('lastdb2.txt', $current_source);
		}
		if (isset($_POST['page'])){		
		$page_counter = $_POST['page'];
		}
		
		if (isset($_POST['submit'])){
		
		$con = access('127.0.0.1');
		$r4 = mysqli_query($con, "SELECT * FROM `$current_source` WHERE scraped = 0 LIMIT $page_counter");
		
		foreach($r4 as $link)
		{		
			
			$newurl = urldecode($link['url']);
			$newpage = file_get_html($newurl);
			$original = $link['original'];
			$id = $link['id'];
			mysqli_query($con, "UPDATE $current_source SET scraped = 1 WHERE id = '$id'");
		    
			
			echo '<font color="blue">' . 'Crawling URL for Links: ' . $newurl . '<br/></font>';
			echo '<pre>';
			print_r($link);
			echo '</pre>';
				
				
				$ptitle = $newpage->find('span[id=btAsinTitle]');
				$auth = $newpage->find('div[class=subTitle ]');
				$avail = $newpage->find('span[class=availOrange]');
				$platform = $newpage->find('div[class=binding-platform]');
				$platform2 = $newpage->find('td[class=tmm_videoMetaBinding]');
				if (strpos(implode("-", $avail),'released') > 0 AND !strpos(implode("-", $avail),'been'))
				{
					// [[DATE]]
					$db_date = date_clean($avail[0]);
					echo '<b>' . $db_date . '</b>';
					
					// [[PLATFORM]]
					$platform_array = array();
					if(empty($platform2)){
						$platform2 = $platform;
					}
					for ($x=0; $x<=3; $x++) {
						if (!empty($platform2[$x]->plaintext)){
							array_push($platform_array, trim($platform2[$x]->plaintext));
					}}
					$platform = platform_clean($platform_array);
							
					// AUTHORS
					$auth_db_array = array();
					foreach($auth as $author)
					{
						for($i=0;$i <= 4;$i++)
						{
							$authorname = $author->find('a[href]', $i);
							if(!empty($authorname))
							{
								if(in_array($authorname, $auth_db_array) == false)
								{
									$key_name = 'author' . (1 + $i);
									$auth_db_array = array_push_assoc($auth_db_array, $key_name, $authorname->plaintext);
								}
							}	
						}	
					}
					// FINAL VARIABLES TO GO INTO DATABASE
					if(isset($auth_db_array['author1'])){
						$author = $auth_db_array['author1'];
					}else{
						$author = '';
					}
					if(isset($auth_db_array['author2'])){
						$author2 = $auth_db_array['author2'];
					}else{
						$author2 = '';
					}
					if(isset($auth_db_array['author3'])){
						$author3 = $auth_db_array['author3'];
					}else{
							$author3 = '';
					}
					if(isset($auth_db_array['author4'])){
						$author4 = $auth_db_array['author4'];
					}else{
						$author4 = '';
					}
					$uncleantitle = $ptitle[0]->plaintext;
					$db_title = title_clean($ptitle[0]->plaintext); 
					$db_source = $link->href;
					if(!empty($platform_array)){
						$platform_array = NULL;
					}
					$con = access('127.0.0.1');
					foreach ($platform as $p)
					{ 
						$r4 = mysqli_query($con, "SELECT * FROM `$current_table` WHERE title='$db_title' AND (`platform`='$p')");
						if (!mysqli_num_rows($r4) > 0) 
						{
							mysqli_query($con, "INSERT INTO `$current_table`(title,platform,date,source,author,author2,author3,author4) VALUES ('$db_title','$p','$db_date','$db_source','$author','$author2','$author3','$author4')");
							echo '<hr><font color=green>' . $db_title . ' inserted Into Database Successfully!</font><br/>';
							mysqli_query($con, "UPDATE $current_table SET find ='$findurl' WHERE title = '$db_title'");
						}elseif(!$r4){
						echo '<hr><font color=red>Error</font><br>';
						}
						else{
								echo '<hr><font color=red>' . $db_title . ' not Entered - Record Already Exists</font><br/>';
						}
					}	
					mysqli_close($con);
				}
			}	
		}
//[TO-DO]
// Make Find strings variables instead.
// Make input fields/post data for find variables.
// Make framework for people to include on their site
// Use the webbrowser plugin and see how it can help
// Use Javascript to make the page dynamic on the webbrowser end.
// Begin Making things more "modular"
// Figure out someway for users to do this, or submit links.
//URLencode different URLs into the dropdown box.
// Make Crawler Able to Crawl every PHP example, and add other templates for people to see.
// Make Crawler Return Results Entered Into Database
// Display Last Results Entered
// Make Templates/Presets by SITE (Amazon, CNN, etc)
// USe Javascript to show/hide certain forms.

// [REMOVED/OLD CODE]
	// $r = mysqli_query($con, "SELECT * FROM `$current_table` WHERE title='$db_title'");
	// $r2 = mysqli_query($con, "SELECT platform FROM $current_table WHERE title='$db_title'");
	// $r3 = mysqli_query($con, "SELECT * FROM `$current_table` WHERE title='$db_title' AND (`platform` = '$platform[0]' OR `platform` = '$platform[1]')");
	// mysqli_query($con, "UPDATE $current_table SET cat ='$actual_table' WHERE title = '$db_title'");

?>
</body>	
</html>