<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	public function index($entity = NULL)
	{
		if(!$this->verificar_administrador())
			redirect('cadastro/sair', 'refresh');
		
		$this->template->set_layout('admin');
		
		$parans = func_get_args();
		unset($parans[0]);
		
		if($entity != NULL)
			$this->$entity( array_values($parans) );
		else
			$this->produtos( array_values($parans) );
	}
	
	private function verificar_administrador()
	{
		$usuario = $this->session->userdata('usuario');
		return ( $usuario != NULL && isset($usuario) && $usuario['nivel_acesso'] === 'ADMINISTRADOR' );
	}
		
	private function assinantes($parans = NULL)
	{
		if(count($parans) == 0 || (isset($parans[0]) && $parans[0] === 'pagina')) {
			$inicio = 0;
			if(isset($parans[0]) && $parans[0] === 'pagina' && isset($parans[1]))
				$inicio = $parans[1];
			
			$quantidade = $this->doctrine->em->createQuery('SELECT COUNT(a) FROM models\Assinante a')
							   ->getSingleScalarResult();
			
			$assinantes = $this->doctrine->em->createQuery('SELECT a FROM models\Assinante a ORDER BY a.data_criacao')
							   ->setFirstResult($inicio)->setMaxResults(25)->getResult();
			
			$this->load->library('pagination');
			
			$config['base_url'] = 'assinantes/pagina';
			$config['total_rows'] = $quantidade;
			$config['per_page'] = 25;
			$config['uri_segment'] = 4;
			$config['full_tag_open'] = '<div id="pagination">';
			$config['full_tag_close'] = '</div>';
			
			$this->pagination->initialize($config);
			
			$this->template->title('Área administrativa', 'Assinantes');
			$this->template->build('backend/assinantes/list_page', array('assinantes' => $assinantes));
		} else {
			$funcao = 'assinantes_'. $parans[0];
			unset($parans[0]);
			$this->$funcao( array_values($parans) );
		}
	}
	
	private function assinantes_cadastrar()
	{
		if(count($_POST) > 0) {
			$assinante = $this->preencherAssinante();
			$this->doctrine->em->persist($assinante);
			$this->doctrine->em->flush();
			redirect('admin/assinantes', 'refresh');
			
		} else {
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						email:{
							required: true,
							email: true
						}
					},
					messages:{
						email: {
							required: "Informe seu email.",
							email: "Email inválido."
						}
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Novo assinante');
			$this->template->build('backend/assinantes/add_page');
		}
	}
	
	private function assinantes_alterar($parans)
	{
		if(count($_POST) > 0) {
			$assinante = $this->doctrine->em->createQuery('SELECT a FROM models\Assinante a WHERE a.email=:email')
							  ->setParameter('email', $this->input->post('email'))->getSingleResult();
			
			$assinante = $this->preencherAssinante($assinante);
			$this->doctrine->em->flush();
			redirect('admin/assinantes', 'refresh');
			
		} else {
			$assinante = $this->doctrine->em->createQuery('SELECT a FROM models\Assinante a WHERE a.email=:email')
							  ->setParameter('email', urldecode($parans[0]))->getSingleResult();
			
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						email:{
							required: true,
							email: true
						}
					},
					messages:{
						email: {
							required: "Informe seu email.",
							email: "Email inválido."
						}
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Alterar assinante');
			$this->template->build('backend/assinantes/edit_page', array('assinante' => $assinante));
		}
	}
	
	private function assinantes_excluir($parans)
	{
		$assinante = $this->doctrine->em->createQuery('SELECT a FROM models\Assinante a WHERE a.email=:email')
						  ->setParameter('email', urldecode($parans[0]))->getSingleResult();
		
		$this->doctrine->em->remove($assinante);
		$this->doctrine->em->flush();
		
		redirect('admin/assinantes', 'refresh');
	}
	
	private function preencherAssinante(models\Assinante $assinante = NULL)
	{
		if($assinante == NULL)
			$assinante = new models\Assinante();
		
		$assinante->setEmail( $this->input->post('email') );
		
		return $assinante;
	}
	
	private function assuntos($parans = NULL)
	{
		if(count($parans) == 0 || (isset($parans[0]) && $parans[0] === 'pagina')) {
			$inicio = 0;
			if(isset($parans[0]) && $parans[0] === 'pagina' && isset($parans[1]))
				$inicio = $parans[1];
			
			$quantidade = $this->doctrine->em->createQuery('SELECT COUNT(a) FROM models\Assunto a')
							   ->getSingleScalarResult();
			
			$assuntos = $this->doctrine->em->createQuery('SELECT a FROM models\Assunto a ORDER BY a.titulo')
						  	 ->setFirstResult($inicio)->setMaxResults(25)->getResult();
			
			$this->load->library('pagination');
			
			$config['base_url'] = 'assuntos/pagina';
			$config['total_rows'] = $quantidade;
			$config['per_page'] = 25;
			$config['uri_segment'] = 4;
			$config['full_tag_open'] = '<div id="pagination">';
			$config['full_tag_close'] = '</div>';
			
			$this->pagination->initialize($config);
			
			$this->template->title('Área administrativa', 'Assuntos');
			$this->template->build('backend/assuntos/list_page', array('assuntos' => $assuntos));
		} else {
			$funcao = 'assuntos_'. $parans[0];
			unset($parans[0]);
			$this->$funcao( array_values($parans) );
		}
	}
	
	private function assuntos_alterar_status($parans)
	{
		$assunto = $this->doctrine->em->createQuery('SELECT a FROM models\Assunto a WHERE a.id=:id')
						->setParameter('id', $parans[0])->getSingleResult();
		
		$assunto->setAtivo( !$assunto->isAtivo() );
		$this->doctrine->em->flush();
		redirect('admin/assuntos', 'refresh');
	}
	
	private function assuntos_cadastrar()
	{
		if(count($_POST) > 0) {
			$assunto = $this->preencherAssunto();
			$this->doctrine->em->persist($assunto);
			$this->doctrine->em->flush();
			redirect('admin/assuntos', 'refresh');
			
		} else {
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						titulo: "required"
					},
					messages:{
						titulo: "Informe o título."
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Novo assunto');
			$this->template->build('backend/assuntos/add_page');
		}
	}
	
	private function assuntos_alterar($parans)
	{
		if(count($_POST) > 0) {
			$assunto = $this->doctrine->em->createQuery('SELECT a FROM models\Assunto a WHERE a.id=:id')
							->setParameter('id', $this->input->post('id'))->getSingleResult();
			
			$assunto = $this->preencherAssunto($assunto);
			$this->doctrine->em->flush();
			redirect('admin/assuntos', 'refresh');
			
		} else {
			$assunto = $this->doctrine->em->createQuery('SELECT a FROM models\Assunto a WHERE a.id=:id')
							->setParameter('id', $parans[0])->getSingleResult();
			
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						titulo: "required"
					},
					messages:{
						titulo: "Informe o título."
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Alterar assunto');
			$this->template->build('backend/assuntos/edit_page', array('assunto' => $assunto));
		}
	}
	
	private function assuntos_excluir($parans)
	{
		$assunto = $this->doctrine->em->createQuery('SELECT a FROM models\Assunto a WHERE a.id=:id')
						->setParameter('id', $parans[0])->getSingleResult();
		
		$this->doctrine->em->remove($assunto);
		$this->doctrine->em->flush();
		
		redirect('admin/assuntos', 'refresh');
	}
	
	private function preencherAssunto(models\Assunto $assunto = NULL)
	{
		if($assunto == NULL)
			$assunto = new models\Assunto();
		
		$assunto->setTitulo( $this->input->post('titulo') );
		$assunto->setAtivo(TRUE);
		
		return $assunto;
	}
	
	private function dicas($parans = NULL)
	{
		if(count($parans) == 0 || (isset($parans[0]) && $parans[0] === 'pagina')) {
			$inicio = 0;
			if(isset($parans[0]) && $parans[0] === 'pagina' && isset($parans[1]))
				$inicio = $parans[1];
			
			$quantidade = $this->doctrine->em->createQuery('SELECT COUNT(d) FROM models\Dica d')
							   ->getSingleScalarResult();
			
			$dicas = $this->doctrine->em->createQuery('SELECT d FROM models\Dica d ORDER BY d.titulo')
						  ->setFirstResult($inicio)->setMaxResults(25)->getResult();
			
			$this->load->library('pagination');
			
			$config['base_url'] = 'dicas/pagina';
			$config['total_rows'] = $quantidade;
			$config['per_page'] = 25;
			$config['uri_segment'] = 4;
			$config['full_tag_open'] = '<div id="pagination">';
			$config['full_tag_close'] = '</div>';
			
			$this->pagination->initialize($config);
			
			$this->template->title('Área administrativa', 'Dicas e novidades');
			$this->template->build('backend/dicas/list_page', array('dicas' => $dicas));
		} else {
			$funcao = 'dicas_'. $parans[0];
			unset($parans[0]);
			$this->$funcao( array_values($parans) );
		}
	}
	
	private function dicas_cadastrar()
	{
		if(count($_POST) > 0) {
			$dica = $this->preencherDica();
			$this->doctrine->em->persist($dica);
			$this->doctrine->em->flush();
			redirect('admin/dicas', 'refresh');
			
		} else {
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						titulo: "required",
						dica: "required"
					},
					messages:{
						titulo: "Informe o título.",
						dica: "Informe a dica."
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Nova dica e novidade');
			$this->template->build('backend/dicas/add_page');
		}
	}
	
	private function dicas_alterar($parans)
	{
		if(count($_POST) > 0) {
			$dica = $this->doctrine->em->createQuery('SELECT d FROM models\Dica d WHERE d.id=:id')
						 ->setParameter('id', $this->input->post('id'))->getSingleResult();
			
			$dica = $this->preencherDica($dica);
			$this->doctrine->em->flush();
			redirect('admin/dicas', 'refresh');
			
		} else {
			$dica = $this->doctrine->em->createQuery('SELECT d FROM models\Dica d WHERE d.id=:id')
						 ->setParameter('id', $parans[0])->getSingleResult();
			
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						titulo: "required",
						dica: "required"
					},
					messages:{
						titulo: "Informe o título.",
						dica: "Informe a dica."
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Alterar dica e novidade');
			$this->template->build('backend/dicas/edit_page', array('dica' => $dica));
		}
	}
	
	private function dicas_excluir($parans)
	{
		$dica = $this->doctrine->em->createQuery('SELECT d FROM models\Dica d WHERE d.id=:id')
					 ->setParameter('id', $parans[0])->getSingleResult();
		
		$this->doctrine->em->remove($dica);
		$this->doctrine->em->flush();
		
		redirect('admin/dicas', 'refresh');
	}
	
	private function preencherDica(models\Dica $dica = NULL)
	{
		if($dica == NULL)
			$dica = new models\Dica();
		
		$dica->setTitulo( $this->input->post('titulo') );
		$dica->setDica( $this->input->post('dica') );
		
		return $dica;
	}
	
	private function produtos($parans = NULL)
	{
		if(count($parans) == 0 || (isset($parans[0]) && $parans[0] === 'pagina')) {
			$inicio = 0;
			if(isset($parans[0]) && $parans[0] === 'pagina' && isset($parans[1]))
				$inicio = $parans[1];
			
			if(isset($_POST['pesquisa'])) {
				
				$busca = strtolower($_POST['pesquisa']);
				
				$quantidade = $this->doctrine->em->createQuery('SELECT COUNT(p) FROM models\Produto p WHERE ( LOWER(p.titulo) LIKE :busca OR LOWER(p.descricao) LIKE :busca OR p.codigo=:codigo )')
								   ->setParameter('busca', '%'. $busca .'%')
								   ->setParameter('codigo', $busca)->getSingleScalarResult();
				
				$produtos = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p WHERE ( LOWER(p.titulo) LIKE :busca OR LOWER(p.descricao) LIKE :busca OR p.codigo=:codigo ) ORDER BY p.data_criacao DESC')
								 ->setParameter('busca', '%'. $busca .'%')
								 ->setParameter('codigo', $busca)->setFirstResult($inicio)->setMaxResults(25)->getResult();
			} else {
				
				$quantidade = $this->doctrine->em->createQuery('SELECT COUNT(p) FROM models\Produto p')
								   ->getSingleScalarResult();
				
				$produtos = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p ORDER BY p.data_criacao DESC')
								 ->setFirstResult($inicio)->setMaxResults(25)->getResult();

			}
			
			$this->load->library('pagination');
			
			$config['base_url'] = 'produtos/pagina';
			$config['total_rows'] = $quantidade;
			$config['per_page'] = 25;
			$config['uri_segment'] = 4;
			$config['full_tag_open'] = '<div id="pagination">';
			$config['full_tag_close'] = '</div>';
			
			$this->pagination->initialize($config);
			
			$this->template->title('Área administrativa', 'Produtos');
			$this->template->build('backend/produtos/list_page', array('produtos' => $produtos));
		} else {
			$funcao = 'produtos_'. $parans[0];
			unset($parans[0]);
			$this->$funcao( array_values($parans) );
		}
	}
	
	private function produtos_alterar_destaque($parans)
	{
		$produto = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p WHERE p.codigo=:codigo')
						->setParameter('codigo', $parans[0])->getSingleResult();
		
		$produto->setDestaque( !$produto->isDestaque() );
		$this->doctrine->em->flush();
		redirect('admin/produtos', 'refresh');
	}
	
	private function produtos_alterar_status($parans)
	{
		$produto = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p WHERE p.codigo=:codigo')
						->setParameter('codigo', $parans[0])->getSingleResult();
		
		$produto->setAtivo( !$produto->isAtivo() );
		$this->doctrine->em->flush();
		redirect('admin/produtos', 'refresh');
	}
	
	private function produtos_cadastrar()
	{
		if(count($_POST) > 0) {
			$produto = $this->preencherProduto();
			$this->doctrine->em->persist($produto);
			$this->doctrine->em->flush();
			
			$_FILES['foto']['name'] = $produto->getCodigo() .'.jpg';
			$config['upload_path'] = $this->config->item('upload_path');
			$config['allowed_types'] = 'gif|jpg|png';
			
			$this->load->library('upload', $config);
			if($this->upload->do_upload('foto')) {
				$data = $this->upload->data();
				
				$this->load->library('image_lib');
				
				$config['image_library'] = 'gd2';
				$config['source_image'] = $data['full_path'];
				$config['new_image'] = $this->config->item('upload_path') .'/thumbs/'. $produto->getCodigo() .'.jpg';
				$config['thumb_marker'] = '';
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['width'] = 200;
				$config['height'] = 195;
				
				$this->image_lib->initialize($config);
				$this->image_lib->resize();
			}
			
			redirect('admin/produtos', 'refresh');
			
		} else {
			$assuntos = $this->doctrine->em->createQuery('SELECT a.id, a.titulo FROM models\Assunto a ORDER BY a.titulo')
						  	 ->getResult();
			
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						codigo: "required",
						titulo: "required",
						descricao: "required",
						unidade_venda: "required",
						composicao: "required",
						foto: "required",
						assuntos: "required"
					},
					messages:{
						codigo: "Informe o código.",
						titulo: "Informe o título.",
						descricao: "Informe a descrição.",
						unidade_venda: "Informe a unidade de venda.",
						composicao: "Informe a composição.",
						foto: "Informe a foto.",
						assuntos: "Informe o assunto."
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Novo produto');
			$this->template->build('backend/produtos/add_page', array('assuntos' => $assuntos));
		}
	}
	
	private function produtos_alterar($parans)
	{
		if(count($_POST) > 0) {
			$produto = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p WHERE p.codigo=:codigo')
							->setParameter('codigo', $this->input->post('codigo'))->getSingleResult();
			
			$produto = $this->preencherProduto($produto);
			$produto->setDataAlteracao( new DateTime() );
			$this->doctrine->em->flush();
			//var_dump($_FILES);
			if($_FILES['foto']['size'] > 0) {
				
				@unlink($this->config->item('upload_path') .'/'. $produto->getCodigo() .'.jpg');
				@unlink($this->config->item('upload_path') .'/thumbs/'. $produto->getCodigo() .'.jpg');
				
				$_FILES['foto']['name'] = $produto->getCodigo() .'.jpg';
				$config['upload_path'] = $this->config->item('upload_path');
				$config['allowed_types'] = 'gif|jpg|png';
				
				$this->load->library('upload', $config);
				if($this->upload->do_upload('foto')) {
					$data = $this->upload->data();
					
					$this->load->library('image_lib');
					
					$config['image_library'] = 'gd2';
					$config['source_image'] = $data['full_path'];
					$config['new_image'] = $this->config->item('upload_path') .'/thumbs/'. $produto->getCodigo() .'.jpg';
					$config['thumb_marker'] = '';
					$config['create_thumb'] = TRUE;
					$config['maintain_ratio'] = TRUE;
					$config['width'] = 200;
					$config['height'] = 195;
					
					$this->image_lib->initialize($config);
					$this->image_lib->resize();
				}
			}
			
			redirect('admin/produtos', 'refresh');
			
		} else {
			$assuntos = $this->doctrine->em->createQuery('SELECT a.id, a.titulo FROM models\Assunto a ORDER BY a.titulo')
						  	 ->getResult();
			
			$produto = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p WHERE p.codigo=:codigo')
							->setParameter('codigo', urldecode($parans[0]))->getSingleResult();
			
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						codigo: "required",
						titulo: "required",
						descricao: "required",
						unidade_venda: "required",
						composicao: "required",
						assuntos: "required"
					},
					messages:{
						codigo: "Informe o código.",
						titulo: "Informe o título.",
						descricao: "Informe a descrição.",
						unidade_venda: "Informe a unidade de venda.",
						composicao: "Informe a composição.",
						assuntos: "Informe o assunto."
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Alterar produto');
			$this->template->build('backend/produtos/edit_page', array('produto' => $produto, 'assuntos' => $assuntos));
		}
	}
	
	private function produtos_excluir($parans)
	{
		$produto = $this->doctrine->em->createQuery('SELECT p FROM models\Produto p WHERE p.codigo=:codigo')
						->setParameter('codigo', urldecode($parans[0]))->getSingleResult();
		
		@unlink($this->config->item('upload_path') .'/'. $produto->getCodigo() .'.jpg');
		@unlink($this->config->item('upload_path') .'/thumbs/'. $produto->getCodigo() .'.jpg');
		
		$this->doctrine->em->remove($produto);
		$this->doctrine->em->flush();
		
		redirect('admin/produtos', 'refresh');
	}
	
	private function preencherProduto(models\Produto $produto = NULL)
	{
		if($produto == NULL)
			$produto = new models\Produto();
		
		$produto->setCodigo( $this->input->post('codigo') );
		$produto->setTitulo( $this->input->post('titulo') );
		$produto->setDescricao( $this->input->post('descricao') );
		$produto->setUnidadeVenda( $this->input->post('unidade_venda') );
		$produto->setComposicao( $this->input->post('composicao') );
		$produto->setDestaque( $this->input->post('destaque') );
		$produto->setPromocao( $this->input->post('promocao') );
		$produto->setPreco( $this->input->post('preco') );
		$produto->setPrecoPromocional( $produto->isPromocao() ? $this->input->post('preco_promocional') : NULL );
		$produto->setAtivo(TRUE);
		
		$assuntos = $this->doctrine->em->createQuery('SELECT a FROM models\Assunto a WHERE a.id IN (:assunto)')
						 ->setParameter('assunto', $this->input->post('assuntos'))->getResult();
		
		$produto->resetAssunto();
		foreach($assuntos as $assunto)
			$produto->addAssunto($assunto);
		
		return $produto;
	}
	
	private function usuarios($parans = NULL)
	{
		if(count($parans) == 0 || (isset($parans[0]) && $parans[0] === 'pagina')) {
			$inicio = 0;
			if(isset($parans[0]) && $parans[0] === 'pagina' && isset($parans[1]))
				$inicio = $parans[1];
			
			$quantidade = $this->doctrine->em->createQuery('SELECT COUNT(u) FROM models\Usuario u')
							   ->getSingleScalarResult();
			
			$usuarios = $this->doctrine->em->createQuery('SELECT u FROM models\Usuario u ORDER BY u.data_criacao DESC')
						   	 ->setFirstResult($inicio)->setMaxResults(25)->getResult();
			
			$this->load->library('pagination');
			
			$config['base_url'] = 'usuarios/pagina';
			$config['total_rows'] = $quantidade;
			$config['per_page'] = 25;
			$config['uri_segment'] = 4;
			$config['full_tag_open'] = '<div id="pagination">';
			$config['full_tag_close'] = '</div>';
			
			$this->pagination->initialize($config);
			
			$this->template->title('Área administrativa', 'Usuários');
			$this->template->build('backend/usuarios/list_page', array('usuarios' => $usuarios));
		} else {
			$funcao = 'usuarios_'. $parans[0];
			unset($parans[0]);
			$this->$funcao( array_values($parans) );
		}
	}
	
	private function usuarios_alterar_status($parans)
	{
		$usuario = $this->doctrine->em->createQuery('SELECT u FROM models\Usuario u WHERE u.login=:login')
						->setParameter('login', $parans[0])->getSingleResult();
		
		$usuario->setAtivo( !$usuario->isAtivo() );
		$this->doctrine->em->flush();
		redirect('admin/usuarios', 'refresh');
	}
	
	private function usuarios_cadastrar()
	{
		if(count($_POST) > 0) {
			$usuario = $this->preencherUsuario();
			$this->doctrine->em->persist($usuario);
			$this->doctrine->em->flush();
			redirect('admin/usuarios', 'refresh');
			
		} else {
			$estados = $this->getEstados();
			$niveis_acesso = $this->getNiveisAcesso();
			
			$metadata = '<script type="text/javascript">$(document).ready(function(){
				$("form").validate({
					rules:{
						login: {
							required: true,
							email: true
						},
						senha: "required",
						confirmacao_senha: {
							equalTo: "#cadastro-senha"
						},
						nome: "required"
					},
					messages:{
						login: {
							required: "Informe o login.",
							email: "O login deve ser um email válido."
						},
						senha: "Informe o senha.",
						confirmacao_senha: {
							equalTo: "As senhas não conferem."
						},
						nome: "Informe o nome."
					}
				});
			});</script>';
			$this->template->append_metadata($metadata);
			
			$this->template->title('Área administrativa', 'Novo usuário');
			$this->template->build('backend/usuarios/add_page', array('estados' => $estados, 'niveis_acesso' => $niveis_acesso));
		}
	}
	
	private function usuarios_alterar($parans)
	{
		if(count($_POST) > 0) {
			$usuario = $this->doctrine->em->createQuery('SELECT u FROM models\Usuario u WHERE u.login=:login')
							->setParameter('login', $this->input->post('login'))->getSingleResult();
			
			$usuario = $this->preencherUsuario($usuario);
			$this->doctrine->em->flush();
			redirect('admin/usuarios', 'refresh');
			
		} else {
			$usuario = $this->doctrine->em->createQuery('SELECT u FROM models\Usuario u WHERE u.login=:login')
							->setParameter('login', urldecode($parans[0]))->getSingleResult();
			
			$estados = $this->getEstados();
			$niveis_acesso = $this->getNiveisAcesso();
			
			$this->template->title('Área administrativa', 'Alterar usuário');
			$this->template->build('backend/usuarios/edit_page', array('usuario' => $usuario, 'estados' => $estados, 'niveis_acesso' => $niveis_acesso));
		}
	}
	
	private function usuarios_excluir($parans)
	{
		$usuario = $this->doctrine->em->createQuery('SELECT u FROM models\Usuario u WHERE u.login=:login')
						->setParameter('login', urldecode($parans[0]))->getSingleResult();
		
		$this->doctrine->em->remove($usuario);
		$this->doctrine->em->flush();
		
		redirect('admin/usuarios', 'refresh');
	}
	
	private function preencherUsuario(models\Usuario $usuario = NULL)
	{
		if($usuario == NULL)
			$usuario = new models\Usuario();
		
		$senha = $this->input->post('senha');
		
		$usuario->setLogin( $this->input->post('login') );
		$usuario->setNome( $this->input->post('nome') );
		$usuario->setAreaAtuacao( $this->input->post('atuacao') != '' ? $this->input->post('atuacao') : NULL );
		$usuario->setDocumento( $this->input->post('documento') != '' ? $this->input->post('documento') : NULL );
		$usuario->setCep( $this->input->post('cep') != '' ? $this->input->post('cep') : NULL );
		$usuario->setEstado( $this->input->post('estado') != '' ? $this->input->post('estado') : NULL );
		$usuario->setCidade( $this->input->post('cidade') != '' ? $this->input->post('cidade') : NULL );
		$usuario->setBairro( $this->input->post('bairro') != '' ? $this->input->post('bairro') : NULL );
		$usuario->setEndereco( $this->input->post('endereco') != '' ? $this->input->post('endereco') : NULL );
		$usuario->setNumero( $this->input->post('numero') != '' ? $this->input->post('numero') : NULL );
		$usuario->setComplemento( $this->input->post('complemento') != '' ? $this->input->post('complemento') : NULL );
		$usuario->setTelefone( $this->input->post('telefone') != '' ? $this->input->post('telefone') : NULL );
		$usuario->setCelular( $this->input->post('celular' != '' ? $this->input->post('celular') : NULL) );
		//$usuario->setContato( $this->input->post('contato') != '' ? $this->input->post('contato') : NULL );
		$usuario->setNivelAcesso( $this->input->post('nivel-acesso') );
		$usuario->setAtivo(TRUE);
		
		$salt1 = $this->config->item('encryption_key');
		$salt2 = wordwrap(wordwrap($senha, 1, $usuario->getLogin(), TRUE), 2, $salt1, TRUE);
		$hash = hash('sha256', strtoupper($usuario->getLogin()).$salt2.md5($salt1.$senha));
		$usuario->setSenha($hash);
		
		return $usuario;
	}
	
	private function getEstados()
	{
		return array(
			'AC' => 'Acre', 'AL' => 'Alagoas', 'AM' => 'Amazonas', 
			'AP' => 'Amapá', 'BA' => 'Bahia', 'CE' => 'Ceará', 
			'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo', 
			'GO' => 'Goiás', 'MA' => 'Maranhão', 'MG' => 'Minas Gerais', 
			'MS' => 'Mato Grosso do Sul', 'MT' => 'Mato Grosso', 
			'PA' => 'Pará', 'PB' => 'Paraíba', 'PE' => 'Pernambuco', 
			'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte', 
			'RO' => 'Rondônia', 'RR' => 'Roraima', 'RS' => 'Rio Grande do Sul', 
			'SC' => 'Santa Catarina', 'SE' => 'Sergipe', 'SP' => 'São Paulo', 
			'TO' => 'Tocantins'
		);
	}
	
	private function getNiveisAcesso()
	{
		return array(
			'USUARIO' => 'Usuário', 'ADMINISTRADOR' => 'Administrador'
		);
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
