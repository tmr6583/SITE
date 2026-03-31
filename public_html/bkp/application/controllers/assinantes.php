<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assinantes extends CI_Controller {
	
	public function cadastrar()
	{
		$assinante = new models\Assinante();
		$assinante->setEmail( $this->input->post('email') );
		
		$this->doctrine->em->persist($assinante);
		$this->doctrine->em->flush();
		redirect('', 'refresh');
	}
}

/* End of file assinantes.php */
/* Location: ./application/controllers/assinantes.php */
