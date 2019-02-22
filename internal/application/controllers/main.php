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

	public function index($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/home', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function child($tp='index', $dx=0, $p=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/child', array('tp' => $tp,'dx' => $dx,'p' => $p));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function surat($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/surat', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function hibah($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/hibah', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function bansos($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/bansos', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function subsidi($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/subsidi', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function bpjs($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/bpjs', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function bantuan($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/bantuan', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function pejabat($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/pejabat', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}

	public function pengguna($tp='index', $p=0, $dx=0)
	{
		$this->load->view('template/meta');
		$this->load->view('template/header');
		if(isset($_SESSION['internal'])){
			$this->load->view('template/menu');
			$this->load->view('content/pengguna', array('tp' => $tp,'p' => $p,'dx' => $dx));
		}
		else $this->load->view('content/login');
		$this->load->view('template/footer');		
	}
	
	public function form($tp, $dx=0)
	{
		if(isset($_SESSION['internal'])){
			$this->load->view('content/form', array(
				'tp' => $tp,
				'dx' => $dx
			));
		}
		else echo '<script type="text/javascript">window.location.reload()</script>';
	}
	
	public function logout($dx=0)
	{
		unset($_SESSION['internal']);
		header('location:'.base_url());
	}
}