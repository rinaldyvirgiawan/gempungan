<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Process extends CI_Controller {

	function __construct()
	{
		parent::__construct();
        $this->load->database();
		$this->load->helper(array('url'));
		$this->load->model('ifunction');
	}

	public function user($tp, $dx=0)
	{
		switch($tp){

			case 'login':
			$uname = strip_tags($_POST['uname']);
			$ifpsd = strip_tags($_POST['pswd']);
			
			if($uname && $ifpsd){
				$Qcheck = $this->db->select("pejabat_id, nama, tipe")->from("pejabat")->where("username", $uname)->where("password", $this->ifunction->pswd($ifpsd))->get();

				if($Qcheck->num_rows){
					$check = $Qcheck->result_object();
					
					$_SESSION['internal']['uid'] = $check[0]->pejabat_id;
					$_SESSION['internal']['name'] = $check[0]->nama;
					$_SESSION['internal']['role'] = $check[0]->tipe;
					$_SESSION['internal']['base_url'] = base_url();
					
					header('location:'.base_url());
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Username dan password Anda tidak sesuai.';

					header('location:'.$_SERVER['HTTP_REFERER']);
				}
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silakan lengkapi formulir berikut.';
				
				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function hibah($tp, $dx=0)
	{
		if(empty($_SESSION['internal'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'nphd':			
			$nomor = $_POST['nomor'];
			$tanggal = $_POST['tanggal'];
			$nama = $_POST['nama'];
			$ktp = $_POST['ktp'];
			$jabatan = $_POST['jabatan'];
			$alamat = $_POST['alamat'];
			$atas_nama = $_POST['atas_nama'];	
			$jumlah = $_POST['jumlah'];
			$guna = $_POST['guna'];	
			$tujuan = $_POST['tujuan'];	
			$nama_rek = $_POST['nama_rek'];	
			$no_rek = $_POST['no_rek'];
			$atas_rek = $_POST['atas_rek'];	
			
			if($nomor && $tanggal && $nama && $ktp && $jabatan && $alamat && $atas_nama && $jumlah && $guna && $tujuan && $nama_rek && $no_rek && $atas_rek){
				$this->db->update("surat_masuk", array('no_nphd' => $nomor, 'tanggal_nphd' => $tanggal, 'nilai_pengajuan' => $jumlah, 'nphd_status' => 1), array('surat_id' => $dx));

				$this->db->insert("nphd", array('surat_id' => $dx, 'nomor' => $nomor, 'tanggal' => $tanggal, 'nama' => $nama, 'ktp' => $ktp, 'jabatan' => $jabatan, 'alamat' => $alamat, 'atas_nama' => $atas_nama, 'jumlah' => $jumlah, 'kegunaan' => $guna, 'tujuan' => $tujuan, 'nama_rekening' => $nama_rek, 'no_rekening' => $no_rek, 'atas_rekening' => $atas_rek));

				$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `nphd_id`");
				$last = $Qlast->result_object(); $nphd_id = $last[0]->nphd_id;

				if(isset($_FILES['file']['tmp_name']) && isset($_FILES['file']['name'])){
					if(($_FILES['file']['type']=='application/x-msexcel') || ($_FILES['file']['type']=='application/vnd.ms-excel')){
												
						require_once "application/libraries/spreadsheet_excel_reader.php";
						$data_exc=new Spreadsheet_Excel_Reader($_FILES['file']['tmp_name']);
						$row_exc=$data_exc->rowcount($sheet_index=0);
						
						$colcount = $data_exc->colcount($sheet_index=0);
						
						if($colcount != "4"){
							$_SESSION['notify']['type'] = 'failed';
							$_SESSION['notify']['message'] = 'Format Excel tidak sesuai, silahkan download file template excel terlebih dahulu.';

							header('location:'.$_SERVER['HTTP_REFERER']);
						}
						
						for($i=2; $i <= $row_exc; $i++){
							$no = $data_exc->val($i, 1);
							$uraian = $data_exc->val($i, 2);
							$jumlah = $data_exc->val($i, 3);
							$keterangan = $data_exc->val($i, 4);

							if(isset($jumlah)) $jumlah = str_replace(".", "", $jumlah);
							
							$this->db->insert("nphd_uraian", array('nphd_id' => $nphd_id, 'no' => $no, 'uraian' => $uraian, 'jumlah' => $jumlah, 'keterangan' => $keterangan));
						}
					}else{
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Extension file tidak sesuai, extension yang support adalah .xls';

						header('location:'.$_SERVER['HTTP_REFERER']);
					}
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

					header('location:'.$_SERVER['HTTP_REFERER']);
				}

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'NPHD berhasil ditambahkan.';

				header('location:'.site_url('hibah'));
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'rekap':			
			$tanggal = $_POST['tanggal'];
			$nomor = $_POST['nomor'];
			$dari = $_POST['dari'];
			$alamat = $_POST['alamat'];
			$jabatan = $_POST['jabatan'];
			$uraian = $_POST['uraian'];
			$nilai = $_POST['nilai'];	
			
			if($nomor && $tanggal && $dari && $alamat && $jabatan && $uraian && $nilai){
				$this->db->update("surat_masuk", array('rekap_status' => 1), array('surat_id' => $dx));

				$this->db->insert("hibah", array('surat_id' => $dx, 'tanggal_spp' => $tanggal, 'no_spp' => $nomor, 'dari' => $dari, 'alamat' => $alamat, 'jabatan' => $jabatan, 'uraian' => $uraian, 'nilai' => $nilai));				

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Hibah berhasil ditambahkan.';

				header('location:'.site_url('hibah'));
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'sppls':	
			if(isset($_POST['isi'])){
				$isi = $_POST['isi']; $i = 1;
				foreach($isi as $index => $value) {
					$this->db->insert("sppls_pilihan", array('surat_id' => $dx, 'sppls_id' => $i, 'isi' => $value));
					if($i==4) $this->db->update("surat_masuk", array('tanggal_spp' => $value), array('surat_id' => $dx));
					elseif($i==5) $this->db->update("surat_masuk", array('no_spp' => $value), array('surat_id' => $dx));
					$i++; 
				}
			}

			if(isset($_POST['ceklis'])){
				$ceklis = $_POST['ceklis'];
				foreach($ceklis as $index => $value) {
					$this->db->insert("sppls_pilihan", array('surat_id' => $dx, 'sppls_id' => $value));
				}	
			}

			if(isset($_POST['activity'])){
				$activity = $_POST['activity'];
				foreach($activity as $index => $value) {
					$this->db->insert("sppls_log", array('surat_id' => $dx, 'activity' => $value));
				}	
			}

			if(isset($_POST['status'])){
				$status = $_POST['status'];
				foreach($status as $index => $value) {
					$this->db->update("surat_masuk", array('sppls_status' => $value), array('surat_id' => $dx));
				}	
			}			

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'SPP-LS berhasil ditambahkan.';

			header('location:'.site_url('hibah'));
			break;

			case 'hibah':
			if(isset($_POST['isi'])){
				$Qurut = $this->db->query("SELECT sppls_id FROM sppls WHERE `pejabat_id`='8' ORDER BY urutan ASC LIMIT 1"); $urut = $Qurut->result_object();

				$isi = $_POST['isi']; $i = $urut[0]->sppls_id;
				foreach($isi as $index => $value) {
					$this->db->insert("sppls_pilihan", array('surat_id' => $dx, 'sppls_id' => $i, 'isi' => $value, 'hibah' => '1'));
					$i++; 
				}
			}

			if(isset($_POST['ceklis'])){
				$ceklis = $_POST['ceklis'];
				foreach($ceklis as $index => $value) {
					$this->db->insert("sppls_pilihan", array('surat_id' => $dx, 'sppls_id' => $value, 'hibah' => '1'));
				}	
			}

			if(isset($_POST['activity'])){
				$activity = $_POST['activity'];
				foreach($activity as $index => $value) {
					$this->db->insert("sppls_log", array('surat_id' => $dx, 'activity' => $value, 'hibah' => '1'));
				}	
			}

			if(isset($_POST['status'])){
				$status = $_POST['status'];
				foreach($status as $index => $value) {
					$this->db->update("surat_masuk", array('hibah_status' => $value), array('surat_id' => $dx));
				}	
			}			

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'SPP-LS Hibah berhasil ditambahkan.';

			header('location:'.site_url('hibah'));
			break;
		}
	}

	public function index($tp, $dx=0, $p=0)
	{
		if(empty($_SESSION['internal'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'add_kepwal':
			$nomor = $_POST['nomor'];
			$tanggal = $_POST['tanggal'];	
			
			if($nomor && $tanggal){
				$this->db->insert("kepwal_parent", array('nomor' => $nomor, 'tanggal' => $tanggal));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Kepwal berhasil ditambahkan.';

				header('location:'.site_url());			
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit_kepwal':
			$nomor = $_POST['nomor'];
			$tanggal = $_POST['tanggal'];		
			
			if($nomor && $tanggal){
				$this->db->update("kepwal_parent", array('nomor' => $nomor, 'tanggal' => $tanggal), array('kepwal_id' => $dx));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Kepwal berhasil diedit.';

				header('location:'.site_url());										
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete_kepwal':
			$this->db->delete("kepwal_parent", array('kepwal_id' => $dx));
			$this->db->delete("kepwal_child", array('kepwal_id' => $dx));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Kepwal berhasil dihapus.';

			header('location:'.site_url());
			break;

			//Kepwal Child
			case 'add_child':
			$nama = $_POST['nama'];
			$alamat = $_POST['alamat'];	
			$telepon = $_POST['telepon'];
			$ketua = $_POST['ketua'];
			$jumlah = $_POST['jumlah'];
			
			if($nama && $alamat && $telepon && $ketua && $jumlah){
				$this->db->insert("kepwal_child", array('kepwal_id' => $dx, 'nama' => $nama, 'alamat' => $alamat, 'telepon' => $telepon, 'ketua' => $ketua, 'jumlah' => $jumlah));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Kepwal berhasil ditambahkan.';

				header('location:'.site_url('child/index/'.$dx));			
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit_child':
			$nama = $_POST['nama'];
			$alamat = $_POST['alamat'];	
			$telepon = $_POST['telepon'];
			$ketua = $_POST['ketua'];
			$jumlah = $_POST['jumlah'];		
			
			if($nama && $alamat && $telepon && $ketua && $jumlah){
				$this->db->update("kepwal_child", array('nama' => $nama, 'alamat' => $alamat, 'telepon' => $telepon, 'ketua' => $ketua, 'jumlah' => $jumlah), array('child_id' => $p));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Kepwal berhasil diedit.';

				header('location:'.site_url('child/index/'.$dx));										
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete_child':
			$this->db->delete("kepwal_child", array('child_id' => $p));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Kepwal berhasil dihapus.';

			header('location:'.site_url('child/index/'.$dx));
			break;
		}
	}

	public function pengguna($tp, $dx=0, $p=0)
	{
		if(empty($_SESSION['internal'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'add':
			$nama = $_POST['nama'];
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];	
			
			if($nama && $uname && $pswd){
				$this->db->insert("pejabat", array('nama' => $nama, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'tipe' => 0));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pengguna berhasil ditambahkan.';

				header('location:'.site_url('pengguna'));			
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$nama = $_POST['nama'];
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];		
			
			if($nama && $uname){
				if($pswd) $this->db->update("pejabat", array('nama' => $nama, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd)), array('pejabat_id' => $dx));
				else $this->db->update("pejabat", array('nama' => $nama, 'username' => $uname), array('pejabat_id' => $dx));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pengguna berhasil diedit.';

				header('location:'.site_url('pengguna'));										
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete':
			$this->db->delete("pejabat", array('pejabat_id' => $dx));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Pengguna berhasil dihapus.';

			header('location:'.site_url('pengguna'));
			break;
		}
	}

	public function pejabat($tp, $dx=0, $p=0)
	{
		if(empty($_SESSION['internal'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){		

			case 'edit':
			$nama = $_POST['nama'];
			$nip = $_POST['nip'];
			$pangkat = $_POST['pangkat'];
			$jabatan = $_POST['jabatan'];
			$unit = $_POST['unit'];
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];		
			
			if($nama){
				if($pswd) $this->db->update("pejabat", array('nama' => $nama, 'nip' => $nip, 'pangkat' => $pangkat, 'jabatan' => $jabatan, 'unit_kerja' => $unit, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd)), array('pejabat_id' => $dx));
				else $this->db->update("pejabat", array('nama' => $nama, 'nip' => $nip, 'pangkat' => $pangkat, 'jabatan' => $jabatan, 'unit_kerja' => $unit, 'username' => $uname), array('pejabat_id' => $dx));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pejabat berhasil diedit.';

				header('location:'.site_url('pejabat'));										
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function surat($tp, $dx=0, $p=0)
	{
		if(empty($_SESSION['internal'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'add':
			$tgl_masuk = $_POST['tgl_masuk'];
			$no_kartu = $_POST['no_kartu'];
			$uraian = $_POST['uraian'];
			$alamat = $_POST['alamat'];
			$jenis = $_POST['jenis'];	
			
			if($tgl_masuk && $no_kartu && $uraian && $alamat && $jenis){
				$this->db->insert("surat_masuk", array('tanggal_masuk' => $tgl_masuk, 'no_kartu' => $no_kartu, 'uraian' => $uraian, 'alamat' => $alamat, 'jenis_bantuan' => $jenis));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Surat masuk berhasil ditambahkan.';

				header('location:'.site_url('surat'));			
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$tgl_masuk = $_POST['tgl_masuk'];
			$no_kartu = $_POST['no_kartu'];
			$uraian = $_POST['uraian'];
			$alamat = $_POST['alamat'];
			$jenis = $_POST['jenis'];	
			
			if($tgl_masuk && $no_kartu && $uraian && $alamat && $jenis){
				$this->db->update("surat_masuk", array('tanggal_masuk' => $tgl_masuk, 'no_kartu' => $no_kartu, 'uraian' => $uraian, 'alamat' => $alamat, 'jenis_bantuan' => $jenis), array('surat_id' => $dx));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Surat masuk berhasil diedit.';

				header('location:'.site_url('surat'));										
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete':
			$this->db->delete("surat_masuk", array('surat_id' => $dx));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Surat masuk berhasil dihapus.';

			header('location:'.site_url('surat'));
			break;
		}
	}

	public function pdf($t, $tp, $d, $dx, $papers='portrait')
	{
		if($t == 'export'){
			
			ini_set('memory_limit', '-1');
			require_once "application/libraries/pdf/dompdf_config.inc.php";
			global $_dompdf_show_warnings, $_dompdf_debug, $_DOMPDF_DEBUG_TYPES;
			
			$sapi = php_sapi_name();
			$options = array();
				
			switch($sapi){
				
				case "cli":
				$opts = $this->ifunction->getoptions();
				
				if(isset($opts["h"]) || (!isset($opts["filename"]) && !isset($opts["l"]))) exit($this->ifunction->dompdf_usage());
				
				if(isset($opts["l"])){
					echo "\nUnderstood paper sizes:\n";
					foreach (array_keys(CPDF_Adapter::$PAPER_SIZES) as $size)
					echo " " . mb_strtoupper($size) . "\n";
					exit;
				}
				
				$file = $opts["filename"];
				if(isset($opts["p"])) $paper = $opts["p"]; else $paper = DOMPDF_DEFAULT_PAPER_SIZE;
				if(isset($opts["o"])) $orientation = $opts["o"]; else $orientation = "portrait";
				if(isset($opts["b"])) $base_path = $opts["b"];
				
				if(isset($opts["f"])) $outfile = $opts["f"];
				else {
					if($file === "-") $outfile = "dompdf_out.pdf"; else $outfile = str_ireplace(array(".html", ".htm", ".php"), "", $file) . ".pdf";
				}
				
				if(isset($opts["v"])) $_dompdf_show_warnings = true;
				
				if(isset($opts["d"])){
					$_dompdf_show_warnings = true;
					$_dompdf_debug = true;
				}
				
				if(isset($opts['t'])){
					$arr = split(',',$opts['t']);
					$types = array();
					foreach ($arr as $type) $types[ trim($type) ] = 1;
					$_DOMPDF_DEBUG_TYPES = $types;
				}
				
				$save_file = true;
				break;
				
				default:
				if($d==1) $file = rawurldecode(site_url('process/report_kepwal/'.$dx));
				elseif($d==2) $file = rawurldecode(site_url('process/report_nphd/'.$dx));
				elseif($d==3) $file = rawurldecode(site_url('process/report_sppls/'.$dx));
				elseif($d==4) $file = rawurldecode(site_url('process/report_surat/'.$dx));
				elseif($d==5) $file = rawurldecode(site_url('process/report_hibah/'.$dx));
				elseif($d==6) $file = rawurldecode(site_url('process/report_rekap/'.$dx));
				
				$paper = DOMPDF_DEFAULT_PAPER_SIZE;
				$orientation = $papers;
				
				$file_parts = explode_url($file);
				
				if(($file_parts['protocol'] == '' || $file_parts['protocol'] === 'file://')){
					$file = realpath($file);
					if(strpos($file, DOMPDF_CHROOT) !== 0) throw new DOMPDF_Exception("Permission denied on $file.");
				}
				
				$outfile = $tp.'.pdf';
				
				$save_file = false;
				//$this->db->insert("activity_log", array('staff_id' => $_SESSION['survey_bpr']['uid'], 'activity' => 'export_pdf', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				//$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'report', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));
				break;
			}
			
			$dompdf = new DOMPDF();
			
			if($file === "-"){
				$str = "";
				while( !feof(STDIN)) $str .= fread(STDIN, 4096);
				$dompdf->load_html($str);
			}
			else $dompdf->load_html_file($file);
			
			if(isset($base_path)) $dompdf->set_base_path($base_path);
			
			$dompdf->set_paper($paper, $orientation);
			$dompdf->render();
			
			if($_dompdf_show_warnings){
				global $_dompdf_warnings;
				foreach ($_dompdf_warnings as $msg) echo $msg . "\n";
				echo $dompdf->get_canvas()->get_cpdf()->messages;
				flush();
			}
			
			if($save_file){
				if(strtolower(DOMPDF_PDF_BACKEND) === "gd") $outfile = str_replace(".pdf", ".png", $outfile);
				list($proto, $host, $path, $file) = explode_url($outfile);
				if($proto <> "") $outfile = $file;
				$outfile = realpath(dirname($outfile)) . DIRECTORY_SEPARATOR . basename($outfile);
				if(strpos($outfile, DOMPDF_CHROOT) !== 0) throw new DOMPDF_Exception("Permission denied.");
				file_put_contents($outfile, $dompdf->output( array("compress" => 0)));
				exit(0);
			}
			
			if(!headers_sent()) $dompdf->stream($outfile, $options);
			
		}
		else{
			if($d==1) header('location:'.site_url('process/report_kepwal/'.$dx));
			elseif($d==2) header('location:'.site_url('process/report_nphd/'.$dx));	
			elseif($d==3) header('location:'.site_url('process/report_sppls/'.$dx));
			elseif($d==4) header('location:'.site_url('process/report_surat/'.$dx));
			elseif($d==5) header('location:'.site_url('process/report_hibah/'.$dx));
			elseif($d==6) header('location:'.site_url('process/report_rekap/'.$dx));		
		}
	}

	public function report_kepwal($tp)
	{
		?>
        <!DOCTYPE html>
		<html>
		<head>
			<title></title>
		</head>
		<style type="text/css">
		table tr td{
			vertical-align: top
		}
		</style>
		<body>

		<?php
		$Qdetail = $this->db->query("SELECT nomor FROM kepwal_parent WHERE `kepwal_id`='$tp'"); $detail = $Qdetail->result_object();
		?>
		<table align="center" width="70%">
			<tr>
				<td>LAMPIRAN I :</td>
				<td colspan="2">KEPUTUSAN WALIKOTA BANDUNG</td>
			</tr>
			<tr>
				<td></td>
				<td>NOMOR</td>
				<td>: <?php echo $detail[0]->nomor ?></td>
			</tr>
			<tr>
				<td></td>
				<td>TANGGAL</td>
				<td>: <?php echo $this->ifunction->indonesian_date(date('d M Y')) ?></td>
			</tr>
		</table>


		<h3 align="center">DAFTAR NAMA DAN ALAMAT PENERIMA BELANJA HIBAH</h3>
		<?php
		$Qlist = $this->db->query("SELECT * FROM kepwal_child WHERE kepwal_id='$tp' ORDER BY child_id ASC");
		?>
		<table width="100%" border="1" cellpadding="0" cellspacing="0">
			<tr>
				<td align="center" style="font-weight:bold">NO</td>
				<td align="center" style="font-weight:bold">NAMA/ALAMAT PENERIMA HIBAH</td>
				<td align="center" style="font-weight:bold">JUMLAH BESARAN DANA HIBAH (RP)</td>
			</tr>
			<tr>
				<td align="center">1</td>
				<td align="center">2</td>
				<td align="center">3</td>
			</tr>
			<?php
			$i = 1; $jumlah = 0;
			foreach($Qlist->result_object() as $list){
                echo '<tr>
						<td align="center">'.$i.'</td>
                        <td>'.strtoupper($list->nama).'; '.strtoupper($list->alamat).'; TELP: '.strtoupper($list->telepon).'; KETUA: '.strtoupper($list->ketua).'</td>
                        <td align="right">'.number_format($list->jumlah,0,",",".").',-</td>
					</tr>';
                $i++; $jumlah += $list->jumlah;
            }
			?>		
			<tr>
				<td align="center" colspan="2"><b>JUMLAH</b></td>
				<td align="right"><b><?php echo number_format($jumlah,0,",",".").',-'; ?></b></td>
			</tr>
			<tr>
				<td colspan="3"><b>TERBILANG</b> : === <?php echo strtoupper($this->ifunction->terbilang($jumlah)); ?> RUPIAH ===</td>
			</tr>
		</table>

		<br><br><br>

		<?php
		$Qjabat = $this->db->query("SELECT nama FROM pejabat WHERE `tipe`='1'"); $jabat = $Qjabat->result_object();
		?>

		<table align="right" width="40%" style="text-align: center">
			<tr>
				<td style="height:100px">WALIKOTA BANDUNG</td>
			</tr>
			<tr>
				<td style="height:100px"><?php echo strtoupper($jabat[0]->nama) ?></td>
			</tr>
		</table>
					
		</html>
        <?php
	}

	public function report_rekap($tp)
	{
		?>
        <!DOCTYPE html>
		<html>
		<head>
			<title></title>
		</head>
		<style type="text/css">
		table tr td{
			vertical-align: top
		}
		</style>
		<body>

		<?php
		$year = date('Y');
		$Qlist = $this->db->query("SELECT * FROM hibah WHERE YEAR(`time_entry`)='$year'");
		?>
		<h4 align="center">DAFTAR HIBAH TAHUN <?php echo date('Y') ?> <br> DINAS PENGELOLAAN KEUANGAN DAN ASET DAERAH <br> PEMERINTAH KOTA BANDUNG</h4>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>				
				<td width="180">REKENING</td>
				<td>:</td>
			</tr>
			<tr>
				<td>NAMA PROGRAM/KEGIATAN</td>
				<td>:</td>
			</tr>
			<tr>
				<td>BESARNYA</td>
				<td>:</td>
			</tr>
		</table>

		<br>

		<table width="100%" border="1" cellpadding="0" cellspacing="0">
			<tr>
				<th rowspan="2">No.</th>
				<th colspan="2">SPP</th>
				<th rowspan="2">Dari</th>
				<th rowspan="2">Alamat</th>
				<th rowspan="2">Jabatan</th>
				<th rowspan="2">Uraian</th>
				<th rowspan="2">Nilai (Rp)</th>
			</tr>
			<tr>
				<th>Tanggal</th>
				<th>No.</th>
			</tr>

			<?php
			$i = 1;
			foreach($Qlist->result_object() as $list){
                echo '<tr>
						<td align="center">'.$i.'</td>
						<td>'.$this->ifunction->indonesian_date($list->tanggal_spp).'</td>
						<td>'.$list->no_spp.'</td>
						<td>'.$list->dari.'</td>
						<td>'.$list->alamat.'</td>
						<td>'.$list->jabatan.'</td>
						<td>'.$list->uraian.'</td>
                        <td align="right">'.number_format($list->nilai,0,",",".").',-</td>
					</tr>';
				$i++;
            }
			?>
		</table>
		</body>	
		</html>
        <?php
	}

	public function report_nphd($tp)
	{
		?>
        <!DOCTYPE html>
		<html>
		<head>
			<title></title>
		</head>
		<style type="text/css">
		body{
			text-align: justify;
		}
		table tr td{
			vertical-align: top;
			text-align: justify;
		}
		</style>
		<body>

		<?php
		$Qdetail = $this->db->query("SELECT * FROM nphd WHERE `nphd_id`='$tp'"); $detail = $Qdetail->result_object();
		$Qjabat = $this->db->query("SELECT * FROM pejabat WHERE `tipe`='2'"); $jabat = $Qjabat->result_object();
		?>
		<p align="center"><b>NASKAH PERJANJIAN BELANJA HIBAH DAERAH (NPHD) 
			<br>BERUPA UANG
			<br>Nomor : <?php echo $detail[0]->nomor ?></b>
		</p>

		<p>
			Pada hari ini <b><i><?php echo $this->ifunction->indonesian_date($detail[0]->tanggal, 'l') ?></i></b>, tanggal <b><i><?php echo $this->ifunction->terbilang(date('d', strtotime($detail[0]->tanggal))) ?></i></b>, bulan <b><i><?php echo $this->ifunction->indonesian_date($detail[0]->tanggal, 'F') ?></i></b>, tahun <b><i><?php echo $this->ifunction->terbilang(date('Y', strtotime($detail[0]->tanggal))) ?></i></b> yang bertanda tangan di bawah ini:
		</p>

		<table width="100%">
			<tr>
				<td rowspan="5" valign="top" width="20">I</td>
				<td width="100">Nama</td>
				<td width="10">:</td>
				<td><?php echo $jabat[0]->nama ?></td>
			</tr>
			<tr>
				<td>NIP</td>
				<td>:</td>
				<td><?php echo $jabat[0]->nip ?></td>
			</tr>
			<tr>
				<td>Pangkat</td>
				<td>:</td>
				<td><?php echo $jabat[0]->pangkat ?></td>
			</tr>
			<tr>
				<td>Jabatan</td>
				<td>:</td>
				<td><?php echo $jabat[0]->jabatan ?></td>
			</tr>
			<tr>
				<td>Unit Kerja</td>
				<td>:</td>
				<td><?php echo $jabat[0]->unit_kerja ?></td>
			</tr>
		</table>

		<br>
		<p>
			Dalam hal ini bertindak untuk dan atas nama Walikota Bandung yang selanjutnya disebut PIHAK PERTAMA.
		</p>

		<table width="100%">
			<tr>
				<td rowspan="4" valign="top" width="20">II</td>
				<td width="100">Nama</td>
				<td width="10">:</td>
				<td><?php echo $detail[0]->nama ?></td>
			</tr>
			<tr>
				<td>No. KTP</td>
				<td>:</td>
				<td><?php echo $detail[0]->ktp ?></td>
			</tr>
			
			<tr>
				<td>Jabatan</td>
				<td>:</td>
				<td><?php echo $detail[0]->jabatan ?></td>
			</tr>
			<tr>
				<td>Alamat</td>
				<td>:</td>
				<td><?php echo $detail[0]->alamat ?></td>
			</tr>

		</table>

		<br>
		<p>
			Yang bertindak untuk dan atas nama <b><?php echo $detail[0]->atas_nama ?></b> yang selanjutnya disebut PIHAK KEDUA
		</p>

		<p>
			Kedua belah PIHAK sepakat untuk melakukan Perjanjian Belanja Hibah Daerah berupa Uang dengan ketentuan sebagai berikut :
		</p>

		<br>
		<p align="center"><b>Pasal 1 
			<br>JUMLAH DAN TUJUAN HIBAH	</b>
		</p>
		<table width="100%">
			<tr>
				<td>(1)</td>
				<td>
					PIHAK PERTAMA memberikan belanja hibah kepada PIHAK KEDUA, berupa uang sebesar <b><i>Rp. <?php echo number_format($detail[0]->jumlah,0,",","."); ?>,- (<?php echo $this->ifunction->terbilang($detail[0]->jumlah) ?> Rupiah)</i></b>.
				</td>
			</tr>
			<tr>
				<td>(2)</td>
				<td>
					PIHAK KEDUA menyatakan telah menerima belanja hibah dari PIHAK PERTAMA berupa uang sebesar <b><i>Rp. <?php echo number_format($detail[0]->jumlah,0,",","."); ?>,- (<?php echo $this->ifunction->terbilang($detail[0]->jumlah) ?> Rupiah)</i></b>.
				</td>
			</tr>
			<tr>
				<td>(3)</td>
				<td>
					Belanja Hibah sebagaimana dimaksud pada ayat (1) dipergunakan untuk <b><i><?php echo $detail[0]->kegunaan ?></i></b> sesuai dengan Rencana Penggunaan Belanja Hibah/Proposal yang merupakan bagian yang tidak terpisahkan dari naskah perjanjian belanja hibah daerah ini.
				</td>
			</tr>
			<tr>
				<td>(4)</td>
				<td>
					Penggunaan belanja hibah sebagaimana ayat (2) bertujuan untuk <b><i><?php echo $detail[0]->tujuan ?></i></b>.
				</td>
			</tr>
		</table>

		<br><br>
		<p align="center"><b>Pasal 2
			<br>PENCAIRAN BELANJA HIBAH</b>	
		</p>

		<table width="100%">
			<tr>
				<td>(1)</td> 
				<td>
					Pencairan belanja hibah berupa uang dibebankan pada Anggaran Pendapatan dan Belanja Daerah (APBD) kota Bandung 2016.<br>
				</td>
			</tr>
			<tr>
			<td>(2)</td> 
				<td>
					Untuk pencairan belanja hibah, PIHAK KEDUA mengajukan permohonan kepada PIHAK PERTAMA, dengan dilampiri :<br>
				</td> 
			</tr>
			<tr>
				<td></td>
				<td><table>
					<tr><td>1.</td><td>Surat permohonan pencairan Belanja Hibah, dilengkapi rincian rencana penggunaan Belanja Hibah sesuai yang tercantum dalam DPA;</td></tr>

					<tr><td>2.</td><td>N P H D;</td></tr>

					<tr><td>3.</td><td>Salinan / fotocopy Kartu Tanda Penduduk (KTP) atas nama pimpinan intansi atau Kepala Daerah / Direksi atau sebutan lain / Ketua Kelompok Masyarakat /  nama ketua / pimpinan / pengurus lembaga/organisasi Penerima Belanja Hibah;</td></tr>

					<tr><td>4.</td><td>Salinan / fotocopy rekening bank yang masih aktif atas nama intansi dan/atau rekening Kas Umum Daerah lainnya;</td></tr>

					<tr><td>5.</td><td>Kuitansi rangkap 4 (empat), terdiri dari 2 (dua) kuitansi bermaterai cukup, ditandatangani dan dibubuhi cap intansi serta dicantumkan nama lengkap pimpinan intansi atau kepala Daerah lainnya ;</td></tr>

					<tr><td>6.</td><td>Surat pernyataan tanggung jawab.</td></tr>
					</table>
				</td>
			</tr>
			<tr>
			<td>(3)</td> 
			<td>
				Belanja Hibah sebagaimana dimaksud pada pasal 1 ayat (1) dibayarkan melalui pemindahbukuan dari Rekening Kas Umum Daerah Kota Bandung ke Rekening <b><?php echo $detail[0]->nama_rekening ?></b> atas nama PIHAK KEDUA dengan Nomor Rekening <b><?php echo $detail[0]->no_rekening ?> / <?php echo $detail[0]->atas_rekening ?></b>.
			</td>
		</tr>
		<tr>
			<td>(4)</td> 
			<td>
				PIHAK KEDUA setelah menerima pencairan belanja hibah dari PIHAK PERTAMA, segera melaksanakan kegiatan dengan berpedoman pada Rencana Pengunaan Belanja Hibah/Proposal dan Peraturan perundang-undangan.
			</td>
		</tr>
		</table>

		<br><br>
		<p align="center"><b>Pasal 3
			<br>PENGGUNAAN</b>	
		</p>

		<table width="100%">
			<tr>
				<td>(1)</td>
				<td>
					 PIHAK KEDUA menggunakan belanja hibah berupa uang sebagaimana dimaksud pada pasal 2 ayat (1) sesuai Rencana Pengguanaan Belanja Hibah/Proposal.
				</td>
			</tr>
			<tr>
				<td>(2)</td>
				<td>
				 	PIHAK KEDUA dilarang mengalihkan belanja hibah yang diterima kepada pihak lain<br>
				</td>
			</tr>
			<tr>
				<td>(3)</td>
				<td>Belanja hibah sebagaimana dimaksud dalam pasal 1 dipergunakan untuk:<br></td>
			</tr>
		</table>

		<br><br>
		<?php
        $Qlist = $this->db->query("SELECT * FROM nphd_uraian WHERE nphd_id='$tp' ORDER BY uraian_id ASC");
        ?>
		<table width="100%" border="1" cellpadding="0" cellspacing="0">
			<tr>
				<td style="text-align:center"><b>No.</b></td>
				<td style="text-align:center"><b>Uraian Kegiatan/Penggunaan</b></td>
				<td style="text-align:center"><b>Jumlah (Rp)</b></td>
				<td style="text-align:center"><b>Ket.</b></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<?php
			foreach($Qlist->result_object() as $list){
				if($list->jumlah){
					$jumlah = doubleval($list->jumlah);
					$jumlah = number_format($jumlah,0,",",".");
				}else $jumlah = $list->jumlah;
				//'; if($list->no) echo $list->no; else echo '&nbsp'; echo '
                echo '<tr>
						<td style="text-align:center">'.$list->no.'</td>
						<td>'.$list->uraian.'</td>
						<td style="text-align:right">'.$jumlah.'</td>
						<td>'.$list->keterangan.'</td>
					</tr>';
            }
			?>
		</table>

		<br><br>
		<p align="center"><b>Pasal 4
			<br>KEWAJIBAN PIHAK KEDUA </b>	
		</p>

		<table width="100%">
			<tr>
				<td>(1)</td>
				<td>Menandatangani Surat Pernyataan Tanggung Jawab Permohonan Belanja Hibah.</td>
			</tr>

			<tr>
				<td>(2)</td>
				 <td>
				 	Apabila diggunakan untuk pengadaan barang dan jasa, maka proses pengadaan barang dan jasa sesuai dengan peraturan perundang-undangan.
				 </td>
			</tr>
			<tr>	
				<td>(3)</td> 
				 <td>
				 	Mengajukan Permohonan nomor register atas hibah langsung bentuk uang kepada Direktur Jendral Pengelolaan Utang c.q Direktur Evaluasi Akutansi dan Setelman.
				 </td>
			</tr>
			<tr>
				<td>(4)</td> 
				<td>
					Mengajukan Permohonan Persetujuan Pembukaan Rekening Hibah kepada BUN/Kuasa BUN dalam rangka Pengelolaan Hibah langsung dalam bentuk uang.
				</td>
			</tr>
			<tr>
				<td>(5)</td> 
				<td>Melakukan penyesuaian pagu belanja yang bersumber dari Hibah langsung dalam bentuk uang dalam DIPA K/L.</td>
			</tr>
			<tr>
				<td>(6)</td> 
				<td>
					Mengajukan SP2HL atas seluruh pendapatan Hibah langsung yang bersumber dari dalam negeri dalam bentuk uang sebesar yang telah diterima dan belanja yang bersumber dari Hibah langsung yang bersumber dari dalam negeri sebesar yang telah dibelanjakan pada tahun anggaran berjalan kepada KPPN mitra kerjanya.
				</td>
			</tr>
			<tr>
				<td>(7)</td> 
				<td>
					Membuat dan menyampaikan Laporan Penggunaan Belanja Hibah kepada walikota paling lambat tanggal 10 januari tahun anggaran berikutnya atau 1 (satu) bulan setelah kegiatan selesai melalui <i>Dinas Pengelolaan keuangan dan Aset Daerah Kota Bandung</i> disertai dokumen Surat Pernyataan Tanggung Jawab Penggunaan Belanja Hibah yang ditandatangani pimpinan lembaga/organisasi dan pengajuan permohonan nomor register.
				</td>
			</tr>
			<tr>
				<td>(8) 
				<td>
					Dalam hal terdapat sisa pagu belanja yang bersumber dari hibah langsung dalam bentuk uang dapat dikembalikan kepada Pemberi Hibah melalui Kas Daerah Pemerintah Kota Bandung.
				</td>
			</tr>
			<tr>
				<td>(9)</td> 
				<td>
					Kewajiban lainnya yang sesuaikan dengan karakteristik dan ketentuan spesifik pada masing-masing SKPD.
				</td>
			</tr>
		</table>	

		<br><br>
		<p align="center"><b>Pasal 5
			<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</b>
		</p>

		<table width="100%">
		<tr>
			<td>(1)</td> 
			<td>
				Mencairkan belanja hibah apabila seluruh persyaratan dan kelengkapan berkas pengajuan dana telah dipenuhi oleh PIHAK KEDUA.
			</td>
		</tr>
		<tr>
			<td>(2)</td>
			<td>Menunda pencairan belanja hibah apabila PIHAK KEDUA tidak/belum memenuhi persyaratan yang ditetapkan.</td>
		</tr>
		<tr>
			<td>(3)</td> 
			<td>Melaksanakan evaluasi dan monitoring atas penggunaan belanja hibah dilakukan secara administratif.</td>
		</tr>
		<tr>
			<td>(4)</td> 
			<td>Hak dan Kewajiban lainnya yang disesuaikan dengan karakteristik dan ketentuan spesifik pada masing-masing SKPD.</td>
		</tr>
		</table>

		<br><br>
		<p align="center"><b>Pasal 6
			<br>HAK DAN KEWAJIBAN PIHAK PERTAMA</b>
		</p>

		<table width="100%">
		<tr>
			<td>(1)</td> 
			<td>
				Naskah Perjanjian Belanja Hibah Daerah (NPHD) ini, dibuat rangkap 4 (empat), lembar pertama dan kedua masing-masing bermaterai cukup sehingga mempunyai kekuatan hukum sama.
			</td>
		</tr>
		<tr>
			<td>(2)</td> 
			<td>Hal-hal lain yang belum tercantum dalam NPHD ini dapat diatur lebih lanjut dalam Addendum.</td>
		</tr>
		</table>
		<br>

		<br>
		<table align="center" width="100%">
		<tr>
			<td width="100" style="text-align:center"><b>PIHAK PERTAMA</b></td>
			<td width="180"></td>
			<td width="100" style="text-align:center"><b>PIHAK KEDUA</b></td>
		</tr>
		
		<tr>
			<td height="60"></td>
			<td></td>
			<td></td>
		</tr>

		<tr>
			<td style="text-align:center"><b><?php echo $jabat[0]->nama ?></b></td>
			<td></td>
			<td style="text-align:center"><b><?php echo $detail[0]->nama ?></b></td>
		</tr>

		</table>
		</body>	
		</html>
        <?php
	}

	public function report_sppls($tp)
	{
		?>
        <!DOCTYPE html>
		<html>
		<head>
			<title></title>
		</head>
		<style type="text/css">
		table tr td{
			vertical-align: top;
			text-align: justify;			
		}
		</style>
		<body>

		<?php
		$Qdetail = $this->db->query("SELECT sppls_id, isi FROM sppls_pilihan WHERE `surat_id`='$tp' ORDER BY sppls_id ASC LIMIT 7"); 
		$detail = $Qdetail->result_object();		
		?>

		<p align="center"><b><u>KARTU KENDALI DOKUMEN </u></b></p><br>
		<b><u>PENGAJUAN KELENGKAPAN SPP-LS SKPD:</u></b><br>
		(BELANJA PEGAWAI/SUBSIDI/HIBAH/BANTUAN KEUANGAN/BELANJA TIDAK TERDUGA)
		<br><br>

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="120">Nama Lembaga</td>
				<td>: <?php echo $detail[0]->isi ?></td>
			</tr>
			<tr>
				<td>Alamat</td>
				<td>: <?php echo $detail[1]->isi ?></td>
			</tr>
			<tr>
				<td>No. Rekening Bank</td>
				<td>: <?php echo $detail[2]->isi ?></td>
			</tr>
			<tr>
				<td>Tanggal SPP</td>
				<td>: <?php echo $this->ifunction->indonesian_date($detail[3]->isi) ?></td>
			</tr>
			<tr>
				<td>No SPP</td>
				<td>: <?php echo $detail[4]->isi ?></td>
			</tr>
			<tr>
				<td>Nilai</td>
				<td>: <?php echo 'Rp. '.number_format($detail[5]->isi,0,",",".").',-' ?></td>
			</tr>
			<tr>
				<td>Uraian</td>
				<td>: <?php echo $detail[6]->isi ?></td>
			</tr>
		</table>

		<br><br>

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td><b>BELANJA PEGAWAI/SUBSIDI/BANTUAN KEUANGAN/BELANJA TIDAK TERDUGA</b></td>
			</tr>
			<?php
            $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 4)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

            $Qdetail1 = $this->db->query("SELECT sppls_id FROM sppls_pilihan WHERE `surat_id`='$tp' ORDER BY sppls_id ASC");

            foreach($Qlist1->result_object() as $list1){
            	$url = 'static/img/check'; foreach($Qdetail1->result_object() as $detail1) if($detail1->sppls_id==$list1->sppls_id) $url .= 'list'; $url .= '.png';          

                echo '<tr>
						<td><img src="'.base_url($url).'"> '.$list1->judul.'</td>
					</tr>';                  
            }
            ?>
		</table>

		<br>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2"><b>BELANJA HIBAH</b></td>
			</tr>
			<?php
            $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 5)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

            $Qdetail1 = $this->db->query("SELECT sppls_id FROM sppls_pilihan WHERE `surat_id`='$tp' ORDER BY sppls_id ASC");

            foreach($Qlist1->result_object() as $list1){
            	$url = 'static/img/check'; foreach($Qdetail1->result_object() as $detail1) if($detail1->sppls_id==$list1->sppls_id) $url .= 'list'; $url .= '.png';          

                echo '<tr>
						<td><img src="'.base_url($url).'"> '.$list1->judul.'</td>
					</tr>';                  
            }
            ?>
		</table>

		<br>
		<table width="100%" cellpadding="0" cellspacing="0">	
			<tr>
				<td colspan="2"><b>PENGUJIAN</b></td>
			</tr>
			<?php
            $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 6)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

            $Qdetail1 = $this->db->query("SELECT sppls_id FROM sppls_pilihan WHERE `surat_id`='$tp' ORDER BY sppls_id ASC");

            foreach($Qlist1->result_object() as $list1){
            	$url = 'static/img/check'; foreach($Qdetail1->result_object() as $detail1) if($detail1->sppls_id==$list1->sppls_id) $url .= 'list'; $url .= '.png';          

                echo '<tr>
						<td><img src="'.base_url($url).'"> '.$list1->judul.'</td>
					</tr>';                  
            }
            ?>
		</table>

		<br>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2"><b>CETAK SPP</b></td>
			</tr>
			<?php
            $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 7)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

            $Qdetail1 = $this->db->query("SELECT sppls_id FROM sppls_pilihan WHERE `surat_id`='$tp' ORDER BY sppls_id ASC");

            foreach($Qlist1->result_object() as $list1){
            	$url = 'static/img/check'; foreach($Qdetail1->result_object() as $detail1) if($detail1->sppls_id==$list1->sppls_id) $url .= 'list'; $url .= '.png';          

                echo '<tr>
						<td><img src="'.base_url($url).'"> '.$list1->judul.'</td>
					</tr>';                  
            }
            ?>			
		</table>

		<br><br><br><br>

		<table width="100%" cellpadding="0" cellspacing="0">
		<?php
		$Qdetail = $this->db->query("SELECT nip, nama FROM pejabat WHERE tipe IN (3,4,5,6) ORDER BY tipe ASC LIMIT 4"); 
		$detail = $Qdetail->result_object();

		$Qtanggal = $this->db->query("SELECT time_entry FROM sppls_log WHERE surat_id='$tp' ORDER BY log_id ASC LIMIT 4"); 
		$tanggal = $Qtanggal->result_object();
		?>
			<tr>
				<td colspan="2"><b><u>Petugas Penyusunan Dokumen dan Pembukuan</u></b></td>
				<td width="50">Diterima </td>
				<td colspan="2"><b><u>Petugas Penguji Kelengkapan Dokumen</u></b></td>
			</tr>
			<tr>
				<td width="80">Tanggal</td>
				<td>: <?php echo $this->ifunction->indonesian_date($tanggal[0]->time_entry) ?></td>
				<td></td>
				<td width="80">Tanggal</td>
				<td>: <?php echo $this->ifunction->indonesian_date($tanggal[1]->time_entry) ?></td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>: <?php echo $detail[0]->nama ?></td>
				<td></td>
				<td>Nama</td>
				<td>: <?php echo $detail[1]->nama ?></td>
			</tr>
			<tr>
				<td>NIP</td>
				<td>: <?php echo $detail[0]->nip ?></td>
				<td></td>
				<td>NIP</td>
				<td>: <?php echo $detail[1]->nip ?></td>
			</tr>
			<tr>
				<td>Tanda Tangan</td>
				<td>:</td>
				<td></td>
				<td>Tanda Tangan</td>
				<td>:</td>
			</tr>
			<tr>
				<td colspan="5"><br></td>
			</tr>
			<tr>
				<td colspan="2"><b><u>Petugas Penguji Dokumen Pencairan Dan Penerbitan SPP</u></b></td>
				<td width="50"></td>
				<td colspan="2"><b><u>Mengetahui,<br> Bendahara Pengeluaran SKPKD</u></b></td>
			</tr>
			<tr>
				<td width="80">Tanggal</td>
				<td>: <?php echo $this->ifunction->indonesian_date($tanggal[2]->time_entry) ?></td>
				<td></td>
				<td width="80">Tanggal</td>
				<td>: <?php echo $this->ifunction->indonesian_date($tanggal[3]->time_entry) ?></td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>: <?php echo $detail[2]->nama ?></td>
				<td></td>
				<td>Nama</td>
				<td>: <?php echo $detail[3]->nama ?></td>
			</tr>
			<tr>
				<td>NIP</td>
				<td>: <?php echo $detail[2]->nip ?></td>
				<td></td>
				<td>NIP</td>
				<td>: <?php echo $detail[3]->nip ?></td>
			</tr>
			<tr>
				<td>Tanda Tangan</td>
				<td>:</td>
				<td></td>
				<td>Tanda Tangan</td>
				<td>:</td>
			</tr>	
		</table>
		</body>	
		</html>
        <?php
	}

	public function report_surat($tp)
	{
		?>
        <!DOCTYPE html>
		<html>
		<head>
			<title></title>
		</head>
		<style type="text/css">
		body{
			font-size: 12px;
		}		
		table tr td{
			vertical-align: top
		}
		</style>
		<body>

		<?php
		$year = date('Y');
		$Qdetail = $this->db->query("SELECT * FROM surat_masuk WHERE YEAR(time_entry)='$year'"); $detail = $Qdetail->result_object();
		?>
		<p>Surat Masuk <?php echo date('Y') ?></p>
		<table border="1" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<th>NO</th>
				<th>TANGGAL MASUK BERKAS/DISPOSISI</th>
				<th>NO KARTU KENDALI</th>
				<th>URAIAN</th>
				<th>ALAMAT</th>
				<th>JENIS BANTUAN</th>
				<th>NOMOR REGISTER NPHD</th>
				<th>TANGGAL REGISTER NPHD</th>
				<th>NILAI PENGAJUAN</th>
				<th colspan="3">TANGGAL dan NO SPP</th>
				<th>NOMOR BOX</th>
				<th colspan="3">TANGGAL dan NO SP2D</th>
			</tr>

			<?php
			$i = 1;
			foreach($Qdetail->result_object() as $detail){
            	echo '<tr>
						<td align="center">1</td>
						<td align="center">'.$this->ifunction->indonesian_date($detail->tanggal_masuk).'</td>
						<td>'.$detail->no_kartu.'</td>
						<td>'.$detail->uraian.'</td>
						<td>'.$detail->alamat.'</td>
						<td align="center">'; if($detail->jenis_bantuan==1) echo 'Hibah'; elseif($detail->jenis_bantuan==2) echo 'Bansos'; elseif($detail->jenis_bantuan==3) echo 'Subsidi'; elseif($detail->jenis_bantuan==4) echo 'BPJS PNS'; elseif($detail->jenis_bantuan==5) echo 'Bantuan Keuangan'; echo '</td>
						<td>'.$detail->no_nphd.'</td>
						<td align="center">'.$this->ifunction->indonesian_date($detail->tanggal_nphd).'</td>
						<td align="right">'.number_format($detail->nilai_pengajuan,0,",",".").'</td>
						<td align="center">'.$this->ifunction->indonesian_date($detail->tanggal_spp).'</td>
						<td width="10">991/</td>
						<td>'.$detail->no_spp.'</td>
						<td></td>
						<td width="50"></td>
						<td width="10">957/</td>
						<td></td>
					</tr>';   
				$i++;         
            }
			?>			
		</table>
		</body>			
		</html>
        <?php
	}

	public function report_hibah($tp)
	{
		?>
        <!DOCTYPE html>
		<html>
		<head>
			<title></title>
		</head>
		<style type="text/css">	
		table tr td{
			vertical-align: top
		}
		</style>
		<body>

		<?php
		$Qdetail = $this->db->query("SELECT sppls_id, isi FROM sppls_pilihan WHERE `surat_id`='$tp' AND hibah='1' ORDER BY sppls_id ASC LIMIT 4"); 
		$detail = $Qdetail->result_object();
		?>
		<p align="center"><b><u>KARTU KENDALI DOKUMEN
			<br>Pengujian Kelengkapan SPP-LS Khusus Belanja Hibah</u></b>
		</p><br>

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td rowspan="4" valign="top" width="130">Surat Pengantar SPP-LS</td>
				<td rowspan="4" valign="top" width="10">:</td>
				<td width="60">Nama</td>
				<td>: <?php echo $detail[0]->isi ?></td>
			</tr>
			<tr>
				<td>Nomor</td>
				<td>: <?php echo $detail[1]->isi ?></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td>: <?php echo $this->ifunction->indonesian_date($detail[2]->isi) ?></td>
			</tr>
			<tr>
				<td>Uraian</td>
				<td>: <?php echo $detail[3]->isi ?></td>
			</tr>
		</table>

		<br><br>

		<table width="100%" cellpadding="0" cellspacing="0">
			<?php
            $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 8)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

            $Qdetail1 = $this->db->query("SELECT sppls_id FROM sppls_pilihan WHERE `surat_id`='$tp' AND hibah='1' ORDER BY sppls_id ASC");

            foreach($Qlist1->result_object() as $list1){
            	$url = 'static/img/check'; foreach($Qdetail1->result_object() as $detail1) if($detail1->sppls_id==$list1->sppls_id) $url .= 'list'; $url .= '.png';          

                echo '<tr>
						<td><img src="'.base_url($url).'"> '.$list1->judul.'</td>
					</tr>';                  
            }
            ?>
        </table>

        <br>

        <table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20">Bila <b><i>lengkap</i></b> di register, ditandatangani, dan segera diserahkan.</td>			
			</tr>
			<tr>
				<td width="20">Bila <b><i>tidak lengkap</i></b> dikembalikan kepada <b><i>Bendahara</i></b></td>				
			</tr>
		</table>

		<br><br>

		<?php
		$Qdetail = $this->db->query("SELECT nip, nama FROM pejabat WHERE tipe IN (7,8,9) ORDER BY tipe ASC LIMIT 3"); 
		$detail = $Qdetail->result_object();

		$Qtanggal = $this->db->query("SELECT time_entry FROM sppls_log WHERE surat_id='$tp' AND hibah='1' ORDER BY log_id ASC LIMIT 2"); 
		$tanggal = $Qtanggal->result_object();
		?>

		<table style="font-weight:bolder" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2"><u>Petugas Penguji Kelengkapan</u></td>
				<td width="70" align="right">Diterima : </td>
				<td colspan="2"><u>Petugas Verifikasi SPP dan Penertiban SPM</u></td>
			</tr>
			<tr>
				<td width="80">Tanggal</td>
				<td>: <?php echo $this->ifunction->indonesian_date($tanggal[0]->time_entry) ?></td>
				<td></td>
				<td width="80">Tanggal</td>
				<td>:</td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>: <?php echo $detail[0]->nama ?></td>
				<td></td>
				<td>Nama</td>
				<td>: <?php echo $detail[2]->nama ?></td>
			</tr>
			<tr>
				<td>NIP</td>
				<td>: <?php echo $detail[0]->nip ?></td>
				<td></td>
				<td>NIP</td>
				<td>: <?php echo $detail[2]->nip ?></td>
			</tr>
			<tr>
				<td>Tanda Tangan</td>
				<td>:</td>
				<td></td>
				<td>Tanda Tangan</td>
				<td>:</td>
			</tr>
		</table>

		<br><br>

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2"><b>Verifikasi SPP dan Penerbitan SPM</b></td>
			</tr>
			<?php
            $Qlist1 = $this->db->select("sppls_id, judul")->from('sppls')->where('pejabat_id', 9)->where('tipe', 'checkbox')->order_by('urutan', 'ASC')->get();

            $Qdetail1 = $this->db->query("SELECT sppls_id FROM sppls_pilihan WHERE `surat_id`='$tp' AND hibah='1' ORDER BY sppls_id ASC");

            foreach($Qlist1->result_object() as $list1){
            	$url = 'static/img/check'; foreach($Qdetail1->result_object() as $detail1) if($detail1->sppls_id==$list1->sppls_id) $url .= 'list'; $url .= '.png';          

                echo '<tr>
						<td><img src="'.base_url($url).'"> '.$list1->judul.'</td>
					</tr>';                  
            }
            ?>			
		</table>
		
		<br><br>

		<table width="100%" style="font-weight:bolder" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="4">Mengetahui,</td>
			</tr>
			<tr>
				<td colspan="2"><u>Pejabat Penatausaha Keuangan</u></td>
				<td colspan="2"><u>Petugas Verifikasi SPP dan Penertiban SPM</u></td>
			</tr>
			<tr>
				<td width="80">Tanggal</td>
				<td>: <?php echo $this->ifunction->indonesian_date($tanggal[1]->time_entry) ?></td>
				<td width="80">Tanggal</td>
				<td>:</td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>: <?php echo $detail[1]->nama ?></td>
				<td>Nama</td>
				<td>: <?php echo $detail[2]->nama ?></td>
			</tr>
			<tr>
				<td>NIP</td>
				<td>: <?php echo $detail[1]->nip ?></td>
				<td>NIP</td>
				<td>: <?php echo $detail[2]->nip ?></td>
			</tr>
			<tr>
				<td>Tanda Tangan</td>
				<td>:</td>
				<td>Tanda Tangan</td>
				<td>:</td>
			</tr>
		</table>
		</body>			
		</html>
        <?php
	}

	public function generate_dnc($kategori=0, $dari='', $sampai='', $skpd=0)
	{
		// if($dari!='' && $sampai!='' && $skpd!=0){
		// 	// $Qskpd = $this->db->query("SELECT name FROM skpd WHERE id='$skpd'");
		// 	// $skpd = $Qskpd->result_object(); $name = $skpd[0]->name;			
		// 	$tgl_dari = date('d/M/Y', strtotime($dari)); $tgl_sampai = date('d/M/Y', strtotime($sampai));

		// 	// $filename = 'DNC-PBH-'.$name.' - '.$tgl_dari.'-'.$tgl_sampai;
		// 	$filename = 'DNC-PBH-'.$tgl_dari.'-'.$tgl_sampai;
		// }else $filename = 'DNC-PBH-'.date('d/M/Y');

		$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'generate_dnc', 'ip' => $_SERVER['REMOTE_ADDR']));

		$filename = 'DNC-PBH-'.date('d/M/Y');

		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$filename.".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>DNC PBH</title>
        </head>
		<style type="text/css">
		table tr td{
			vertical-align: top
		}
		</style>
        <body>
        	<p align="center" style="font-size:15px">DAFTAR NOMINATIF CALON PENERIMA BELANJA BANTUAN SOSIAL<br>
			(DNCP-BBS)<br>
			PERSETUJUAN WALIKOTA TAHUN<br>
			ANGGARAN <?php echo date('Y') ?></p>

			<p>Nama OPD : ..............................<br>
			Jenis Belanja Bantuan Sosial: Uang/Barang *)</p>

        	<table border="1" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Nama Lengkap Calon Penerima</th>
                        <th rowspan="2">Alamat Lengkap</th>
                        <th rowspan="2">Rencana Penggunaan</th>
                        <th class="has-sub" colspan="3">Besaran Belanja Bantuan Sosial (Rp)</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Permohonan</th>
                        <th>Hasil Evaluasi</th>
                        <th>Pertimbangan TAPD</th>
                    </tr>
                    <tr>
                    	<th>1</th>
                    	<th>2</th>
                    	<th>3</th>
                    	<th>4</th>
                    	<th>5</th>
                    	<th>6</th>
                    	<th>7</th>
                    	<th>8</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // if($dari!='' && $sampai!='' && $skpd!=0) $Qlist = $this->db->select("id, name, address, maksud_tujuan")->from('proposal')->where('time_entry >=', $dari)->where('time_entry <=', $sampai)->where('skpd_id', $skpd)->order_by('id', 'DESC')->get();                    
                    // else $Qlist = $this->db->select("id, name, address, maksud_tujuan")->from('proposal')->order_by('id', 'DESC')->get();

                    if($kategori || $dari || $sampai || $skpd){
                        $where = '';

                        //kategori
                        if($kategori && !$dari && !$sampai && !$skpd){
                            if($kategori=='all') $where .= "";
                            else $where .= "WHERE type_id = $kategori";
                        }elseif($kategori && $dari && !$sampai && !$skpd){
                            if($kategori=='all') $where .= "WHERE time_entry >= '$dari'";
                            else $where .= "WHERE type_id = $kategori AND time_entry >= '$dari'";
                        }elseif($kategori && !$dari && $sampai && !$skpd){
                            if($kategori=='all') $where .= "WHERE time_entry <= '$sampai'";
                            else $where .= "WHERE type_id = $kategori AND time_entry <= '$sampai'";
                        }elseif($kategori && !$dari && !$sampai && $skpd){
                            if($kategori=='all' AND $skpd=='all') $where .= "";
                            elseif($kategori!='all' AND $skpd=='all') $where .= "WHERE type_id = $kategori";
                            elseif($kategori=='all' AND $skpd!='all') $where .= "WHERE skpd_id = $skpd";
                            else $where .= "WHERE type_id = $kategori AND skpd_id = $skpd";
                        }                        

                        //dari
                        elseif(!$kategori && $dari && !$sampai && !$skpd) $where .= "WHERE time_entry >= '$dari'";
                        elseif(!$kategori && $dari && $sampai && !$skpd) $where .= "WHERE time_entry >= '$dari' AND time_entry <= '$sampai'";
                        elseif(!$kategori && $dari && !$sampai && $skpd){
                            if($skpd=='all') $where .= "WHERE time_entry >= '$dari'";
                            else $where .= "WHERE time_entry >= '$dari' AND skpd_id = $skpd";
                        }

                        //sampai
                        elseif(!$kategori && !$dari && $sampai && !$skpd) $where .= "WHERE time_entry <= '$sampai'";
                        elseif(!$kategori && !$dari && $sampai && $skpd){
                            if($skpd=='all') $where .= "WHERE time_entry <= '$sampai'";
                            else $where .= "WHERE time_entry <= '$sampai' AND skpd_id = $skpd";
                        }

                        //skpd
                        elseif(!$kategori && !$dari && !$sampai && $skpd){
                            if($skpd=='all') $where .= "";
                            else $where .= "WHERE skpd_id = $skpd";
                        }

                        //mixed
                        elseif($kategori && $dari && $sampai && !$skpd){
                            if($kategori=='all') $where .= "WHERE time_entry >= '$dari' AND time_entry <= '$sampai'";
                            else $where .= "WHERE type_id = $kategori AND time_entry >= '$dari' AND time_entry <= '$sampai'";
                        }elseif(!$kategori && $dari && $sampai && $skpd){
                            if($skpd=='all') $where .= "WHERE time_entry >= '$dari' AND time_entry <= '$sampai'";
                            else $where .= "WHERE skpd_id = $skpd AND time_entry >= '$dari' AND time_entry <= '$sampai'";
                        }elseif($kategori && $dari && !$sampai && $skpd){
                            if($kategori=='all') $where .= "WHERE time_entry >= '$dari' AND skpd_id = $skpd";
                            else $where .= "WHERE type_id = $kategori AND time_entry >= '$dari' AND skpd_id = $skpd";
                        }elseif($kategori && !$dari && $sampai && $skpd){
                            if($kategori=='all') $where .= "WHERE time_entry <= '$sampai' AND skpd_id = $skpd";
                            else $where .= "WHERE type_id = $kategori AND time_entry <= '$sampai' AND skpd_id = $skpd";
                        }elseif($kategori && $dari && $sampai && $skpd){
                            if($kategori=='all' && $skpd=='all') $where .= "WHERE time_entry >= '$dari' AND time_entry <= '$sampai'";
                            elseif($kategori!='all' && $skpd=='all') $where .= "WHERE type_id = $kategori AND time_entry >= '$dari' AND time_entry <= '$sampai'";
                            elseif($kategori=='all' && $skpd!='all') $where .= "WHERE time_entry >= '$dari' AND time_entry <= '$sampai' AND skpd_id = $skpd";
                            else $where .= "WHERE type_id = $kategori AND time_entry >= '$dari' AND time_entry <= '$sampai' AND skpd_id = $skpd";
                        }

                        $Qlist = $this->db->query("SELECT id, name, address, maksud_tujuan FROM proposal $where ORDER BY id DESC");
                    }else $Qlist = $this->db->select("id, name, address, maksud_tujuan")->from('proposal')->order_by('id', 'DESC')->get();

                    if($Qlist->num_rows){
                        $i = 1; $total_mohon = 0; $total_evaluasi = 0; $total_timbang = 0;
                        foreach($Qlist->result_object() as $list){
                            $Qmohon = $this->db->query("SELECT SUM(amount) AS mohon FROM proposal_dana WHERE `proposal_id`='$list->id'"); $mohon = $Qmohon->result_object(); 

                            $Qbesar = $this->db->query("SELECT value FROM proposal_checklist WHERE `proposal_id`='$list->id' AND checklist_id IN (26,28,29)"); $besar = $Qbesar->result_object(); 

                            echo '<tr>
                                    <td>'.$i.'</td>
                                    <td>'.$list->name.'</td>
                                    <td>'.$list->address.'</td>
                                    <td>'.$list->maksud_tujuan.'</td>
                                    <td>'; if(isset($mohon[0]->mohon)){ echo 'Rp. '.number_format($mohon[0]->mohon,0,",",".").',-'; $total_mohon += $mohon[0]->mohon; }else echo '-'; echo '</td>
                                    <td>'; if(isset($besar[0]->value)){ echo 'Rp. '.number_format($besar[0]->value,0,",",".").',-'; $total_evaluasi += $besar[0]->value; }else echo '-'; echo '</td>
                                    <td>'; if(isset($besar[1]->value)){ echo 'Rp. '.number_format($besar[1]->value,0,",",".").',-'; $total_timbang += $besar[1]->value; }else echo '-'; echo '</td>
                                    <td>'; if(isset($besar[2]->value)) echo $besar[2]->value; else echo '-'; echo '</td>
                                </tr>';
                            $i++;
                        }
                        echo '<tr>
                        		<td></td>
                        		<td>TOTAL</td>
                        		<td></td>
                        		<td></td>
                        		<td>Rp. '.number_format($total_mohon,0,",",".").',-</td>
                        		<td>Rp. '.number_format($total_evaluasi,0,",",".").',-</td>
                        		<td>Rp. '.number_format($total_timbang,0,",",".").',-</td>
                        		<td></td>
                        	</tr>';
                    }else echo '<tr><td colspan="8">No data.</td></tr>';
                    ?>
                </tbody>
            </table>

            <?php $bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember') ?>
			<p align="right">Bandung, <?php echo date('j').' '.$bulan[date('n')].' '.date('Y'); ?></p>

			<p align="right">WALIKOTA BANDUNG,<br><br><br><br>MOCHAMAD RIDWAN KAMIL</p>
			<!-- <div style="text-align:center;float:right;">WALIKOTA BANDUNG,<br><br><br><br>MOCHAMAD RIDWAN KAMIL</div> -->

			<p>*) Coret yang tidak perlu</p>
        </body>
		</html>
        <?php
	}

	public function search($id)
	{
		header('location:'.config_item('base_portal').'manage/users/surveyor/'.$_POST['q']);
	}
	
	public function autocomplete($id, $val, $selected)
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("content-type: application/x-javascript; charset=tis-620");
		
		if($id=='role'){
			if($val==3){
				echo '<label class="control-label" for="">SKPD</label>
	                    <div class="controls">
	                        <select name="skpd">
	                        <option value="0">-- Silahkan Pilih</option>';                        
	                        $Qkategori = $this->db->select("id, name")->from('skpd')->order_by('id', 'ASC')->get();

	                        foreach($Qkategori->result_object() as $kategori){
	                            echo '<option value="'.$kategori->id.'">'.$kategori->name.'</option>';
	                        }
	                        echo '</select>
	                    </div>';
			}
			else echo '';			
		}
	}
}