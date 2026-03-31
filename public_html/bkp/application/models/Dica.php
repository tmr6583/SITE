<?php

namespace models;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="dicas")
 **/
class Dica {
	
	/**
	 * @Id @GeneratedValue @Column(type="integer", name="id")
	 * @var int
	 **/
	protected $id;
	
	/**
	 * @Column(type="string", name="titulo")
	 * @var string
	 **/
	protected $titulo;
	
	/**
	 * @Column(type="string", name="dica")
	 * @var string
	 **/
	protected $dica;
	
	
	public function getId() {
		return $this->id;
	}
	
	public function getTitulo() {
		return $this->titulo;
	}
	
	public function setTitulo($titulo) {
		$this->titulo = $titulo;
	}
	
	public function getDica() {
		return $this->dica;
	}
	
	public function setDica($dica) {
		$this->dica = $dica;
	}
}
