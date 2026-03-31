<?php

namespace models;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="usuarios")
 **/
class Usuario {
	
	/**
	 * @Id @Column(type="string", name="login")
	 * @var string
	 **/
	protected $login;
	
	/**
	 * @Column(type="string", name="senha")
	 * @var string
	 **/
	protected $senha;
	
	/**
	 * @Column(type="string", name="nome")
	 * @var string
	 **/
	protected $nome;
	
	/**
	 * @Column(type="string", name="empresa")
	 * @var string
	 **/
	protected $empresa;
	
	/**
	 * @Column(type="string", name="area_atuacao")
	 * @var string
	 **/
	protected $area_atuacao;
	
	/**
	 * @Column(type="string", name="documento")
	 * @var string
	 **/
	protected $documento;
	
	/**
	 * @Column(type="string", name="cep")
	 * @var string
	 **/
	protected $cep;
	
	/**
	 * @Column(type="string", name="estado")
	 * @var string
	 **/
	protected $estado;
	
	/**
	 * @Column(type="string", name="cidade")
	 * @var string
	 **/
	protected $cidade;
	
	/**
	 * @Column(type="string", name="bairro")
	 * @var string
	 **/
	protected $bairro;
	
	/**
	 * @Column(type="string", name="endereco")
	 * @var string
	 **/
	protected $endereco;
	
	/**
	 * @Column(type="integer", name="numero")
	 * @var int
	 **/
	protected $numero;
	
	/**
	 * @Column(type="string", name="complemento")
	 * @var string
	 **/
	protected $complemento;
	
	/**
	 * @Column(type="integer", name="telefone")
	 * @var int
	 **/
	protected $telefone;
	
	/**
	 * @Column(type="integer", name="celular")
	 * @var int
	 **/
	protected $celular;
	
	/**
	 * @Column(type="string", name="nivel_acesso")
	 * @var string
	 **/
	protected $nivel_acesso;
	
	/**
	 * @Column(type="boolean", name="ativo")
	 * @var boolean
	 **/
	protected $ativo;
	
	/**
	 * @Column(type="datetime", name="data_criacao")
	 * @var datetime
	 **/
	protected $data_criacao;
	
	
	public function getLogin() {
		return $this->login;
	}
	
	public function setLogin($login) {
		$this->login = $login;
	}
	
	public function getSenha() {
		return $this->senha;
	}
	
	public function setSenha($senha) {
		$this->senha = $senha;
	}
	
	public function getNome() {
		return $this->nome;
	}
	
	public function setNome($nome) {
		$this->nome = $nome;
	}
	
	public function getEmpresa() {
		return $this->empresa;
	}
	
	public function setEmpresa($empresa) {
		$this->nome = $empresa;
	}
	
	public function getAreaAtuacao() {
		return $this->area_atuacao;
	}
	
	public function setAreaAtuacao($area_atuacao) {
		$this->area_atuacao = $area_atuacao;
	}
	
	public function getDocumento() {
		return $this->documento;
	}
	
	public function setDocumento($documento) {
		$this->documento = $documento;
	}
	
	public function getCep() {
		return $this->cep;
	}
	
	public function setCep($cep) {
		$this->cep = $cep;
	}
	
	public function getEstado() {
		return $this->estado;
	}
	
	public function setEstado($estado) {
		$this->estado = $estado;
	}
	
	public function getCidade() {
		return $this->cidade;
	}
	
	public function setCidade($cidade) {
		$this->cidade = $cidade;
	}
	
	public function getBairro() {
		return $this->bairro;
	}
	
	public function setBairro($bairro) {
		$this->bairro = $bairro;
	}
	
	public function getEndereco() {
		return $this->endereco;
	}
	
	public function setEndereco($endereco) {
		$this->endereco = $endereco;
	}
	
	public function getNumero() {
		return $this->numero;
	}
	
	public function setNumero($numero) {
		$this->numero = $numero;
	}
	
	public function getComplemento() {
		return $this->complemento;
	}
	
	public function setComplemento($complemento) {
		$this->complemento = $complemento;
	}
	
	public function getTelefone() {
		return $this->telefone;
	}
	
	public function setTelefone($telefone) {
		$this->telefone = $telefone;
	}
	
	public function getCelular() {
		return $this->celular;
	}
	
	public function setCelular($celular) {
		$this->celular = $celular;
	}
	
	public function getNivelAcesso() {
		return $this->nivel_acesso;
	}
	
	public function setNivelAcesso($nivel_acesso) {
		$this->nivel_acesso = $nivel_acesso;
	}
	
	public function isAtivo() {
		return $this->ativo;
	}
	
	public function setAtivo($ativo) {
		$this->ativo = $ativo;
	}
	
	public function getDataCriacao() {
		return $this->data_criacao;
	}
	
	public function setDataCriacao($data_criacao) {
		$this->data_criacao = $data_criacao;
	}
}
