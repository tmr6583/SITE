<?php

namespace models;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="assinantes")
 **/
class Assinante {
	
	/**
	 * @Id @Column(type="string", name="email")
	 * @var string
	 **/
	protected $email;
	
	/**
	 * @Column(type="datetime", name="data_criacao")
	 * @var datetime
	 **/
	protected $data_criacao;
	
	
	public function getEmail() {
		return $this->email;
	}
	
	public function setEmail($email) {
		$this->email = $email;
	}
	
	public function getDataCriacao() {
		return $this->data_criacao;
	}
	
	public function setDataCriacao($data_criacao) {
		$this->data_criacao = $data_criacao;
	}
}
