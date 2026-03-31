<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Localizacao extends CI_Controller {
	
	public function index()
	{
		$this->template->title('Localização');
		$this->template->build('frontend/localizacao_page');
	}
}

/* End of file localizacao.php */
/* Location: ./application/localizacao/sobre.php */
