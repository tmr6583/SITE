<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	
	public function index()
	{
		$produtos_promocionais = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p 
										  						   WHERE p.promocao=true AND p.ativo=true ORDER BY p.data_criacao DESC')
									  ->setMaxResults(4)->getResult();
		
		$produtos_destaque = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p 
										  					   WHERE p.destaque=true AND p.ativo=true ORDER BY p.data_criacao DESC')
								  ->setMaxResults(8)->getResult();
		
		$this->template->title('Home');
		$this->template->build('frontend/home_page', array(
															'promocionais' => $produtos_promocionais, 
															'destaques' => $produtos_destaque
														));
	}
	
	public function buscar()
	{
		$busca = strtolower($_GET['busca']);
		
		$produtos = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p 
										  					   WHERE p.ativo=true AND ( LOWER(p.titulo) LIKE :busca OR LOWER(p.descricao) LIKE :busca )')
								  ->setParameter('busca', '%'. $busca .'%')->getResult();
		
		$paginacao = '';
		
		$this->template->title('Resultados da pesquisa - '. $busca);
		$this->template->build('frontend/produtos_page', array(
																'titulo' => 'Resultados da pesquisa - '. $busca, 
																'produtos' => $produtos,
																'paginacao' => $paginacao
															));
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
