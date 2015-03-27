<?php
	function array_push_assoc($array, $key, $value){
		$array[$key] = $value;
		return $array;
	}
	function write($fn, $link){
		$file = fopen($fn, 'a');
		fwrite($file, $link . "\r\n");
	}
	function valid_date($input){
		$date_format = 'Y/m/d';
		$input = trim($input);
		$time = strtotime($input);
		$is_valid = date($date_format, $time) === $input;
		return ($is_valid ? 'yes' : 'no');
	}
	function next_id($table){
		$conn = mysqli_connect('127.0.0.1', 'root', '');
		$database = 'updayte';
		mysqli_select_db($conn, $database);
		$q = "select MAX(id) from $table";
		$result = mysqli_query($conn, $q);
		$data = mysqli_fetch_array($result);
		return ($data[0] + 1);
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
	date_default_timezone_set("America/New_york");
	ini_set('display_errors', 'On');
	ini_set('max_execution_time', 0);
?>
<html><head></head><body>
<center><b><font color="green">Amazon Web Scraping Utility: By Russell Rounds</b></font><br/><br/>
<form method="post">
<table border="1" width="25%" bordercolor="green">
	<tr>
		<td>URL:</td>
		<td align=center><input type="text" name="url" size="50"/></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align=center><input type="submit" name="submit" value="Scrape URL" /></td>
	</tr>
</table>
</form>
</center>

<?php
	if (isset($_POST['submit']))
	{	
		$target_url = htmlspecialchars($_POST['url']);
		include_once('simple_html_dom.php');
		$html = new simple_html_dom();
		$html->load_file($target_url);
		echo '<font color="blue">' . 'Crawling URL for Links: ' . $target_url . '<br/></font>';
		foreach($html->find('a') as $link)
		{
				$newurl = $link->href;
				if (strpos($link->href,'www.amazon.com') == false){
				continue;
				} 
				if (strpos($link->href,'amazon.com:443') == true){
				continue;
				}
				if(strpos($link,'class="grey"') == true && strpos($link,'class="bld red fixed14"') == true){
				continue;
				}
				if(strpos($link,'img') == true){
				continue;
				}
				if(strpos($link,'productPromo') == true){
				continue;
				}
				$current_table = 'queue';
				$con = mysqli_connect('127.0.0.1', 'root', '');
				$database = 'updayte';
				mysqli_select_db($con, $database);
				$findurl = substr($newurl, 0, 60);
				$r = mysqli_query($con, "SELECT * FROM `$current_table` WHERE find='$findurl'");
				if (!$r || mysqli_num_rows($r) > 0) {
				mysqli_close($con);
				echo '<br/>Skipping Entry Already in Database!<br/>';
				echo 'FindURL: ' . $findurl . '<br/>';
				continue;
				} else
				{
				$newpage = file_get_html($newurl);
				$ptitle = $newpage->find('span[id=btAsinTitle]');
				$auth = $newpage->find('div[class=subTitle ]');
				$avail = $newpage->find('span[class=availOrange]');
				$platform = $newpage->find('div[class=binding-platform]');
				$platform2 = $newpage->find('td[class=tmm_videoMetaBinding]');
				if (strpos(implode("-", $avail),'released') == true AND !strpos(implode("-", $avail),'been'))
				{
					echo '<hr/>';
					// DATE
					$db_date = date_clean($avail[0]);
					// if(valid_date($db_date) == 'yes'){
						// echo '  <font color=green><b>[Database-ready]</b></font>';
						// write('good.txt', $newurl);
						// }else{
						// echo '  <font color=red><b>[Date NOT Valid for DB!]</b></font>';
					// }
			
					// PLATFORM		
					$platform_array = array();
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
								}}}}
						
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
					$db_title = title_clean($uncleantitle); 
					$db_source = $link->href;
					
						if(!empty($platform_array)){ // Write An Entry for Each Platform
						$con = mysqli_connect('127.0.0.1', 'root', '');
						$database = 'updayte';
						mysqli_select_db($con, $database);
						$r = mysqli_query($con, "SELECT * FROM `$current_table` WHERE title='$db_title'");
						$r2 = mysqli_query($con, "SELECT platform FROM $current_table WHERE title='$db_title'");
						$r3 = mysqli_query($con, "SELECT * FROM `$current_table` WHERE title='$db_title' AND (platform != '$platform[0]' OR platform != '$platform[1]')");
						echo '<pre>';
						echo print_r($r2);
						echo '</pre>';
						if (!$r || mysqli_num_rows($r) < 1) {
						foreach ($platform as $p){
						mysqli_query($con, "INSERT INTO `$current_table`(title,platform,date,source,author,author2,author3,author4) VALUES ('$db_title','$p','$db_date','$db_source','$author','$author2','$author3','$author4')");
						$actual_table = 'movies';
						mysqli_query($con, "UPDATE $current_table SET cat ='$actual_table' WHERE title = '$db_title'");
						echo $db_title . '<font color=blue>Inserted Successfully Into Database!</font><br/>';
						mysqli_query($con, "UPDATE $current_table SET find ='$findurl' WHERE title = '$db_title'");
						}}else{
						echo $db_title . '<font color=red> not Entered - Record Already Exists</font><br/>';
						}
						mysqli_close($con);
						}
				}}			
		}
	}
?>
</body>	
</html>