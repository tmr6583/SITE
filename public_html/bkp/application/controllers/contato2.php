<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contato extends CI_Controller {
	
	public function index()
	{
		$metadata = '<script src="lib/js/jquery.validate.pack.js" type="text/javascript"></script>';
		$metadata .= '<script type="text/javascript">$(document).ready(function(){$("form[name=frmContato]").validate({rules:{nome:"required",email:{required:true,email:true},telefone:"required",mensagem:"required"},messages:{nome:"Informe seu nome.",email:{required:"Informe seu email.",email: "Email inválido."},telefone: "Informe seu telefone.",mensagem: "Escreva sua mensagem."}});});</script>';
		$this->template->append_metadata($metadata);
		
		$this->template->title('Fale conosco');
		$this->template->build('frontend/contato_page');
	}
	
	public function enviar()
	{
		$nome = $this->input->post('nome');
		$email = $this->input->post('email');
		$telefone = $this->input->post('telefone');
		$assunto = 'Contato pelo site';
		
		$mensagem  = "O sr(a). {$nome} entrou em contato atravez do site betina.com.br.\n";
		$mensagem .= "Email: {$email}\n";
		$mensagem .= "Telefone: {$telefone}\n\n";
		$mensagem .= "{$assunto}\n";
		$mensagem .= $this->input->post('mensagem');
		
		$this->load->library('email');
		$this->email->from('betina@betinalimpeza.com.br', $assunto);
		$this->email->to('betina@betinalimpeza.com.br');
		$this->email->subject($assunto);
		$this->email->message($mensagem);
		$this->email->send();
		
		redirect('', 'refresh');
	}
}

/* End of file contato.php */
/* Location: ./application/controllers/contato.php */
