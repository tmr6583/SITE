<?php

namespace models;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="cotacoes")
 **/
class Cotacao {
	
	/**
	 * @Id @GeneratedValue @Column(type="integer", name="id")
	 * @var int
	 **/
	protected $id;
	
	/**
	 * @Column(type="string", name="usuarios_login")
	 * @var string
	 **/
	protected $usuario;
	
	/**
	 * @Column(type="integer", name="quantidade_total")
	 * @var int
	 **/
	protected $quantidade_total;
	
	/**
	 * @Column(type="datetime", name="data_criacao")
	 * @var datetime
	 **/
	protected $data_criacao;
	
	/**
	 * @OnetoMany(targetEntity="ItemCotacao", mappedBy="itens")
     */
	protected $itens;
	
	
	public function __construct() {
		$this->itens = new ArrayCollection();
    }
	
	public function getId() {
		return $this->id;
	}
	
	public function getUsuario() {
		return $this->usuario;
	}
	
	public function setUsuario($usuario) {
		$this->usuario = $usuario;
	}
	
	public function getQuantidadeTotal() {
		return $this->quantidade_total;
	}
	
	public function setQuantidadeTotal($quantidade_total) {
		$this->quantidade_total = $quantidade_total;
	}
	
	public function getDataCriacao() {
		return $this->data_criacao;
	}
	
	public function setDataCriacao($data_criacao) {
		$this->data_criacao = $data_criacao;
	}
	
	/**
     * Add ItemCotacao
     *
     * @param ItemCotacao $item
     */
    public function addItem(ItemCotacao $item)
    {
		$item->setCotacao($this);
        $this->itens[] = $item;
    }
	
	public function removerItem(ItemCotacao $item)
	{
		$item->setCotacao(NULL);
		$this->itens->removeElement($item);
	}
	
	public function getItens() {
		return $this->itens;
	}
}
