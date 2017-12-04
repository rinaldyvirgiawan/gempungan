<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

session_start();

class Main extends CI_Controller {

	function __construct()
	{
		parent::__construct();
        $this->load->database();
		$this->load->helper(array('html', 'url'));
		$this->load->model('ifunction');
	}
	
	public function index()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/home');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function login()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/login');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function tentang()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/tentang');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function proposal($t=0, $tp=0, $d=0, $dx=0, $id=0, $p=0)
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/proposal', array('p' => $p,'t' => $t,'tp' => $tp,'d' => $d,'dx' => $dx,'id' => $id));
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function detail($dx=0)
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/detail', array('dx' => $dx));
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function view($dx=0)
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/view', array('dx' => $dx));
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function galeri($dx=0)
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/galeri', array('dx' => $dx));
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function laporan($dx=0)
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/laporan', array('dx' => $dx));
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function peraturan()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/peraturan');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function lapor()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/lapor');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function listlaporan($p=0, $dx=0)
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/listlaporan', array('p' => $p,'dx' => $dx));
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function pengumuman($p=0, $dx=0)
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/pengumuman', array('p' => $p,'dx' => $dx));
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}

	public function bcc()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header_bcc');
			$this->load->view('content/bcc');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}

	public function statistik($p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header_bcc');
		$this->load->view('content/statistik', array('p' => $p,'dx' => $dx));
		$this->load->view('template/footer');		
	}
	
	public function daftar()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/daftar');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function sandi()
	{
		// if(isset($_SESSION['sabilulungan'])){
			$this->load->view('template/meta');
			$this->load->view('template/header');
			$this->load->view('content/sandi');
			$this->load->view('template/footer');
		// }
		// else $this->load->view('template/login');		
	}
	
	public function hibah($tp='', $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==3 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==5 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/hibah', array('tp' => $tp,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function report($p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan'])) $this->load->view('content/report', array('p' => $p,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function tatausaha($tp='', $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==5 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/tatausaha', array('tp' => $tp,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function walikota($tp='', $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==1 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/walikota', array('tp' => $tp,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function pertimbangan($tp='', $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==4 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/pertimbangan', array('tp' => $tp,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function skpd($tp='', $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==3 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/skpd', array('tp' => $tp,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function tapd($tp='', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==2 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/tapd', array(
				'p' => $p,
				'tp' => $tp,
				'dx' => $dx
			));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function admin($tp='', $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/admin', array('tp' => $tp,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function cms($tp='koordinator', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==9) $this->load->view('content/cms', array('tp' => $tp,'p' => $p,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function realisasi($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==9) $this->load->view('content/realisasi', array('tp' => $tp,'p' => $p,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function detil($tp='', $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan'])) $this->load->view('content/detil', array('tp' => $tp,'dx' => $dx));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function input()
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==8 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/input');
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function lpj($tp='list', $dx=0, $p=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['sabilulungan']) && $_SESSION['sabilulungan']['role']==6 || $_SESSION['sabilulungan']['role']==7 || $_SESSION['sabilulungan']['role']==9) $this->load->view('content/lpj', array(
				'p' => $p,
				'tp' => $tp,
				'dx' => $dx
			));
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function form($tp, $dx=0)
	{
		if(isset($_SESSION['sabilulungan'])){
			$this->load->view('content/form', array(
				'tp' => $tp,
				'dx' => $dx
			));
		}
		else echo '<script type="text/javascript">window.location.reload()</script>';
	}
	
	public function logout($dx=0)
	{
		unset($_SESSION['sabilulungan']);

		$this->db->insert("log", array('user_id' => $dx, 'activity' => 'logout', 'ip' => $_SERVER['REMOTE_ADDR']));

		header('location:'.base_url());
	}
	
	public function new_report(){
		$kategori = $this->input->post('kategori');
		$dari = $this->input->post('dari');
		$sampai = $this->input->post('sampai');
		$skpd = $this->input->post('skpd');
		
		
	}
}