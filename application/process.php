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

	public function lapor($tp, $dx=0)
	{
		switch($tp){
			
			case 'send':			
			$name = $_POST['name'];
		    $email = $_POST['email'];
		    $subject = $_POST['subject'];
		    $message = $_POST['message'];

		    date_default_timezone_set('Etc/UTC');

		    require "application/libraries/mail/PHPMailerAutoload.php";

		    //Create a new PHPMailer instance
		    $mail = new PHPMailer;

		    //Tell PHPMailer to use SMTP
		    $mail->isSMTP();

		    //Enable SMTP debugging
		    $mail->SMTPDebug = 2;

		    //Ask for HTML-friendly debug output
		    $mail->Debugoutput = 'html';

		    //Set the hostname of the mail server
		    $mail->Host = 'smtp.gmail.com';

		    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		    $mail->Port = 587;

		    //Set the encryption system to use - ssl (deprecated) or tls
		    $mail->SMTPSecure = 'tls';

		    //Whether to use SMTP authentication
		    $mail->SMTPAuth = true;

		    //Username to use for SMTP authentication - use full email address for gmail
		    $mail->Username = "kontaksabilulungan@gmail.com";

		    //Password to use for SMTP authentication
		    $mail->Password = "s4b1lulung4n";

		    //Set who the message is to be sent from
		    $mail->setFrom($email, $name);

		    //Set an alternative reply-to address
		    //$mail->addReplyTo('replyto@example.com', 'First Last');

		    //Set who the message is to be sent to
		    $mail->addAddress('sabilulungan.bandung@gmail.com', 'Sabilulungan Bansos dan Hibah Online');
		    //$mail->addAddress('mt.ilham@gmail.com', 'Sabilulungan Bansos dan Hibah Online');

		    //Set the subject line
		    $mail->Subject = $subject;

		    $msg = '<p><span style="font-size: medium;"><strong>Lapor - Sabilulungan Bansos dan Hibah Online</strong></span></p>
		            <p>Name : '.$name.'</p>
		            <p>Email : '.$email.'</p>
		            <p>Subject : '.$subject.'</p>
		            <p>Message : '.$message.'</p>';

		    $mail->MsgHTML($msg);

		    if(!$mail->send()) {
		       	$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Laporan Anda Gagal Dikirim.';

				header('location:'.$_SERVER['HTTP_REFERER']);
		    }else{
		    	$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Laporan Anda Berhasil Dikirim.';

				header('location:'.$_SERVER['HTTP_REFERER']); 
		    }

			break;
		}
	}

	public function user($tp, $dx=0)
	{
		//if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'login':
			$uname = strip_tags($_POST['uname']);
			$ifpsd = strip_tags($_POST['pswd']);
			
			if($uname && $ifpsd){
				$Qcheck = $this->db->select("id, name, role_id, skpd_id")->from("user")->where("username", $uname)->where("password", $this->ifunction->pswd($ifpsd))->where("is_active", 1)->get();

				if($Qcheck->num_rows){
					$check = $Qcheck->result_object();
					
					$_SESSION['sabilulungan']['uid'] = $check[0]->id;
					$_SESSION['sabilulungan']['name'] = $check[0]->name;
					$_SESSION['sabilulungan']['role'] = $check[0]->role_id;
					$_SESSION['sabilulungan']['skpd'] = $check[0]->skpd_id;
					$_SESSION['sabilulungan']['base_url'] = base_url();

					$this->db->insert("log", array('user_id' => $check[0]->id, 'activity' => 'login', 'ip' => $_SERVER['REMOTE_ADDR']));
					
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

			case 'register':
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];
			$repswd = $_POST['repswd'];
			$name = $_POST['name'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$ktp = $_POST['ktp'];
			$email = $_POST['email'];
			$skpd = $_SESSION['sabilulungan']['skpd'];
			
			if($uname && $pswd && $repswd && $name && $address && $phone && $ktp && $email){
				if($pswd==$repswd){
					$this->db->insert("user", array('name' => $name, 'email' => $email, 'address' => $address, 'phone' => $phone, 'ktp' => $ktp, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'role_id' => 6, 'skpd_id' => $skpd));

					$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `id`"); $last = $Qlast->result_object(); $dx = $last[0]->id;
					$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'register', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

					$_SESSION['notify']['type'] = 'success';
					$_SESSION['notify']['message'] = 'Pendaftaran berhasil. Silahkan sign in untuk masuk.';

					header('location:'.$_SERVER['HTTP_REFERER']);
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Password tidak sama.';

					header('location:'.$_SERVER['HTTP_REFERER']);
				}
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function hibah($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'daftar':
			$user_id = $_POST['user_id'];			
			$name = $_POST['name'];
			$address = $_POST['address'];
			$judul = $_POST['judul'];
			$latar = $_POST['latar'];
			$maksud = $_POST['maksud'];	
			// $kegiatan = $_POST['kegiatan'];
			$deskripsi = $_POST['deskripsi'];	
			$jumlah = $_POST['jumlah'];	
			$role_id = $_POST['role_id'];		
			$skpd_id = $_SESSION['sabilulungan']['skpd'];

			$carikode = "SELECT id from user";
			$query = $this->db->query($carikode);
			$query->result();

			$jumlah_data = $query->num_rows();

			if ($query) {
			  
				$nilaikode = substr($jumlah_data[0], 1);
				$kode = (int) $nilaikode;
				$kode = $jumlah_data + 1;
			  
				$kode_otomatis = "W".str_pad($kode, 1, "0", STR_PAD_LEFT);
			} else {
				$kode_otomatis = "W1";
			}
			
			//akun
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$no_ktp = $this->input->post('no_ktp');
			$email = $this->input->post('email');
			
			$array = array(
				"username" => $username,
				"password" => $this->ifunction->pswd($password),
				"ktp" => $no_ktp,
				"email" => $email,
				"role_id" => 6,
				"id" => $kode_otomatis,
				
			);
			$this->db->insert('user',$array);
			
			if($name && $address && $judul && $latar && $maksud){
				if(isset($_FILES['proposal']['name']) && $_FILES['proposal']['tmp_name']){
					//$file_allowed = array('application/pdf', 'application/x-pdf', 'application/acrobat');

					$allowedExts = array("pdf"); $extension = end(explode(".", $_FILES["proposal"]["name"]));

					if(!in_array($extension, $allowedExts)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Format proposal harus PDF, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}

					$path = './media/proposal/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['proposal']['name'], $_FILES['proposal']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah proposal, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}
					
					
					
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan masukkan proposal.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}
				//, 'deskripsi_kegiatan' => $kegiatan
				if(isset($_POST['tanggal'])) $this->db->insert("proposal", array('user_id' => $kode_otomatis, 'name' => $name, 'judul' => $judul, 'latar_belakang' => $latar, 'maksud_tujuan' => $maksud, 'address' => $address, 'file' => $new_file_name, 'time_entry' => $_POST['tanggal'], 'skpd_id' => $skpd_id));
				else $this->db->insert("proposal", array('user_id' => $user_id, 'name' => $name, 'judul' => $judul, 'latar_belakang' => $latar, 'maksud_tujuan' => $maksud, 'address' => $address, 'file' => $new_file_name));

				$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `proposal_id`");
				$last = $Qlast->result_object(); $proposal_id = $last[0]->proposal_id;

				if(isset($deskripsi)){
					$i = 1;
					foreach($deskripsi as $index => $value) {
						$this->db->insert("proposal_dana", array('proposal_id' => $proposal_id, 'sequence' => $i, 'description' => $value, 'amount' => $jumlah[$index])); $i++;
					}
				}

				if(isset($_FILES['foto'])){
				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

				    $i = 1;
				    foreach ($file_ary as $file) {
				     	// $file_allowed = array('image/gif', 'image/png', 'image/x-png', 'image/jpg', 'image/jpeg', 'image/pjpeg');
						// if(!in_array($file['type'], $file_allowed, true)){
						// 	$_SESSION['notify']['type'] = 'failed';
						// 	$_SESSION['notify']['message'] = 'Format foto tidak sesuai, silakan ulangi lagi.';

						// 	header('location:'.$_SERVER['HTTP_REFERER']); die();
						// }

				    	$path = './media/proposal_foto/';
					
						$new_file_name = $this->ifunction->upload($path, $file['name'], $file['tmp_name']);
						if(!file_exists($path.$new_file_name)){
							$_SESSION['notify']['type'] = 'failed';
							$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

							header('location:'.$_SERVER['HTTP_REFERER']); die();
						}

						$this->db->insert("proposal_photo", array('proposal_id' => $proposal_id, 'sequence' => $i, 'path' => $new_file_name)); $i++;
				    }
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'daftar_hibah', 'id' => $proposal_id, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pendaftaran hibah bansos berhasil.';

				if($role_id==7) header('location:'.site_url('report'));
				else header('location:'.$_SERVER['HTTP_REFERER']);
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$user_id = $_POST['user_id'];
			$tanggal = $_POST['tanggal'];
			$name = $_POST['name'];
			$address = $_POST['address'];
			$judul = $_POST['judul'];
			$latar = $_POST['latar'];
			$maksud = $_POST['maksud'];	
			// $kegiatan = $_POST['kegiatan'];
			$deskripsi = $_POST['deskripsi'];	
			$jumlah = $_POST['jumlah'];	
			$role_id = $_POST['role_id'];
			$old_proposal = $_POST['old_proposal'];
			$old_foto = $_POST['old_foto'];		
			$dana = $_POST['dana'];		
			$del_dana = $_POST['del_dana'];		
			$del_foto = $_POST['del_foto'];	
			
			if($name && $address && $judul && $latar && $maksud){
				if(isset($_FILES['proposal']['name']) && $_FILES['proposal']['tmp_name']){
					$allowedExts = array("pdf"); $extension = end(explode(".", $_FILES["proposal"]["name"]));

					if(!in_array($extension, $allowedExts)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Format proposal harus PDF, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}

					$path = './media/proposal/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['proposal']['name'], $_FILES['proposal']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah proposal, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}else $this->ifunction->un_link($path.$old_proposal);
				}else $new_file_name = $old_proposal;

				$this->db->update("proposal", array('name' => $name, 'judul' => $judul, 'latar_belakang' => $latar, 'maksud_tujuan' => $maksud, 'address' => $address, 'file' => $new_file_name, 'time_entry' => $tanggal), array('id' => $dx));

				if(isset($deskripsi)){
					$i = 1; $j = count($dana);
					foreach($deskripsi as $index => $value) {
						if($i <= $j) $this->db->update("proposal_dana", array('description' => $value, 'amount' => $jumlah[$index]), array('proposal_id' => $dx, 'sequence' => $i));
						else $this->db->insert("proposal_dana", array('proposal_id' => $dx, 'sequence' => $i, 'description' => $value, 'amount' => $jumlah[$index]));

						$i++;
					}
				}

				if(isset($del_dana)){
					foreach($del_dana as $index => $value) {
						$this->db->delete("proposal_dana", array('sequence' => $value, 'proposal_id' => $dx));
					}
				}

				if(isset($_FILES['foto'])){
					$Qurut = $this->db->query("SELECT sequence FROM proposal_photo WHERE `proposal_id`='$dx' ORDER BY sequence DESC LIMIT 1"); $urut = $Qurut->result_object();
					$Qpos = $this->db->query("SELECT sequence FROM proposal_photo WHERE `proposal_id`='$dx' AND is_nphd='0' ORDER BY sequence ASC"); $pos = $Qpos->result_object();

				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

				    $i = 1; $j = count($old_foto); $k = $urut[0]->sequence+1;
				    foreach ($file_ary as $file => $value) {
				    	$path = './media/proposal_foto/';
					
						if(!empty($value['tmp_name'])){
							$new_file_name = $this->ifunction->upload($path, $value['name'], $value['tmp_name']);
							if(!file_exists($path.$new_file_name)){
								$_SESSION['notify']['type'] = 'failed';
								$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

								header('location:'.$_SERVER['HTTP_REFERER']); die();
							}else $this->ifunction->un_link($path.$old_foto[$file]);

							if($i <= $j){
								$this->db->update("proposal_photo", array('path' => $new_file_name), array('proposal_id' => $dx, 'sequence' => $pos[$file]->sequence));
							}else{
								$this->db->insert("proposal_photo", array('proposal_id' => $dx, 'sequence' => $k, 'path' => $new_file_name)); $k++;						
							}
						}

						$i++;
				    }
				}

				if(isset($del_foto)){
					foreach($del_foto as $index => $value) {
						$Qpos = $this->db->query("SELECT `path` FROM proposal_photo WHERE `proposal_id`='$dx' AND sequence='$value'");
						$pos = $Qpos->result_object(); $path = './media/proposal_foto/';

						$this->ifunction->un_link($path.$pos[0]->path);
						$this->db->delete("proposal_photo", array('sequence' => $value, 'proposal_id' => $dx));
					}
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_hibah', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Koreksi hibah bansos berhasil.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function realisasi($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			//Koordinator
			case 'add_laporan':
			$tahun = $_POST['tahun'];
			$anggaran = $_POST['anggaran'];
			$realisasi_rp = $_POST['realisasi_rp'];
			$realisasi_persen = $_POST['realisasi_persen'];
			$penerima_cair = $_POST['penerima_cair'];
			$penerima_lapor = $_POST['penerima_lapor'];	
			$nilai_lapor = $_POST['nilai_lapor'];	
			
			if($tahun && $anggaran && $realisasi_rp && $realisasi_persen && $penerima_cair && $penerima_lapor && $nilai_lapor){
				if(isset($_FILES['laporan']['name']) && $_FILES['laporan']['tmp_name']){
					$allowedExts = array("pdf"); $extension = end(explode(".", $_FILES["laporan"]["name"]));

					if(!in_array($extension, $allowedExts)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Format Laporan harus PDF, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}

					$path = './media/laporan/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['laporan']['name'], $_FILES['laporan']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah Laporan, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan masukkan Laporan.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}

				$this->db->insert("laporan", array('tahun' => $tahun, 'anggaran' => $anggaran, 'realisasi_rp' => $realisasi_rp, 'realisasi_persen' => $realisasi_persen, 'penerima_cair' => $penerima_cair, 'penerima_lapor' => $penerima_lapor, 'nilai_lapor' => $nilai_lapor, 'file' => $new_file_name));

				$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `id`"); $last = $Qlast->result_object(); $dx = $last[0]->id;
				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_laporan', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Laporan berhasil ditambahkan.';

				header('location:'.site_url('realisasi'));			
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit_laporan':
			$tahun = $_POST['tahun'];
			$anggaran = $_POST['anggaran'];
			$realisasi_rp = $_POST['realisasi_rp'];
			$realisasi_persen = $_POST['realisasi_persen'];
			$penerima_cair = $_POST['penerima_cair'];
			$penerima_lapor = $_POST['penerima_lapor'];	
			$nilai_lapor = $_POST['nilai_lapor'];
			$old_laporan = $_POST['old_laporan'];	
			
			if($tahun && $anggaran && $realisasi_rp && $realisasi_persen && $penerima_cair && $penerima_lapor && $nilai_lapor){
				if(isset($_FILES['laporan']['name']) && $_FILES['laporan']['tmp_name']){
					$allowedExts = array("pdf"); $extension = end(explode(".", $_FILES["laporan"]["name"]));

					if(!in_array($extension, $allowedExts)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Format Laporan harus PDF, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}

					$path = './media/laporan/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['laporan']['name'], $_FILES['laporan']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah Laporan, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}else $this->ifunction->un_link($path.$old_laporan);
				}else $new_file_name = $old_laporan;

				$this->db->update("laporan", array('tahun' => $tahun, 'anggaran' => $anggaran, 'realisasi_rp' => $realisasi_rp, 'realisasi_persen' => $realisasi_persen, 'penerima_cair' => $penerima_cair, 'penerima_lapor' => $penerima_lapor, 'nilai_lapor' => $nilai_lapor, 'file' => $new_file_name), array('laporan_id' => $dx));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_laporan', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Laporan berhasil diedit.';

				header('location:'.site_url('realisasi'));						
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete_laporan':
			$Qpos = $this->db->query("SELECT `file` FROM laporan WHERE `laporan_id`='$dx'");
			$pos = $Qpos->result_object(); $path = './media/laporan/';

			$this->ifunction->un_link($path.$pos[0]->file);
			$this->db->delete("laporan", array('laporan_id' => $dx));

			$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'delete_laporan', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Laporan berhasil dihapus.';

			header('location:'.site_url('realisasi'));
			break;
		}
	}

	public function cms($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			//Koordinator
			case 'add_koordinator':
			
			$carikode = "SELECT id from user";
			$query = $this->db->query($carikode);
			$query->result();

			$jumlah_data = $query->num_rows();

			if ($query) {
			  
				$nilaikode = substr($jumlah_data[0], 1);
				$kode = (int) $nilaikode;
				$kode = $jumlah_data + 1;
			  
				$kode_otomatis = "U".str_pad($kode, 1, "0", STR_PAD_LEFT);
			} else {
				$kode_otomatis = "U1";
			}
			
			$id = $kode_otomatis;
			$role = $_POST['role'];
			$skpd = $_POST['skpd'];
			$name = $_POST['name'];
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];
			$repswd = $_POST['repswd'];	
			$status = $_POST['status'];	
			
			if($role && $name && $uname && $pswd && $repswd){
				if($pswd == $repswd){
					if($skpd) $this->db->insert("user", array('id' => $id, 'name' => $name, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'role_id' => $role, 'is_skpd' => 1, 'skpd_id' => $skpd, 'is_active' => $status));
					else $this->db->insert("user", array('id' => $id, 'name' => $name, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'role_id' => $role, 'is_active' => $status));

					$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `id`"); $last = $Qlast->result_object(); $dx = $last[0]->id;
					$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_koordinator', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

					$_SESSION['notify']['type'] = 'success';
					$_SESSION['notify']['message'] = 'Koordinator berhasil ditambahkan.';

					header('location:'.site_url('cms/koordinator'));
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Password tidak sama.';

					header('location:'.$_SERVER['HTTP_REFERER']);
				}				
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit_koordinator':
			$role = $_POST['role'];
			$skpd = $_POST['skpd'];
			$name = $_POST['name'];
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];
			$repswd = $_POST['repswd'];	
			$status = $_POST['status'];		
			
			if($role && $name && $uname){
				if($pswd != '' || $repswd != ''){
					if($pswd == $repswd){
						if($skpd) $this->db->update("user", array('name' => $name, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'role_id' => $role, 'is_skpd' => 1, 'skpd_id' => $skpd, 'is_active' => $status), array('id' => $dx));
						else $this->db->update("user", array('name' => $name, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'role_id' => $role, 'is_skpd' => 0, 'skpd_id' => NULL, 'is_active' => $status), array('id' => $dx));

						$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_koordinator', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

						$_SESSION['notify']['type'] = 'success';
						$_SESSION['notify']['message'] = 'Koordinator berhasil diedit.';

						header('location:'.site_url('cms/koordinator'));
					}else{
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Password tidak sama.';

						header('location:'.$_SERVER['HTTP_REFERER']);
					}
				}else{
					if($skpd) $this->db->update("user", array('name' => $name, 'username' => $uname, 'role_id' => $role, 'is_skpd' => 1, 'skpd_id' => $skpd, 'is_active' => $status), array('id' => $dx));
					else $this->db->update("user", array('name' => $name, 'username' => $uname, 'role_id' => $role, 'is_skpd' => 0, 'skpd_id' => NULL, 'is_active' => $status), array('id' => $dx));

					$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_koordinator', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

					$_SESSION['notify']['type'] = 'success';
					$_SESSION['notify']['message'] = 'Koordinator berhasil diedit.';

					header('location:'.site_url('cms/koordinator'));					
				}							
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete_koordinator':
			$this->db->delete("user", array('id' => $dx));

			$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'delete_koordinator', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Koordinator berhasil dihapus.';

			header('location:'.site_url('cms/koordinator'));
			break;


			//Umum
			case 'add_umum':
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];
			$repswd = $_POST['repswd'];
			$name = $_POST['name'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$ktp = $_POST['ktp'];
			$email = $_POST['email'];
			$status = $_POST['status'];	
			
			if($uname && $pswd && $repswd && $name && $address && $phone && $ktp && $email){
				if($pswd==$repswd){
					$this->db->insert("user", array('name' => $name, 'email' => $email, 'address' => $address, 'phone' => $phone, 'ktp' => $ktp, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'role_id' => 6, 'is_active' => $status));

					$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `id`"); $last = $Qlast->result_object(); $dx = $last[0]->id;
					$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_umum', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

					$_SESSION['notify']['type'] = 'success';
					$_SESSION['notify']['message'] = 'Pendaftaran berhasil. Silahkan sign in untuk masuk.';

					header('location:'.site_url('cms/umum'));
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Password tidak sama.';

					header('location:'.$_SERVER['HTTP_REFERER']);
				}
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit_umum':
			$uname = $_POST['uname'];
			$pswd = $_POST['pswd'];
			$repswd = $_POST['repswd'];
			$name = $_POST['name'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$ktp = $_POST['ktp'];
			$email = $_POST['email'];
			$status = $_POST['status'];	
			
			if($uname && $name && $address && $phone && $ktp && $email){
				if($pswd != '' || $repswd != ''){
					if($pswd == $repswd){						
						$this->db->update("user", array('name' => $name, 'email' => $email, 'address' => $address, 'phone' => $phone, 'ktp' => $ktp, 'username' => $uname, 'password' => $this->ifunction->pswd($pswd), 'is_active' => $status), array('id' => $dx));

						$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_umum', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

						$_SESSION['notify']['type'] = 'success';
						$_SESSION['notify']['message'] = 'Pengguna umum berhasil diedit.';

						header('location:'.site_url('cms/umum'));
					}else{
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Password tidak sama.';

						header('location:'.$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->db->update("user", array('name' => $name, 'email' => $email, 'address' => $address, 'phone' => $phone, 'ktp' => $ktp, 'username' => $uname, 'is_active' => $status), array('id' => $dx));

					$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_umum', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

					$_SESSION['notify']['type'] = 'success';
					$_SESSION['notify']['message'] = 'Pengguna umum berhasil diedit.';

					header('location:'.site_url('cms/umum'));					
				}							
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete_umum':
			$this->db->delete("user", array('id' => $dx));

			$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'delete_umum', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Pengguna umum berhasil dihapus.';

			header('location:'.site_url('cms/umum'));
			break;


			//Home
			case 'home':			
			if(isset($_FILES['image1']['name']) && $_FILES['image1']['tmp_name']){
				$path = './media/cms/';
				
				$new_foto_name = $this->ifunction->upload($path, $_FILES['image1']['name'], $_FILES['image1']['tmp_name']);
				if(!file_exists($path.$new_foto_name)){
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah gambar, silakan ulangi lagi.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}else $this->ifunction->un_link($path.$_POST['old_image1']);

				$this->db->update("cms", array('content' => $new_foto_name), array('page_id' => 'home', 'sequence' => $_POST['sequence1']));				
			}

			if(isset($_FILES['image2']['name']) && $_FILES['image2']['tmp_name']){
				$path = './media/cms/';
				
				$new_foto_name = $this->ifunction->upload($path, $_FILES['image2']['name'], $_FILES['image2']['tmp_name']);
				if(!file_exists($path.$new_foto_name)){
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah gambar, silakan ulangi lagi.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}else $this->ifunction->un_link($path.$_POST['old_image2']);

				$this->db->update("cms", array('content' => $new_foto_name), array('page_id' => 'home', 'sequence' => $_POST['sequence2']));
			}

			$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'home', 'ip' => $_SERVER['REMOTE_ADDR']));	

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Home berhasil diedit.';

			header('location:'.site_url('cms/home'));						
			break;


			//Tentang
			case 'tentang':
			$content = $_POST['content'];	
			
			if($content){
				$this->db->update("cms", array('content' => $content), array('page_id' => 'tentang', 'sequence' => $_POST['sequence0']));

				if(isset($_FILES['image1']['name']) && $_FILES['image1']['tmp_name']){
					$path = './media/cms/';
					
					$new_foto_name = $this->ifunction->upload($path, $_FILES['image1']['name'], $_FILES['image1']['tmp_name']);
					if(!file_exists($path.$new_foto_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah gambar, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}else $this->ifunction->un_link($path.$_POST['old_image1']);

					$this->db->update("cms", array('content' => $new_foto_name), array('page_id' => 'tentang', 'sequence' => $_POST['sequence1']));				
				}

				if(isset($_FILES['image2']['name']) && $_FILES['image2']['tmp_name']){
					$path = './media/cms/';
					
					$new_foto_name = $this->ifunction->upload($path, $_FILES['image2']['name'], $_FILES['image2']['tmp_name']);
					if(!file_exists($path.$new_foto_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah gambar, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}else $this->ifunction->un_link($path.$_POST['old_image2']);

					$this->db->update("cms", array('content' => $new_foto_name), array('page_id' => 'tentang', 'sequence' => $_POST['sequence2']));
				}	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'tentang', 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Tentang Sabilulungan berhasil diedit.';

				header('location:'.site_url('cms/tentang'));							
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;


			//Peraturan
			case 'add_peraturan':
			$title = $_POST['title'];	
			
			if($title){
				if(isset($_FILES['file']['name']) && $_FILES['file']['tmp_name']){
					$path = './media/peraturan/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['file']['name'], $_FILES['file']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah file, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan masukkan file peraturan.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}

				$Qurut = $this->db->query("SELECT sequence FROM cms WHERE `page_id`='peraturan' ORDER BY sequence DESC LIMIT 1"); $urut = $Qurut->result_object(); $i = $urut[0]->sequence+1;

				$this->db->insert("cms", array('page_id' => 'peraturan', 'sequence' => $i, 'title' => $title, 'content' => $new_file_name, 'type' => 3));

				$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `id`"); $last = $Qlast->result_object(); $dx = $last[0]->id;
				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_peraturan', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));			

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Peraturan berhasil ditambahkan.';

				header('location:'.site_url('cms/peraturan'));							
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit_peraturan':
			$title = $_POST['title'];	
			
			if($title){
				if(isset($_FILES['file']['name']) && $_FILES['file']['tmp_name']){
					$path = './media/peraturan/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['file']['name'], $_FILES['file']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah file, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}else $this->ifunction->un_link($path.$_POST['old_file']);					
				}else $new_file_name = $_POST['old_file'];

				$this->db->update("cms", array('title' => $title, 'content' => $new_file_name), array('page_id' => 'peraturan', 'sequence' => $_POST['sequence']));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_peraturan', 'id' => $_POST['sequence'], 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Peraturan berhasil diedit.';

				header('location:'.site_url('cms/peraturan'));							
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete_peraturan':
			$Qurut = $this->db->query("SELECT content FROM cms WHERE `page_id`='peraturan' AND sequence='$dx'");
			$urut = $Qurut->result_object(); $path = './media/peraturan/';

			$this->ifunction->un_link($path.$urut[0]->content);
			$this->db->delete("cms", array('sequence' => $dx, 'page_id' => 'peraturan'));

			$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'delete_peraturan', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Peraturan berhasil dihapus.';

			header('location:'.site_url('cms/peraturan'));
			break;


			//Pengumuman
			case 'add_pengumuman':
			$judul = $_POST['judul'];
			$konten = $_POST['konten'];	
			
			if($judul && $konten){	
				$this->db->insert("pengumuman", array('judul' => $judul, 'konten' => $konten));

				$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `id`"); $last = $Qlast->result_object(); $dx = $last[0]->id;
				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_pengumuman', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));			

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pengumuman berhasil ditambahkan.';

				header('location:'.site_url('cms/pengumuman'));							
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit_pengumuman':
			$judul = $_POST['judul'];
			$konten = $_POST['konten'];		
			
			if($judul && $konten){		

				$this->db->update("pengumuman", array('judul' => $judul, 'konten' => $konten), array('pengumuman_id' => $dx));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_pengumuman', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pengumuman berhasil diedit.';

				header('location:'.site_url('cms/pengumuman'));							
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'delete_pengumuman':
			$this->db->delete("pengumuman", array('pengumuman_id' => $dx));

			$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'delete_pengumuman', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

			$_SESSION['notify']['type'] = 'success';
			$_SESSION['notify']['message'] = 'Pengumuman berhasil dihapus.';

			header('location:'.site_url('cms/pengumuman'));
			break;
		}
	}

	public function admin($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'nphd':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$koreksi = $_POST['koreksi'];		
			
			if($user_id && $role_id){
				if(isset($_FILES['nphd']['name']) && $_FILES['nphd']['tmp_name']){
					$allowedExts = array("pdf"); $extension = end(explode(".", $_FILES["nphd"]["name"]));

					if(!in_array($extension, $allowedExts)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Format NPHD harus PDF, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}

					$path = './media/nphd/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['nphd']['name'], $_FILES['nphd']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah NPHD, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan masukkan NPHD.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}

				$this->db->update("proposal", array('nphd' => $new_file_name), array('id' => $dx));

				// if(isset($_FILES['foto']['name']) && $_FILES['foto']['tmp_name']){
				// 	$path = './media/proposal_foto/';
					
				// 	$new_foto_name = $this->ifunction->upload($path, $_FILES['foto']['name'], $_FILES['foto']['tmp_name']);
				// 	if(!file_exists($path.$new_foto_name)){
				// 		$_SESSION['notify']['type'] = 'failed';
				// 		$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

				// 		header('location:'.$_SERVER['HTTP_REFERER']); die();
				// 	}
				// }else{
				// 	$_SESSION['notify']['type'] = 'failed';
				// 	$_SESSION['notify']['message'] = 'Silahkan masukkan foto.';

				// 	header('location:'.$_SERVER['HTTP_REFERER']); die();
				// }

				// $this->db->update("proposal", array('nphd' => $new_file_name, 'foto' => $new_foto_name), array('id' => $dx));

				if(isset($_FILES['foto'])){
					$Qurut = $this->db->query("SELECT sequence FROM proposal_photo WHERE `proposal_id`='$dx' ORDER BY sequence DESC LIMIT 1"); $urut = $Qurut->result_object(); 

				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

				    $i = $urut[0]->sequence+1;
				    foreach ($file_ary as $file) {
				    	$path = './media/proposal_foto/';
					
						$new_file_name = $this->ifunction->upload($path, $file['name'], $file['tmp_name']);
						if(!file_exists($path.$new_file_name)){
							$_SESSION['notify']['type'] = 'failed';
							$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

							header('location:'.$_SERVER['HTTP_REFERER']); die();
						}

						$this->db->insert("proposal_photo", array('proposal_id' => $dx, 'sequence' => $i, 'path' => $new_file_name, 'is_nphd' => 1)); $i++;
				    }
				}

				if($koreksi){
					$i = 1;
					foreach($koreksi as $index => $value) {
						$this->db->update("proposal_dana", array('correction' => $value), array('proposal_id' => $dx, 'sequence' => $i)); $i++;
					}
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_nphd', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));				

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'NPHD berhasil ditambahkan.';

				header('location:'.site_url('report'));
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$koreksi = $_POST['koreksi'];
			$old_nphd = $_POST['old_nphd'];
			$old_foto = $_POST['old_foto'];	
			$del_foto = $_POST['del_foto'];	
			
			if($user_id && $role_id){
				if(isset($_FILES['nphd']['name']) && $_FILES['nphd']['tmp_name']){
					$allowedExts = array("pdf"); $extension = end(explode(".", $_FILES["nphd"]["name"]));

					if(!in_array($extension, $allowedExts)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Format NPHD harus PDF, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}

					$path = './media/nphd/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['nphd']['name'], $_FILES['nphd']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah NPHD, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}else $this->ifunction->un_link($path.$old_nphd);
				}else $new_file_name = $old_nphd;

				$this->db->update("proposal", array('nphd' => $new_file_name), array('id' => $dx));

				if(isset($_FILES['foto'])){
					$Qurut = $this->db->query("SELECT sequence FROM proposal_photo WHERE `proposal_id`='$dx' ORDER BY sequence DESC LIMIT 1"); $urut = $Qurut->result_object(); 
					$Qpos = $this->db->query("SELECT sequence FROM proposal_photo WHERE `proposal_id`='$dx' AND is_nphd='1' ORDER BY sequence ASC"); $pos = $Qpos->result_object(); 

				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

				    $i = 1; $j = count($old_foto); $k = $urut[0]->sequence+1;
				    foreach ($file_ary as $file => $value) {
				    	$path = './media/proposal_foto/';
					
						if(!empty($value['tmp_name'])){
							$new_file_name = $this->ifunction->upload($path, $value['name'], $value['tmp_name']);
							if(!file_exists($path.$new_file_name)){
								$_SESSION['notify']['type'] = 'failed';
								$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

								header('location:'.$_SERVER['HTTP_REFERER']); die();
							}else $this->ifunction->un_link($path.$old_foto[$file]);

							if($i <= $j){
								$this->db->update("proposal_photo", array('path' => $new_file_name), array('proposal_id' => $dx, 'sequence' => $pos[$file]->sequence));
							}else{
								$this->db->insert("proposal_photo", array('proposal_id' => $dx, 'sequence' => $k, 'path' => $new_file_name, 'is_nphd' => 1)); $k++;
							}
						}

						$i++;
				    }
				}

				if(isset($del_foto)){
					foreach($del_foto as $index => $value) {
						$Qpos = $this->db->query("SELECT `path` FROM proposal_photo WHERE `proposal_id`='$dx' AND sequence='$value'");
						$pos = $Qpos->result_object(); $path = './media/proposal_foto/';

						$this->ifunction->un_link($path.$pos[0]->path);
						$this->db->delete("proposal_photo", array('sequence' => $value, 'proposal_id' => $dx));
					}
				}

				if($koreksi){
					$i = 1;
					foreach($koreksi as $index => $value) {
						$this->db->update("proposal_dana", array('correction' => $value), array('proposal_id' => $dx, 'sequence' => $i)); $i++;
					}
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_nphd', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'NPHD berhasil diedit.';

				header('location:'.site_url('report'));
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'lpj':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$tanggal = $_POST['tanggal'];		
			
			if($user_id && $role_id && $tanggal){	
				$this->db->update("proposal", array('tanggal_lpj' => $tanggal), array('id' => $dx));

				if(isset($_FILES['foto'])){
				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

				    $i = 1;
				    foreach ($file_ary as $file) {
				    	$path = './media/proposal_lpj/';
					
						$new_file_name = $this->ifunction->upload($path, $file['name'], $file['tmp_name']);
						if(!file_exists($path.$new_file_name)){
							$_SESSION['notify']['type'] = 'failed';
							$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

							header('location:'.$_SERVER['HTTP_REFERER']); die();
						}

						$this->db->insert("proposal_lpj", array('proposal_id' => $dx, 'sequence' => $i, 'path' => $new_file_name, 'type' => 1)); $i++;
				    }
				}

				if(isset($_POST['video'])){
					$video = $_POST['video'];

					$j = $i;
					foreach($video as $index => $value) {
					    preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $value, $matches);
					    $id = $matches[1]; $url = 'http://www.youtube.com/embed/'.$id.'?autoplay=1';

						$this->db->insert("proposal_lpj", array('proposal_id' => $dx, 'sequence' => $j, 'path' => $url, 'type' => 2)); $j++;
					}
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_lpj', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'LPJ berhasil ditambahkan.';

				header('location:'.site_url('report'));
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'view':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$tanggal = $_POST['tanggal'];
			$old_foto = $_POST['old_foto'];	
			$old_video = $_POST['old_video'];
			$del_foto = $_POST['del_foto'];
			$del_video = $_POST['del_video'];	
			
			if($user_id && $role_id && $tanggal){	
				$this->db->update("proposal", array('tanggal_lpj' => $tanggal), array('id' => $dx));

				if(isset($_FILES['foto'])){
					$Qurut = $this->db->query("SELECT sequence FROM proposal_lpj WHERE `proposal_id`='$dx' ORDER BY sequence DESC LIMIT 1"); $urut = $Qurut->result_object();
					$Qpos = $this->db->query("SELECT sequence FROM proposal_lpj WHERE `proposal_id`='$dx' AND type='1' ORDER BY sequence ASC"); $pos = $Qpos->result_object();

				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

					$i = 1; $j = count($old_foto); $k = $urut[0]->sequence+1;
					foreach ($file_ary as $file => $value) {
						$path = './media/proposal_lpj/';
					
						if(!empty($value['tmp_name'])){
							$new_file_name = $this->ifunction->upload($path, $value['name'], $value['tmp_name']);
							if(!file_exists($path.$new_file_name)){
								$_SESSION['notify']['type'] = 'failed';
								$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

								header('location:'.$_SERVER['HTTP_REFERER']); die();
							}else $this->ifunction->un_link($path.$old_foto[$file]);

							if($i <= $j){
								$this->db->update("proposal_lpj", array('path' => $new_file_name), array('proposal_id' => $dx, 'sequence' => $pos[$file]->sequence));
							}else{
								$this->db->insert("proposal_lpj", array('proposal_id' => $dx, 'sequence' => $k, 'path' => $new_file_name, 'type' => 1)); $k++;
							}
						}
						$i++;						
					}
				}

				if(isset($del_foto)){
					foreach($del_foto as $index => $value) {
						$Qpos = $this->db->query("SELECT `path` FROM proposal_lpj WHERE `proposal_id`='$dx' AND sequence='$value'");
						$pos = $Qpos->result_object(); $path = './media/proposal_lpj/';

						$this->ifunction->un_link($path.$pos[0]->path);
						$this->db->delete("proposal_lpj", array('sequence' => $value, 'proposal_id' => $dx));
					}
				}

				if(isset($_POST['video'])){
					$Qurut = $this->db->query("SELECT sequence FROM proposal_lpj WHERE `proposal_id`='$dx' ORDER BY sequence DESC LIMIT 1"); $urut = $Qurut->result_object();
					$Qpos = $this->db->query("SELECT sequence FROM proposal_lpj WHERE `proposal_id`='$dx' AND type='2' ORDER BY sequence ASC"); $pos = $Qpos->result_object();

					$video = $_POST['video'];

					$i = 1; $j = count($old_video); $k = $urut[0]->sequence+1;
					foreach($video as $index => $value) {						
					    preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $value, $matches);
					    $id = $matches[1]; $url = 'http://www.youtube.com/embed/'.$id.'?autoplay=1';

					    if(!empty($id)){
							if($i <= $j){
								$this->db->update("proposal_lpj", array('path' => $url), array('proposal_id' => $dx, 'sequence' => $pos[$index]->sequence));
							}else{
								$this->db->insert("proposal_lpj", array('proposal_id' => $dx, 'sequence' => $k, 'path' => $url, 'type' => 2)); $k++;
							}		
						}				
						$i++;
					}
				}

				if(isset($del_video)){
					foreach($del_video as $index => $value) {
						$this->db->delete("proposal_lpj", array('sequence' => $value, 'proposal_id' => $dx));
					}
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_lpj', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'LPJ berhasil diedit.';

				header('location:'.site_url('report'));
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'detail':
			$tanggal = $_POST['tanggal'];
			$tanggal1 = $_POST['tanggal1'];
			$tanggal2 = $_POST['tanggal2'];
			$tanggal3 = $_POST['tanggal3'];
			$tanggal4 = $_POST['tanggal4'];
			$tanggal5 = $_POST['tanggal5'];
			$tanggal6 = $_POST['tanggal6'];
			$tanggal7 = $_POST['tanggal7'];	
			
			if($tanggal){
				$this->db->update("proposal", array('time_entry' => $tanggal), array('id' => $dx));	

				if(isset($tanggal1)) $this->db->update("proposal_approval", array('time_entry' => $tanggal1), array('proposal_id' => $dx, 'flow_id' => 1));		
				if(isset($tanggal2)) $this->db->update("proposal_approval", array('time_entry' => $tanggal2), array('proposal_id' => $dx, 'flow_id' => 2));
				if(isset($tanggal3)) $this->db->update("proposal_approval", array('time_entry' => $tanggal3), array('proposal_id' => $dx, 'flow_id' => 3));
				if(isset($tanggal4)) $this->db->update("proposal_approval", array('time_entry' => $tanggal4), array('proposal_id' => $dx, 'flow_id' => 4));
				if(isset($tanggal5)) $this->db->update("proposal_approval", array('time_entry' => $tanggal5), array('proposal_id' => $dx, 'flow_id' => 5));
				if(isset($tanggal6)) $this->db->update("proposal_approval", array('time_entry' => $tanggal6), array('proposal_id' => $dx, 'flow_id' => 6));
				if(isset($tanggal7)) $this->db->update("proposal_approval", array('time_entry' => $tanggal7), array('proposal_id' => $dx, 'flow_id' => 7));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'edit_detail', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Detail proposal berhasil diedit.';

				header('location:'.site_url('report'));
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function input($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'daftar':
			$user_id = $_POST['user_id'];
			$user = $_POST['user'];
			$name = $_POST['name'];
			$address = $_POST['address'];
			$judul = $_POST['judul'];
			$latar = $_POST['latar'];
			$maksud = $_POST['maksud'];	
			$skpd = $_POST['skpd'];
			$tahap = $_POST['tahap'];
			$keterangan = $_POST['keterangan'];
			// $kegiatan = $_POST['kegiatan'];
			$deskripsi = $_POST['deskripsi'];	
			$jumlah = $_POST['jumlah'];	
			$role_id = $_POST['role_id'];		
			
			if($name && $address && $judul && $latar && $maksud){
				if(isset($_FILES['proposal']['name']) && $_FILES['proposal']['tmp_name']){
					$path = './media/proposal/';
					
					$new_file_name = $this->ifunction->upload($path, $_FILES['proposal']['name'], $_FILES['proposal']['tmp_name']);
					if(!file_exists($path.$new_file_name)){
						$_SESSION['notify']['type'] = 'failed';
						$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah proposal, silakan ulangi lagi.';

						header('location:'.$_SERVER['HTTP_REFERER']); die();
					}
				}else{
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan masukkan proposal.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}
				//, 'deskripsi_kegiatan' => $kegiatan
				$this->db->insert("proposal", array('user' => $user, 'user_id' => $user_id, 'name' => $name, 'judul' => $judul, 'latar_belakang' => $latar, 'maksud_tujuan' => $maksud, 'address' => $address, 'skpd_id' => $skpd, 'current_stat' => $tahap, 'file' => $new_file_name));

				$Qlast = $this->db->query("SELECT LAST_INSERT_ID() AS `proposal_id`");
				$last = $Qlast->result_object(); $proposal_id = $last[0]->proposal_id;

				$this->db->insert("proposal_checklist", array('proposal_id' => $proposal_id, 'checklist_id' => 13, 'value' => $keterangan));

				for ($i=1; $i <= $tahap; $i++) { 
					$this->db->insert("proposal_approval", array('proposal_id' => $proposal_id, 'user_id' => $user_id, 'flow_id' => $i, 'action' => 1));

					$this->db->insert("proposal_approval_history", array('proposal_id' => $proposal_id, 'user_id' => $user_id, 'flow_id' => $i, 'role_id' => $role_id, 'action' => 1));
				}

				if($deskripsi){
					$i = 1;
					foreach($deskripsi as $index => $value) {
						$this->db->insert("proposal_dana", array('proposal_id' => $proposal_id, 'sequence' => $i, 'description' => $value, 'amount' => $jumlah[$index])); $i++;
					}
				}

				if(isset($_FILES['foto'])){
				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

				    $i = 1;
				    foreach ($file_ary as $file) {
				    	$path = './media/proposal_foto/';
					
						$new_file_name = $this->ifunction->upload($path, $file['name'], $file['tmp_name']);
						if(!file_exists($path.$new_file_name)){
							$_SESSION['notify']['type'] = 'failed';
							$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

							header('location:'.$_SERVER['HTTP_REFERER']); die();
						}

						$this->db->insert("proposal_photo", array('proposal_id' => $proposal_id, 'sequence' => $i, 'path' => $new_file_name)); $i++;
				    }
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'daftar_hibah', 'id' => $proposal_id, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pendaftaran hibah bansos berhasil.';

				if($role_id==7) header('location:'.site_url('report'));
				else header('location:'.$_SERVER['HTTP_REFERER']);
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function lpj($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){

			case 'add':
			$user_id = $_POST['user_id'];	
			$role_id = $_POST['role_id'];	
			$deskripsi = $_POST['deskripsi'];		
			
			if($user_id && $role_id){
				if(isset($_FILES['foto'])){
				    $file_ary = $this->ifunction->reArrayFiles($_FILES['foto']);

				    $i = 1;
				    foreach ($file_ary as $index => $file) {
				    	/*$path = './media/proposal_lpj/';
					
						$new_file_name = $this->ifunction->upload($path, $file['name'], $file['tmp_name']);
						if(!file_exists($path.$new_file_name)){
							$_SESSION['notify']['type'] = 'failed';
							$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

							header('location:'.$_SERVER['HTTP_REFERER']); die();
						}*/
						
						/*$fileName = $_FILES['foto']['name'];
						$uploaddir = 'media/proposal_lpj/';
						$new_file_name = $fileName;
								 
						move_uploaded_file($_FILES['foto']['tmp_name'], "media/proposal_lpj/".$_FILES['foto']['name']);
						*/
						
						$config=array(
							'upload_path' => './media/proposal_lpj', //lokasi gambar akan di simpan
							'allowed_types' => 'jpg|jpeg|png|gif', //ekstensi gambar yang boleh di uanggah
							'file_name' => url_title($this->input->post('foto')) //nama gambar
						);
						
						$this->load->library('upload', $config);
						
						if (!$this->upload->do_upload('foto')){
								$_SESSION['notify']['type'] = 'failed';
								$_SESSION['notify']['message'] = 'Terjadi kesalahan saat mengunggah foto, silakan ulangi lagi.';

								header('location:'.$_SERVER['HTTP_REFERER']); die();	
						}else{	
							$new_file_name = $this->upload->file_name;
							$this->db->insert("proposal_lpj", array('proposal_id' => $dx, 'sequence' => $i, 'path' => $new_file_name, 'description' => $deskripsi[$index])); $i++;
						}
					}
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'add_lpj', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Laporan Pertanggung Jawaban berhasil ditambahkan.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function tatausaha($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){			

			case 'periksa':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$kategori = $_POST['kategori'];
			$kelengkapan = $_POST['kelengkapan'];
			$persyaratan = $_POST['persyaratan'];
			$keterangan = $_POST['keterangan'];
			
			if($user_id && $role_id && $kategori && $kelengkapan && $persyaratan && $keterangan){
				if(count($kelengkapan) < 4 || count($persyaratan) < 8){
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan lengkapi semua persyaratan.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}

				$this->db->update("proposal", array('current_stat' => 1), array('id' => $dx));

				$this->db->update("proposal", array('type_id' => $kategori), array('id' => $dx));

				foreach($kelengkapan as $index => $value) {
					$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $value));
				}

				foreach($persyaratan as $index => $value) {
					$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $value));
				}

				$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 13, 'value' => $_POST['keterangan']));

				if(isset($_POST['lanjut'])) $status = 1; elseif(isset($_POST['tolak'])) $status = 2;

				$Qcheck = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $dx)->where('flow_id', 1)->get();
        		if($Qcheck->num_rows) $this->db->update("proposal_approval", array('user_id' => $user_id, 'action' => $status), array('proposal_id' => $dx, 'flow_id' => 1));
        		else $this->db->insert("proposal_approval", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 1, 'action' => $status));

				$this->db->insert("proposal_approval_history", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 1, 'role_id' => $role_id, 'action' => $status));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'tu_periksa', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));	

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pemeriksaan hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$kategori = $_POST['kategori'];
			$kelengkapan = $_POST['kelengkapan'];
			$persyaratan = $_POST['persyaratan'];
			$keterangan = $_POST['keterangan'];
			
			if($user_id && $role_id && $kategori && $kelengkapan && $persyaratan && $keterangan){
				if(count($kelengkapan) < 4 || count($persyaratan) < 8){
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan lengkapi semua persyaratan.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}

				$this->db->update("proposal", array('type_id' => $kategori), array('id' => $dx));

				$this->db->delete("proposal_checklist", array('proposal_id' => $dx, 'checklist_id >=' => 1, 'checklist_id <=' => 12));

				foreach($kelengkapan as $index => $value) {
					$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $value));
				}

				foreach($persyaratan as $index => $value) {
					$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $value));
				}

				$this->db->update("proposal_checklist", array('value' => $_POST['keterangan']), array('proposal_id' => $dx, 'checklist_id' => 13));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'tu_periksa_edit', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));	

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Edit hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function walikota($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){			

			case 'periksa':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				$this->db->update("proposal", array('current_stat' => 2), array('id' => $dx));

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 14, 'value' => $_POST['keterangan']));

				if(isset($_POST['lanjut'])) $status = 1; elseif(isset($_POST['tolak'])) $status = 2;

				$Qcheck = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $dx)->where('flow_id', 2)->get();
        		if($Qcheck->num_rows) $this->db->update("proposal_approval", array('user_id' => $user_id, 'action' => $status), array('proposal_id' => $dx, 'flow_id' => 2));
        		else $this->db->insert("proposal_approval", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 2, 'action' => $status));

				$this->db->insert("proposal_approval_history", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 2, 'role_id' => $role_id, 'action' => $status));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'walikota_periksa', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pemeriksaan hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->update("proposal_checklist", array('value' => $_POST['keterangan']), array('proposal_id' => $dx, 'checklist_id' => 14));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'walikota_periksa_edit', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Edit hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'setuju':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				$this->db->update("proposal", array('current_stat' => 7), array('id' => $dx));

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 30, 'value' => $_POST['keterangan']));

				if(isset($_POST['lanjut'])) $status = 1; elseif(isset($_POST['tolak'])) $status = 2;

				$Qcheck = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $dx)->where('flow_id', 7)->get();
        		if($Qcheck->num_rows) $this->db->update("proposal_approval", array('user_id' => $user_id, 'action' => $status), array('proposal_id' => $dx, 'flow_id' => 7));
        		else $this->db->insert("proposal_approval", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 7, 'action' => $status));

				$this->db->insert("proposal_approval_history", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 7, 'role_id' => $role_id, 'action' => $status));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'walikota_setuju', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pemeriksaan hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'view':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->update("proposal_checklist", array('value' => $_POST['keterangan']), array('proposal_id' => $dx, 'checklist_id' => 30));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'walikota_setuju_edit', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Edit hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function pertimbangan($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){			

			case 'periksa':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				$this->db->update("proposal", array('current_stat' => 3), array('id' => $dx));

				if(isset($_POST['skpd'])){
					$this->db->update("proposal", array('skpd_id' => $_POST['skpd']), array('id' => $dx));

					$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 31, 'value' => $_POST['skpd']));
				}

				if(isset($_POST['lanjut'])) $status = 1; elseif(isset($_POST['tolak'])) $status = 2;

				$Qcheck = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $dx)->where('flow_id', 3)->get();
        		if($Qcheck->num_rows) $this->db->update("proposal_approval", array('user_id' => $user_id, 'action' => $status), array('proposal_id' => $dx, 'flow_id' => 3));
        		else $this->db->insert("proposal_approval", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 3, 'action' => $status));

				$this->db->insert("proposal_approval_history", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 3, 'role_id' => $role_id, 'action' => $status));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'pertimbangan_periksa', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pemeriksaan hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				if(isset($_POST['skpd'])){
					$this->db->update("proposal", array('skpd_id' => $_POST['skpd']), array('id' => $dx));

					$this->db->update("proposal_checklist", array('value' => $_POST['skpd']), array('proposal_id' => $dx, 'checklist_id' => 31));
				}

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'pertimbangan_periksa_edit', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));	

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Edit hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'verifikasi':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				$this->db->update("proposal", array('current_stat' => 5), array('id' => $dx));

				if(isset($_POST['koreksi']) && $_POST['koreksi'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 26, 'value' => $_POST['koreksi']));

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 27, 'value' => $_POST['keterangan']));

				if(isset($_POST['lanjut'])) $status = 1; elseif(isset($_POST['tolak'])) $status = 2;

				$Qcheck = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $dx)->where('flow_id', 5)->get();
        		if($Qcheck->num_rows) $this->db->update("proposal_approval", array('user_id' => $user_id, 'action' => $status), array('proposal_id' => $dx, 'flow_id' => 5));
        		else $this->db->insert("proposal_approval", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 5, 'action' => $status));

				$this->db->insert("proposal_approval_history", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 5, 'role_id' => $role_id, 'action' => $status));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'pertimbangan_verifikasi', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));
				
				$kor = array(
					"correction" => $this->input->post('koreksi'),
				);
				
				$a = $this->db->where('proposal_id',$dx);
				$a = $this->db->update('proposal_dana',$kor);
				
				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pemeriksaan hibah bansos berhasil.';

				header('location:'.$_SERVER['HTTP_REFERER']);

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'view':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				if(isset($_POST['koreksi']) && $_POST['koreksi'] != '') $this->db->update("proposal_checklist", array('value' => $_POST['koreksi']), array('proposal_id' => $dx, 'checklist_id' => 26));

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->update("proposal_checklist", array('value' => $_POST['keterangan']), array('proposal_id' => $dx, 'checklist_id' => 27));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'pertimbangan_verifikasi_edit', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Edit hibah bansos berhasil.';

				header('location:'.$_SERVER['HTTP_REFERER']);

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function skpd($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){			

			case 'periksa':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$syarat = $_POST['syarat'];
			
			if($user_id && $role_id){
				if(count($syarat) < 7){
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan lengkapi semua persyaratan.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}

				$this->db->update("proposal", array('current_stat' => 4), array('id' => $dx));

				if(isset($_POST['beri'])) $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $_POST['beri']));

				if(isset($_POST['besar']) && $_POST['besar'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 17, 'value' => $_POST['besar']));

				foreach($syarat as $index => $value) {
					$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $value));
				}

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 25, 'value' => $_POST['keterangan']));

				if(isset($_POST['lanjut'])) $status = 1; elseif(isset($_POST['tolak'])) $status = 2;

				$Qcheck = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $dx)->where('flow_id', 4)->get();
        		if($Qcheck->num_rows) $this->db->update("proposal_approval", array('user_id' => $user_id, 'action' => $status), array('proposal_id' => $dx, 'flow_id' => 4));
        		else $this->db->insert("proposal_approval", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 4, 'action' => $status));

				$this->db->insert("proposal_approval_history", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 4, 'role_id' => $role_id, 'action' => $status));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'skpd_periksa', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pemeriksaan hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			$syarat = $_POST['syarat'];
			
			if($user_id && $role_id){
				if(count($syarat) < 7){
					$_SESSION['notify']['type'] = 'failed';
					$_SESSION['notify']['message'] = 'Silahkan lengkapi semua persyaratan.';

					header('location:'.$_SERVER['HTTP_REFERER']); die();
				}

				$this->db->delete("proposal_checklist", array('proposal_id' => $dx, 'checklist_id >=' => 15, 'checklist_id <=' => 25));

				if(isset($_POST['beri'])) $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $_POST['beri']));

				if(isset($_POST['besar']) && $_POST['besar'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 17, 'value' => $_POST['besar']));

				foreach($syarat as $index => $value) {
					$this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => $value));
				}

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 25, 'value' => $_POST['keterangan']));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'skpd_periksa_edit', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Edit hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;
		}
	}

	public function tapd($tp, $dx=0)
	{
		if(empty($_SESSION['sabilulungan'])) die('<p align="center">Sesi Anda telah habis!<br />Silakan lakukan <a href="'.site_url('logout').'">otorisasi</a> ulang.</p>');
		switch($tp){				

			case 'verifikasi':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				$this->db->update("proposal", array('current_stat' => 6), array('id' => $dx));

				if(isset($_POST['rekomendasi']) && $_POST['rekomendasi'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 28, 'value' => $_POST['rekomendasi']));

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->insert("proposal_checklist", array('proposal_id' => $dx, 'checklist_id' => 29, 'value' => $_POST['keterangan']));

				if(isset($_POST['lanjut'])) $status = 1; elseif(isset($_POST['tolak'])) $status = 2;

				$Qcheck = $this->db->select("user_id")->from('proposal_approval')->where('proposal_id', $dx)->where('flow_id', 6)->get();
        		if($Qcheck->num_rows) $this->db->update("proposal_approval", array('user_id' => $user_id, 'action' => $status), array('proposal_id' => $dx, 'flow_id' => 6));
        		else $this->db->insert("proposal_approval", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 6, 'action' => $status));

				$this->db->insert("proposal_approval_history", array('proposal_id' => $dx, 'user_id' => $user_id, 'flow_id' => 6, 'role_id' => $role_id, 'action' => $status));	

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'tapd_verifikasi', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Pemeriksaan hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			break;

			case 'edit':
			$user_id = $_POST['user_id'];
			$role_id = $_POST['role_id'];
			
			if($user_id && $role_id){
				if(isset($_POST['rekomendasi']) && $_POST['rekomendasi'] != '') $this->db->update("proposal_checklist", array('value' => $_POST['rekomendasi']), array('proposal_id' => $dx, 'checklist_id' => 28));

				if(isset($_POST['keterangan']) && $_POST['keterangan'] != '') $this->db->update("proposal_checklist", array('value' => $_POST['keterangan']), array('proposal_id' => $dx, 'checklist_id' => 29));

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'tapd_verifikasi_edit', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));

				$_SESSION['notify']['type'] = 'success';
				$_SESSION['notify']['message'] = 'Edit hibah bansos berhasil.';

				header('location:'.site_url('report'));

			}else{
				$_SESSION['notify']['type'] = 'failed';
				$_SESSION['notify']['message'] = 'Silahkan lengkapi formulir berikut.';

				header('location:'.$_SERVER['HTTP_REFERER']);
			}
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
				if($d==1) $file = rawurldecode(site_url('process/report_hibah/'.$dx));
				elseif($d==2) $file = rawurldecode(site_url('process/report_bansos/'.$dx));
				
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

				$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'report', 'id' => $dx, 'ip' => $_SERVER['REMOTE_ADDR']));
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
			if($d==1) header('location:'.site_url('process/report_hibah/'.$dx));
			elseif($d==2) header('location:'.site_url('process/report_bansos/'.$dx));			
		}
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
			<table align="right" border="1" cellpadding="0" cellspacing="0" width="100"><tr><td align="center">HIBAH</td></tr></table><br>
			<p align="center"><span style="font-size:20px">REKOMENDASI</span><br>
			PEMBERIAN BELANJA HIBAH DAN BELANJA BANTUAN SOSIAL YANG<br>
			BERSUMBER DARI ANGGARAN PENDAPATAN DAN BELANJA DAERAH</p>
			<hr>
			<!-- <hr style="border: 2px solid #000;margin-top: -8px;"> -->

			<p>Yang bertanda tangan di bawah ini, kami telah melakukan evaluasi Proposal yang disusulkan oleh Permohonan Belanja Hibah dan memberikan Rekomendasi sebagai berikut:</p>

			<?php
			$Qisi = $this->db->query("SELECT a.id, a.judul, a.name, b.name AS ketua, b.address, a.maksud_tujuan, SUM(c.amount) AS usulan  FROM proposal a JOIN user b ON b.id=a.user_id JOIN proposal_dana c ON c.proposal_id=a.id WHERE a.id='$tp'");
			$isi = $Qisi->result_object(); $id = $isi[0]->id;

			$Qbesar = $this->db->query("SELECT value FROM proposal_checklist WHERE `proposal_id`='$id' AND checklist_id IN (26,27)"); $besar = $Qbesar->result_object();

			//$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'report_hibah', 'id' => $tp, 'ip' => $_SERVER['REMOTE_ADDR']));
			?>

			<table width="100%" >
				<tr><td width="3%">1.</td><td width="37%">NAMA KEGIATAN</td><td>:</td><td width="60%"><?php echo $isi[0]->judul ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>2.</td><td>NAMA ORGANISASI / KEPANITIAAN</td><td>:</td><td><?php echo $isi[0]->name ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>3.</td><td>NAMA KETUA/PIMPINAN ORGANISASI / KEPANITIAAN</td><td>:</td><td><?php echo $isi[0]->ketua ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>4.</td><td>ALAMAT ORGANISASI / KEPANITIAAN</td><td>:</td><td><?php echo $isi[0]->address ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>5.</td><td>RENCANA PELAKSANAAN KEGIATAN</td><td>:</td><td><?php echo $isi[0]->maksud_tujuan ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>6.</td><td>BESARNYA USULAN</td><td>:</td><td>Rp. <?php echo number_format($isi[0]->usulan,0,",","."); echo ',-'; ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>7.</td><td>BESARNYA REKOMENDASI</td><td>:</td><td><?php if(isset($besar[0]->value)) echo 'Rp. '.number_format($besar[0]->value,0,",",".").',-'; else echo '-'; ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>8.</td><td>CATATAN</td><td>:</td><td><?php if(isset($besar[1]->value)) echo $besar[1]->value; else echo '-'; ?></td></tr>
				<tr><td colspan="3"></td></tr>
			</table>

			<?php $bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember') ?>
			<p align="right">Bandung, <?php echo date('j').' '.$bulan[date('n')].' '.date('Y'); ?></p>
			<br>

			<table width="100%" border="1" cellpadding="0" cellspacing="0">
				<tr><td align="center" width="40%">JABATAN</td><td align="center" width="40%">NAMA/NIP</td><td align="center" width="20%">TANDA TANGAN</td></tr>
				<tr><td>Kepala SKPD................................</td><td></td><td></td></tr>
				<tr><td>Camat................................</td><td></td><td></td></tr>
				<tr><td>Lurah................................</td><td></td><td></td></tr>
			</table>

			<br>
			<p>Telah dilakukan pembahasan<br>Pada tanggal..........................</p>
			<p>Ketua Tim Pertimbangan Pemberian<br>Belanja Hibah dan Belanja Bantuan Sosial</p>
			<br>
			<p>....................................................................</p>
		</body>
		</html>
        <?php
	}

	public function report_bansos($tp)
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
			<table align="right" border="1" cellpadding="0" cellspacing="0" width="100"><tr><td align="center">BANTUAN SOSIAL</td></tr></table><br><br>
			<p align="center"><span style="font-size:20px">REKOMENDASI</span><br>
			PEMBERIAN BELANJA HIBAH DAN BELANJA BANTUAN SOSIAL YANG<br>
			BERSUMBER DARI ANGGARAN PENDAPATAN DAN BELANJA DAERAH</p>
			<hr>
			<!-- <hr style="border: 2px solid #000;margin-top: -8px;"> -->

			<p>Yang bertanda tangan di bawah ini, kami telah melakukan evaluasi Proposal yang disusulkan oleh Permohonan Belanja Bantuan Sosial dan memberikan Rekomendasi sebagai berikut:</p>

			<?php
			$Qisi = $this->db->query("SELECT a.id, a.judul, a.name, b.name AS ketua, b.address, a.maksud_tujuan, SUM(c.amount) AS usulan  FROM proposal a JOIN user b ON b.id=a.user_id JOIN proposal_dana c ON c.proposal_id=a.id WHERE a.id='$tp'");
			$isi = $Qisi->result_object(); $id = $isi[0]->id;

			$Qbesar = $this->db->query("SELECT value FROM proposal_checklist WHERE `proposal_id`='$id' AND checklist_id IN (26,27)"); $besar = $Qbesar->result_object();

			//$this->db->insert("log", array('user_id' => $_SESSION['sabilulungan']['uid'], 'activity' => 'report_bansos', 'id' => $tp, 'ip' => $_SERVER['REMOTE_ADDR']));
			?>

			<table width="100%" >
				<tr><td width="3%">1.</td><td width="37%">NAMA KEGIATAN</td><td>:</td><td width="60%"><?php echo $isi[0]->judul ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>2.</td><td>NAMA ORGANISASI / KEPANITIAAN</td><td>:</td><td><?php echo $isi[0]->name ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>3.</td><td>NAMA KETUA/PIMPINAN ORGANISASI / KEPANITIAAN</td><td>:</td><td><?php echo $isi[0]->ketua ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>4.</td><td>ALAMAT ORGANISASI / KEPANITIAAN</td><td>:</td><td><?php echo $isi[0]->address ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>5.</td><td>RENCANA PELAKSANAAN KEGIATAN</td><td>:</td><td><?php echo $isi[0]->maksud_tujuan ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>6.</td><td>BESARNYA USULAN</td><td>:</td><td>Rp. <?php echo number_format($isi[0]->usulan,0,",","."); echo ',-'; ?></td></tr>
				<tr><td>7.</td><td>BESARNYA REKOMENDASI</td><td>:</td><td><?php if(isset($besar[0]->value)) echo 'Rp. '.number_format($besar[0]->value,0,",",".").',-'; else echo '-'; ?></td></tr>
				<tr><td colspan="3"></td></tr>
				<tr><td>8.</td><td>CATATAN</td><td>:</td><td><?php if(isset($besar[1]->value)) echo $besar[1]->value; else echo '-'; ?></td></tr>
				<tr><td colspan="3"></td></tr>
			</table>

			<?php $bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember') ?>
			<p align="right">Bandung, <?php echo date('j').' '.$bulan[date('n')].' '.date('Y'); ?></p>
			<br>

			<table width="100%" border="1" cellpadding="0" cellspacing="0">
				<tr><td align="center" width="40%">JABATAN</td><td align="center" width="40%">NAMA/NIP</td><td align="center" width="20%">TANDA TANGAN</td></tr>
				<tr><td>Kepala SKPD................................</td><td></td><td></td></tr>
				<tr><td>Camat................................</td><td></td><td></td></tr>
				<tr><td>Lurah................................</td><td></td><td></td></tr>
			</table>

			<br>
			<p>Telah dilakukan pembahasan<br>Pada tanggal..........................</p>
			<p>Ketua Tim Pertimbangan Pemberian<br>Belanja Hibah dan Belanja Bantuan Sosial</p>
			<br>
			<p>....................................................................</p>

			<!-- <table width="100%" border="1" cellpadding="0" cellspacing="0">
				<tr><td align="center" width="40%">JABATAN</td><td align="center" width="40%">NAMA/NIP</td><td align="center" width="20%">TANDA TANGAN</td></tr>
				<tr><td>Kepala SKPD................................</td><td></td><td></td></tr>
				<tr><td>Camat................................</td><td></td><td></td></tr>
				<tr><td>Lurah................................</td><td></td><td></td></tr>
			</table>

			<br>
			<p>Telah dilakukan pembahasan<br>Pada tanggal..........................</p>
			<p>Ketua Tim Pertimbangan Pemberian<br>Belanja Hibah dan Belanja Bantuan Sosial<br>
			....................................................................</p>

			<div style="margin-left: 65%"><p align="center">WALIKOTA BANDUNG,<br><br>TTD.<br><br>MOCHAMAD RIDWAN KAMIL</p></div>

			<p>Salinan sesuai dengan aslinya<br>
			KEPALA BAGIAN HUKUM DAN HAM</p>
			<br><br><br>

			<div style="width: 260px"><center>H. ADIN MUKHTARUDIN, SH, MH</center>
			<center>Pembina Tingkat I</center>
			<center>NIP. 19610625 198603 1 008</center></div> -->
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
			PERSETUJUAN BUPATI TAHUN<br>
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
                            if($kategori=='all' && $skpd=='all') $where .= "WHERE YEAR(time_entry) BETWEEN '$dari' AND '$sampai'";
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

			<p align="right">BUPATI BANDUNG BARAT,<br><br><br><br>Drs. H. Abubakar, M.Si</p>
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