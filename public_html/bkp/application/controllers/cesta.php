<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cesta extends CI_Controller {
	
	public function index()
	{
		if(count($_POST) > 0) {
			$codigo = intval($this->input->post('codigo'));

			$cesta = $this->session->userdata('cesta');
			if(is_array($cesta) && count($cesta) > 0) {
				foreach($cesta as $key => $item) {
					if($item['codigo'] === $codigo) {
						$cesta[$key]['quantidade'] = /*$item['quantidade'] + */$this->input->post('quantidade');
					}
				}
				
				$this->session->set_userdata('cesta', $cesta);
			}
		}
		
		$cesta = $this->session->userdata('cesta');
		
		$this->template->title('Cesta de cotações');
		$this->template->build('frontend/cesta_page', array('cesta' => $cesta));
	}
	
	public function adicionar()
	{
		$produto = $this->doctrine->em->createQuery('SELECT p.codigo, p.titulo, p.preco_promocional FROM models\Produto p WHERE p.codigo=:codigo')
						->setParameter('codigo', $this->input->post('codigo'))->getSingleResult();
		
		$quantidade = 1;
		
		$cesta = $this->session->userdata('cesta');
		if(is_array($cesta) && count($cesta) > 0) {
			
			$existe = false;
			
			foreach($cesta as $key => $item) {
				$quantidade += $item['quantidade'];
				if($item['codigo'] === $produto['codigo']) {
					$cesta[$key]['quantidade'] = $item['quantidade'] + 1;
					$existe = true;
				}
			}
			
			if(!$existe)
				$cesta[] = array('codigo' => $produto['codigo'], 'titulo' => $produto['titulo'], 'quantidade' => 1, 'preco_promocional' => $produto['preco_promocional']);
		
		} else {
			$cesta = array(
				array('codigo' => $produto['codigo'], 'titulo' => $produto['titulo'], 'quantidade' => 1, 'preco_promocional' => $produto['preco_promocional'])
			);
		}
			
		$this->session->set_userdata('cesta', $cesta);
		
		echo $quantidade;
	}
	
	public function remover($codigo)
	{
		$cesta = $this->session->userdata('cesta');
		if(is_array($cesta) && count($cesta) > 0)
			foreach($cesta as $key => $item)
				if($item['codigo'] == $codigo)
					if($item['quantidade'] > 1)
						$cesta[$key]['quantidade'] = $item['quantidade'] - 1;
					else
						unset($cesta[$key]);
		
		$this->session->set_userdata('cesta', $cesta);
		
		redirect('cesta', 'refresh');
	}
	
	public function fechar()
	{
		$usuario = $this->session->userdata('usuario');
		if($usuario == NULL)
			redirect('cadastro', 'refresh');
		
		$nome = $usuario['nome'];
		$email = $usuario['login'];
		$assunto = 'Cotação de produtos';
		
		$mensagem  = "O sr(a). {$nome} entrou em contato atraves do site betina.com.br.\n";
		$mensagem .= "Email: {$email}\n\n";
		$mensagem .= "{$assunto}\n";
		
		$total_produtos = 0;
		$cesta = $this->session->userdata('cesta');
		if(is_array($cesta) && count($cesta) > 0) {
			foreach($cesta as $item) {
				$mensagem .= 'Produto: '. $item['codigo'] ."\t". $item['titulo'] .";\tQuantidade: ". $item['quantidade'];
				if($item['preco_promocional'] > 0)
					$mensagem .= ";\tPreço promocional: R$". format_to_real($item['preco_promocional']);
				$mensagem .= "\n";
			}
			
			$this->session->unset_userdata('cesta');
			
			$this->load->library('email');
			$this->email->from('contato@betinalimpeza.com.br', $assunto);
			$this->email->to('contato@betinalimpeza.com.br');
			$this->email->subject($assunto);
			$this->email->message($mensagem);
			$this->email->send();
			
			echo '<b>Cota&ccedil;&atilde;o enviada com sucesso!</b><br>Aguarde e em breve enviaremos uma resposta.';
			redirect('', 'refresh', NULL, 8);
		}
	}
}

/* End of file cesta.php */
/* Location: ./application/controllers/cesta.php */
