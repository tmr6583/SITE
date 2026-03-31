<?php

namespace models;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="assuntos")
 **/
class Assunto {
	
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
	 * @Column(type="boolean", name="ativo")
	 * @var boolean
	 **/
	protected $ativo;
	
	/**
     * @ManyToMany(targetEntity="Produto")
     * @JoinTable(name="assuntos_has_produtos",
     *      joinColumns={@JoinColumn(name="assuntos_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="produtos_codigo", referencedColumnName="codigo")}
     * )
     **/
	protected $produtos;
	
	
	public function __construct() {
		$this->produtos = new ArrayCollection();
    }
	
	public function getId() {
		return $this->id;
	}
	
	public function getTitulo() {
		return $this->titulo;
	}
	
	public function setTitulo($titulo) {
		$this->titulo = $titulo;
	}
	
	public function isAtivo() {
		return $this->ativo;
	}
	
	public function setAtivo($ativo) {
		$this->ativo = $ativo;
	}
	
	/**
     * Add produto
     *
     * @param Produto $produto
     */
    public function addProduto(Produto $produto)
    {
        $this->produtos[] = $produto;
    }
	
	public function removerProduto(Produto $produto)
	{
		$this->produtos->removeElement($produto);
	}
	
	public function getProdutos() {
		return $this->produtos;
	}
}
