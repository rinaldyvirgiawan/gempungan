<?php
class Ifunction extends CI_Model{

	public function __construct()
	{
        parent::__construct();
    }
	
	public function action_response($status, $form_id, $css, $message, $js = '')
	{
		return '<div class="'.$css.'">'.$message.'</div><script>iFresponse('.$status.', "'.$form_id.'");'.$js.'</script>';
	}
	
	public function slidedown_response($form_id, $css, $message)
	{
		return '<div class="'.$css.'">'.$message.'</div><script>jq("#'.$form_id.'").slideDown()</script>';
	}
	
	public function xlsBOF()
	{
		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
		return;
	}
	
	public function xlsEOF()
	{
		echo pack("ss", 0x0A, 0x00);
		return;
	}
	
	public function xlsWriteNumber($rows, $cols, $values)
	{
		echo pack("sssss", 0x203, 14, $rows, $cols, 0x0);
		echo pack("d", $values);
		return;
	}
	
	public function xlsWriteLabel($rows, $cols, $values )
	{
		$L = strlen($values);
		echo pack("ssssss", 0x204, 8 + $L, $rows, $cols, 0x0, $L);
		echo $values;
		return;
	}
	
	public function recaptcha($tp)
	{
		if($tp == 'secret'){
			return '6LcjVQcTAAAAADf9QmNhKXd9lRS7JIBBnoMQeUgX';
		}
		else return '6LcjVQcTAAAAAPR7yl2PY02C3KUeDPCCbkKdeFZl';
	}
	
	public  function encode($values)
	{
		$len=strlen($values); 
		for($i=0; $i < $len; $i++){
			$numeric[$i]=substr($values, $i, 1);
		}
		$arand[0]=rand(0, 700);
		srand((double)microtime() * 1000000);
		$random=rand(0, 8);
		$result=($random + 1) * 1000 + $arand[0];
		$result=$result."";
		for($i=1; $i <= $len; $i++){
			$random=rand(0, 8);
			$arand[$i]=($random + 1) * 1000 + $arand[0] + ord($numeric[$i - 1]); 
			$result=$result . $arand[$i];
		}
		return $result;
	}
	
	public  function decode($values)
	{
		$len=strlen($values);
		$lens=($len / 4) - 1;
		$arand[0]=substr($values, 0, 4);
		$arand[0]=$arand[0] % 1000;
		$result="";
		for($i=1; $i <= $lens; $i++){
			$arand[$i]=substr($values, $i * 4, 4);
			$arand[$i]=$arand[$i] % 1000;
			$arand[$i]=$arand[$i] - $arand[0]; 
			$result=$result . chr($arand[$i]);
		}
		return $result;
	}
	
	public function curl($url)
	{
		$init=curl_init($url);
		ob_start();
		curl_exec($init);
		$get_content=ob_get_contents();
		ob_end_clean();
		curl_close($init);
		return $get_content;
	}
	
