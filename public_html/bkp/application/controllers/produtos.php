<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Produtos extends CI_Controller {
	
	public function index()
	{
		
	}
	 
	public function por_assunto($assunto_id, $inicio = 0)
	{
		$assunto = $this->doctrine->em->createQuery('SELECT a, p FROM models\Assunto a 
													 JOIN a.produtos p 
													 WHERE a.id=:assunto AND a.ativo=true AND p.ativo=true')
						->setParameter('assunto', $assunto_id)->getSingleResult();
		
		$produtos = $assunto->getProdutos()->toArray();
		
		$this->load->library('pagination');
		
		$parts = explode('/', uri_string());
		$url = $parts[0] .'/'. $parts[1] .'/'. $parts[2];
		
		$config['base_url'] = $url;
		$config['total_rows'] = count($produtos);
		$config['per_page'] = 16;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<div id="pagination">';
		$config['full_tag_close'] = '</div>';
		
		$this->pagination->initialize($config);
		
		$prods = array();
		
		for($i = $inicio; $i < 16*(($inicio/16)+1) && $i < count($produtos); $i++)
			$prods[] = $produtos[$i];
		
		$paginacao = $this->pagination->create_links();
		
	    $this->template->title($assunto->getTitulo());
		$this->template->build('frontend/produtos_page', array(
																'titulo' => $assunto->getTitulo(), 
																'produtos' => $prods,
																'paginacao' => $paginacao
															));
	}
	
	public function ver()
	{
		$produto = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p WHERE p.codigo=:codigo')
						->setParameter('codigo', $this->input->post('codigo'))->getSingleResult();
		
		$img_version = '';
		if(!is_null($produto->getDataAlteracao())) {
			$data = $produto->getDataAlteracao();
			$img_version = '?v='. $data->getTimestamp();
		}
		
		echo '<h4>'. $produto->getCodigo() .' - '. $produto->getTitulo() .'</h4><div><img src="lib/uploads/'. $produto->getCodigo() .'.jpg'. $img_version .'" alt="'. $produto->getTitulo() .'" title="'. $produto->getTitulo() .'" /></div>
			 <p>'. $produto->getDescricao() .'</p>
			 <p>'. $produto->getUnidadeVenda() .'<br />
			 '. $produto->getComposicao() .'</p>';
		
		if($produto->isPromocao())
			echo '<p>De R$ '. format_to_real($produto->getPreco()) .' por <b>R$ '. format_to_real($produto->getPrecoPromocional()) .'</b></p>';
	}
}

/* End of file produtos.php */
/* Location: ./application/controllers/produtos.php */
