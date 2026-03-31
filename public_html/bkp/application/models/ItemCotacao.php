<?php

namespace models;

/**
 * @Entity @Table(name="cotacoes_has_produtos")
 **/
class ItemCotacao {
	
	/**
     * @Id @ManyToOne(targetEntity="Cotacao", inversedBy="itens", cascade={"persist"})
	 * @JoinColumn(name="cotacoes_id", referencedColumnName="id")
     */
	protected $cotacao;
	
	/**
     * @Id @Column(type="integer", name="produtos_codigo")
	 * @var int
     */
	protected $produto;
	
	/**
	 * @Column(type="integer", name="quantidade")
	 * @var int
	 **/
	protected $quantidade;
	
	
	public function setCotacao($cotacao) {
		$this->cotacao = $cotacao;
	}
	
	public function getCotacao() {
		return $this->cotacao;
	}
	
	public function getProduto() {
		return $this->produto;
	}
	
	public function setProduto($produto) {
		$this->produto = $produto;
	}
	
	public function getQuantidade() {
		return $this->quantidade;
	}
	
	public function setQuantidade($quantidade) {
		$this->quantidade = $quantidade;
	}
}