	public function curl_file_get_contents($url)
	{
		$ch = curl_init();
		curl_setopt_array($ch,
			array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_COOKIE => NULL,
				CURLOPT_NOBODY => false
			)
		);
		$contents = curl_exec($ch);
		curl_close($ch);
		if($contents) return $contents; else return false;
	}

	public function token($tp, $id, $tipe)
	{
		if($tp == 'check'){
			
			$this->db('connect');
			$Qcheck = mysql_query("SELECT `user_id` FROM `ak_token` WHERE `token`='$id' AND `tipe`='$tipe'");
			if(mysql_num_rows($Qcheck)){
				$check = mysql_fetch_object($Qcheck);
				return $check->user_id;
			}
			else return false;
			$this->db('close');
			
		}elseif($tp == 'remove'){
			
			mysql_query("DELETE FROM `ak_token` WHERE `token`='$id' AND `tipe`='$tipe'");
			return true;
			
		}else{
			
			$token = md5(microtime().rand().'iF'.$id);
			mysql_query("INSERT INTO `ak_token` (`user_id`, `token`, `tipe`) VALUES ($id, '$token', '$tipe')");
			return $token;
			
		}
	}
	
	public function un_link($url)
	{
		if(file_exists($url)) unlink($url);
		return true;
	}
	
	public function upload($dir, $files_name, $files_tmp, $fn='')
	{
		$fileext = explode('.', $files_name);
		$file_ext = strtolower(end($fileext));
		
		$new_name = $fn ? $fn : md5(date("YmdHms").'_'.rand(100, 999));
		$new_file_name = $new_name.'.'.$file_ext;
		
		$file_path = $dir.$new_file_name;
		if(!in_array($file_ext, array('php','html'), true)){
			move_uploaded_file($files_tmp, $file_path);
			if(file_exists($file_path)){
				return $new_file_name;
			}
			else return false;
		}
		else return false;
	}
	
	public function last_id()
	{
		$last = mysql_fetch_object(mysql_query("SELECT LAST_INSERT_ID() AS `id`"));
		return $last->id;
	}
	
	public function pswd($str)
	{
		return md5(crypt($str.'54Lt', 'Developed by: me@irvanfauzie.com'));
	}
	
	public function paging($p=1, $page, $num_page, $num_record, $click='href', $total=1, $extra='')
	{
		$pnumber = '';
		echo '<div class="pagination"><ul class="list-nostyle">';
		if($p>1){
			$previous=$p-1;
			echo '<li><a '.$click.'="'.$page.$previous.$extra.'" title="Previous">&laquo;</a></li>';
		}
		if($p>3) echo '<li><a '.$click.'="'.$page.'1'.$extra.'">1</a></li>';
		for($i=$p-2;$i<$p;$i++){
		  if($i<1) continue;
		  $pnumber .= '<li><a '.$click.'="'.$page.$i.$extra.'">'.$i.'</a></li>';
		}
		$pnumber .= '<li class="active"><a>'.$p.'</a></li>';
		for($i=$p+1;$i<($p+3);$i++){
		  if($i>$num_page) break;
		  $pnumber .= '<li><a '.$click.'="'.$page.$i.$extra.'">'.$i.'</a></li>';
		}
		$pnumber .= ($p+2<$num_page ? '<li><a '.$click.'="'.$page.$num_page.$extra.'">'.$num_page.'</a></li>' : " ");
		echo $pnumber;
		if($p<$num_page){
			$next=$p+1;
			echo '<li><a '.$click.'="'.$page.$next.$extra.'" title="Next">&raquo;</a></li>';
		}
		if($total) echo '<span>Total: <b>'.$num_record.'</b> data</span>';
		echo '</ul></div>';
	}
	
