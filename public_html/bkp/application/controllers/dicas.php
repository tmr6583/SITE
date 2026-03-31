<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dicas extends CI_Controller {
	
	public function index($inicio = 0)
	{
		$quantidade = $this->doctrine->em->createQuery('SELECT COUNT(d) FROM models\Dica d')
						   ->getSingleScalarResult();
		
		$dicas = $this->doctrine->em->createQuery('SELECT d FROM models\Dica d ORDER BY d.id DESC')
					  ->setFirstResult($inicio)->setMaxResults(9)->getResult();
		
		$this->load->library('pagination');
		
		$config['base_url'] = 'dicas/index';
		$config['total_rows'] = $quantidade;
		$config['per_page'] = 9;
		$config['uri_segment'] = 3;
		$config['full_tag_open'] = '<div id="pagination">';
		$config['full_tag_close'] = '</div>';
		
		$this->pagination->initialize($config);
		
		$this->template->title('Dicas e novidades');
		$this->template->build('frontend/dicas_page', array('dicas' => $dicas));
	}
	
	public function ler()
	{
		$dica = $this->doctrine->em->createQuery('SELECT d FROM models\Dica d WHERE d.id=:codigo')
						 ->setParameter('codigo', $this->input->post('codigo'))->getSingleResult();
		
		echo '<h4>'. $dica->getTitulo() .'</h4><p>'. nl2br( $dica->getDica() ) .'</p>';
	}
}

/* End of file dicas.php */
/* Location: ./application/controllers/dicas.php */
