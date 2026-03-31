<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sobre extends CI_Controller {
	
	public function index()
	{
		$this->template->title('Sobre a Betina');
		$this->template->build('frontend/sobre_page');
	}
}

/* End of file sobre.php */
/* Location: ./application/controllers/sobre.php */
