<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cadastro extends CI_Controller {
	
	public function index()
	{
		$this->template->title('Cadastro / Login');
		$this->template->build('frontend/cadastro_page');
	}
	
	public function login()
	{
		$email = $this->input->post('email');
		$senha = $this->input->post('senha');
		
		$salt1 = $this->config->item('encryption_key');
		$salt2 = wordwrap(wordwrap($senha, 1, $email, TRUE), 2, $salt1, TRUE);
		$hash = hash('sha256', strtoupper($email).$salt2.md5($salt1.$senha));
		
		$usuario = $this->doctrine->em->createQuery('SELECT u FROM models\Usuario u WHERE u.login=:email AND u.senha=:senha AND u.ativo=true')
						->setParameter('email', $email)->setParameter('senha', $hash)->getResult();
		
		if($usuario != NULL) {
			$this->session->set_userdata('usuario', array('login' => $usuario[0]->getLogin(), 'nome' => $usuario[0]->getNome(), 'nivel_acesso' => $usuario[0]->getNivelAcesso()));
			if($usuario[0]->getNivelAcesso() === 'ADMINISTRADOR')
				redirect('admin', 'refresh');
			else
				redirect('', 'refresh');
			
		} else {
			echo '<b>Email / Senha inv&aacute;lido(s)!</b><br>Aguarde e cadastre-se em nosso site.';
			redirect('cadastro/registro', 'refresh', NULL, 8);
		}
	}
	
	public function sair()
	{
		$this->session->unset_userdata('usuario');
		redirect('', 'refresh');
	}
	
	public function novaSenha($email = '')
	{
		$senha = $this->geraSenha();
		
		$salt1 = $this->config->item('encryption_key');
		$salt2 = wordwrap(wordwrap($senha, 1, $email, TRUE), 2, $salt1, TRUE);
		$hash = hash('sha256', strtoupper($email).$salt2.md5($salt1.$senha));
		
		try
		{
			$usuario = $this->doctrine->em->createQuery('SELECT u FROM models\Usuario u WHERE u.login=:login')
							->setParameter('login', $email)->getSingleResult();
			
			$usuario->setSenha($hash);
			$this->doctrine->em->flush();
			
			$nome = $usuario->getNome();
			$assunto = 'Nova senha';
			
			$mensagem  = "Sr(a). {$nome}, sua nova senha de acesso ao site betina.com.br foi gerada.\n\n";
			$mensagem .= "Sua nova senha é: {$senha}";
			
			$this->load->library('email');
			$this->email->from('contato@betinalimpeza.com.br', $assunto);
			$this->email->to('contato@betinalimpeza.com.br');//$this->email->to($email);
			$this->email->subject($assunto);
			$this->email->message($mensagem);
			$this->email->send();
					
			echo '<b>Aguarde um momento!</b><br>Uma nova senha foi enviada para o seu email.';
		}
		catch (Exception $e)
		{
			echo '<b>Email n&atilde;o cadastrado!</b><br>Aguarde um momento e cadastre-se em nosso site.';
		}
		
		redirect('cadastro', 'refresh', NULL, 8);
	}
	
	public function registro()
	{
		$estados = $this->getEstados();
		
		$metadata = '<script src="lib/js/jquery.validate.pack.js" type="text/javascript"></script>';
		$metadata .= '<script type="text/javascript">$(document).ready(function(){
        $("form[name=frmCadastro]").validate({
            rules:{
                login: {
                    required: true,
                    email: true
                },
                senha: "required",
				confirmacao_senha: {
					equalTo: "#cadastro-senha"
				},
                nome: "required",
				empresa: "required",
                atuacao: "required",
				documento: "required",
				cep: "required",
				estado: "required",
				cidade: "required",
				bairro: "required",
				endereco: "required",
				numero: "required",
				telefone: "required"
            },
            messages:{
                login: {
                    required: "Informe seu login.",
                    email: "Seu login deve ser um email válido."
                },
                senha: "Informe sua senha.",
				confirmacao_senha: {
					equalTo: "As senhas não conferem."
				},
                nome: "Informe seu nome.",
				empresa: "Informe sua empresa.",
                atuacao: "Informe sua área de atuação.",
				documento: "Informe seu documento.",
				cep: "Informe seu cep.",
				estado: "Informe seu estado.",
				cidade: "Informe sua cidade.",
				bairro: "Informe seu bairro.",
				endereco: "Informe seu endereço.",
				numero: "Informe seu número.",
				telefone: "Informe seu telefone."
            }
 
        });
    });</script>';
		$this->template->append_metadata($metadata);
		
		$this->template->title('Cadastro');
		$this->template->build('frontend/registro_page', array('estados' => $estados));
	}
	
	public function salvar()
	{
		$usuario = new models\Usuario();
		
		$senha = $this->input->post('senha');
		
		$usuario->setLogin( $this->input->post('login') );
		$usuario->setNome( $this->input->post('nome') );
		$usuario->setEmpresa( $this->input->post('empresa') );
		$usuario->setAreaAtuacao( $this->input->post('atuacao') );
		$usuario->setDocumento( $this->input->post('documento') );
		$usuario->setCep( $this->input->post('cep') );
		$usuario->setEstado( $this->input->post('estado') );
		$usuario->setCidade( $this->input->post('cidade') );
		$usuario->setBairro( $this->input->post('bairro') );
		$usuario->setEndereco( $this->input->post('endereco') );
		$usuario->setNumero( $this->input->post('numero') );
		$usuario->setComplemento( $this->input->post('complemento') );
		$usuario->setTelefone( $this->input->post('telefone') );
		$usuario->setCelular( $this->input->post('celular') );
		$usuario->setNivelAcesso('USUARIO');
		$usuario->setAtivo(TRUE);
		
		$salt1 = $this->config->item('encryption_key');
		$salt2 = wordwrap(wordwrap($senha, 1, $usuario->getLogin(), TRUE), 2, $salt1, TRUE);
		$hash = hash('sha256', strtoupper($usuario->getLogin()).$salt2.md5($salt1.$senha));
		$usuario->setSenha($hash);
		
		$this->doctrine->em->persist($usuario);
		$this->doctrine->em->flush();
		
		redirect('cadastro', 'refresh');
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
	
	private function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
	{
		$lmin = 'abcdefghijklmnopqrstuvwxyz';
		$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num = '1234567890';
		$simb = '!@#$%*-';
		$retorno = '';
		$caracteres = '';
		
		$caracteres .= $lmin;
		if ($maiusculas) $caracteres .= $lmai;
		if ($numeros) $caracteres .= $num;
		if ($simbolos) $caracteres .= $simb;
		
		$len = strlen($caracteres);
		for ($n = 1; $n <= $tamanho; $n++) {
			$rand = mt_rand(1, $len);
			$retorno .= $caracteres[$rand-1];
		}
		
		return $retorno;
	}
}

/* End of file cadastro.php */
/* Location: ./application/controllers/cadastro.php */