public function dompdf_usage(){
  $default_paper_size = DOMPDF_DEFAULT_PAPER_SIZE;
  
  echo <<<EOD
  
Usage: {$_SERVER["argv"][0]} [options] html_file

html_file can be a filename, a url if fopen_wrappers are enabled, or the '-' character to read from standard input.

Options:
 -h             Show this message
 -l             List available paper sizes
 -p size        Paper size; something like 'letter', 'A4', 'legal', etc.  
                  The default is '$default_paper_size'
 -o orientation Either 'portrait' or 'landscape'.  Default is 'portrait'
 -b path        Set the 'document root' of the html_file.  
                  Relative urls (for stylesheets) are resolved using this directory.  
                  Default is the directory of html_file.
 -f file        The output filename.  Default is the input [html_file].pdf
 -v             Verbose: display html parsing warnings and file not found errors.
 -d             Very verbose: display oodles of debugging output: every frame 
                  in the tree printed to stdout.
 -t             Comma separated list of debugging types (page-break,reflow,split)
 
EOD;
exit;
}
	
	public function getoptions(){
		$opts = array();
		if($_SERVER["argc"] == 1) return $opts;
		$i = 1;
		while($i < $_SERVER["argc"]){
			
			switch($_SERVER["argv"][$i]){
				case "--help":
				case "-h":
				$opts["h"] = true;
				$i++;
				break;
				
				case "-l":
				$opts["l"] = true;
				$i++;
				break;
				
				case "-p":
				if( !isset($_SERVER["argv"][$i+1]) )
				die("-p switch requires a size parameter\n");
				$opts["p"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;
				
				case "-o":
				if( !isset($_SERVER["argv"][$i+1]) )
				die("-o switch requires an orientation parameter\n");
				$opts["o"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;
				
				case "-b":
				if( !isset($_SERVER["argv"][$i+1]) )
				die("-b switch requires a path parameter\n");
				$opts["b"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;
				
				case "-f":
				if( !isset($_SERVER["argv"][$i+1]) )
				die("-f switch requires a filename parameter\n");
				$opts["f"] = $_SERVER["argv"][$i+1];
				$i += 2;
				break;
				
				case "-v":
				$opts["v"] = true;
				$i++;
				break;
				
				case "-d":
				$opts["d"] = true;
				$i++;
				break;
				
				case "-t":
				if( !isset($_SERVER['argv'][$i + 1]) )
				die("-t switch requires a comma separated list of types\n");
				$opts["t"] = $_SERVER['argv'][$i+1];
				$i += 2;
				break;
				
				default:
				$opts["filename"] = $_SERVER["argv"][$i];
				$i++;
				break;
			}
		
		}
		return $opts;
	}
	
	public function get_tiny_url($url)  {  
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;
	}

	public function reArrayFiles(&$file_post) {

	    $file_ary = array();
	    $file_count = count($file_post['name']);
	    $file_keys = array_keys($file_post);

	    for ($i=0; $i<$file_count; $i++) {
	        foreach ($file_keys as $key) {
	            $file_ary[$i][$key] = $file_post[$key][$i];
	        }
	    }

	    return $file_ary;
	}

	public function indonesian_date ($timestamp = '', $date_format = 'j F Y', $suffix = '') {
		if($timestamp){
		    if (trim ($timestamp) == '')
		    {
		            $timestamp = time ();
		    }
		    elseif (!ctype_digit ($timestamp))
		    {
		        $timestamp = strtotime ($timestamp);
		    }
		    # remove S (st,nd,rd,th) there are no such things in indonesia :p
		    $date_format = preg_replace ("/S/", "", $date_format);
		    $pattern = array (
		        '/Mon[^day]/','/Tue[^sday]/','/Wed[^nesday]/','/Thu[^rsday]/',
		        '/Fri[^day]/','/Sat[^urday]/','/Sun[^day]/','/Monday/','/Tuesday/',
		        '/Wednesday/','/Thursday/','/Friday/','/Saturday/','/Sunday/',
		        '/Jan[^uary]/','/Feb[^ruary]/','/Mar[^ch]/','/Apr[^il]/','/May/',
		        '/Jun[^e]/','/Jul[^y]/','/Aug[^ust]/','/Sep[^tember]/','/Oct[^ober]/',
		        '/Nov[^ember]/','/Dec[^ember]/','/January/','/February/','/March/',
		        '/April/','/June/','/July/','/August/','/September/','/October/',
		        '/November/','/December/',
		    );
		    $replace = array ( 'Sen','Sel','Rab','Kam','Jum','Sab','Min',
		        'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
		        'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des',
		        'Januari','Februari','Maret','April','Juni','Juli','Agustus','Sepember',
		        'Oktober','November','Desember',
		    );
		    $date = date ($date_format, $timestamp);
		    $date = preg_replace ($pattern, $replace, $date);
		    $date = "{$date} {$suffix}";
		    return $date;
		}
	} 

	public function terbilang ($angka) {
	    $angka = (float)$angka;
	    $bilangan = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas');
	    if ($angka < 12) {
	        return $bilangan[$angka];
	    } else if ($angka < 20) {
	        return $bilangan[$angka - 10] . ' Belas';
	    } else if ($angka < 100) {
	        $hasil_bagi = (int)($angka / 10);
	        $hasil_mod = $angka % 10;
	        return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
	    } else if ($angka < 200) {
	        return sprintf('Seratus %s', $this->terbilang($angka - 100));
	    } else if ($angka < 1000) {
	        $hasil_bagi = (int)($angka / 100);
	        $hasil_mod = $angka % 100;
	        return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], $this->terbilang($hasil_mod)));
	    } else if ($angka < 2000) {
	        return trim(sprintf('Seribu %s', $this->terbilang($angka - 1000)));
	    } else if ($angka < 1000000) {
	        $hasil_bagi = (int)($angka / 1000); 
	        $hasil_mod = $angka % 1000;
	        return sprintf('%s Ribu %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod));
	    } else if ($angka < 1000000000) {
	        $hasil_bagi = (int)($angka / 1000000);
	        $hasil_mod = $angka % 1000000;
	        return trim(sprintf('%s Juta %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
	    } else if ($angka < 1000000000000) {
	        $hasil_bagi = (int)($angka / 1000000000);
	        $hasil_mod = fmod($angka, 1000000000);
	        return trim(sprintf('%s Milyar %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
	    } else if ($angka < 1000000000000000) {
	        $hasil_bagi = $angka / 1000000000000;
	        $hasil_mod = fmod($angka, 1000000000000);
	        return trim(sprintf('%s Triliun %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
	    } else {
	        return 'Data Salah';
	    }
	}
}