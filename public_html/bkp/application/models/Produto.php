<?php



namespace models;



use \Doctrine\Common\Collections\ArrayCollection;



/**

 * @Entity @Table(name="produtos")

 **/

class Produto {

	

	/**

	 * @Id @Column(type="integer", name="codigo")

	 * @var int

	 **/

	protected $codigo;

	

	/**

	 * @Column(type="string", name="titulo")

	 * @var string

	 **/

	protected $titulo;

	

	/**

	 * @Column(type="string", name="descricao")

	 * @var string

	 **/

	protected $descricao;

	

	/**

	 * @Column(type="string", name="unidade_venda")

	 * @var string

	 **/

	protected $unidade_venda;

	

	/**

	 * @Column(type="string", name="composicao")

	 * @var string

	 **/

	protected $composicao;

	

	/**

	 * @Column(type="boolean", name="destaque")

	 * @var boolean

	 **/

	protected $destaque;

	

	/**

	 * @Column(type="boolean", name="promocao")

	 * @var boolean

	 **/

	protected $promocao;

	

	/**

	 * @Column(type="float", name="preco")

	 * @var float

	 **/

	protected $preco;

	

	/**

	 * @Column(type="float", name="preco_promocional")

	 * @var float

	 **/

	protected $preco_promocional;

	

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


	/**

	 * @Column(type="datetime", name="data_alteracao")

	 * @var datetime

	 **/

	protected $data_alteracao;
	

	/**

     * @ManyToMany(targetEntity="Assunto", mappedBy="produtos")

     */

    private $assuntos;

	

	

	public function __construct() {

		$this->assuntos = new ArrayCollection();

    }

	

	public function getcodigo() {

		return $this->codigo;

	}

	

	public function setCodigo($codigo) {

		$this->codigo = $codigo;

	}

	

	public function getTitulo() {

		return $this->titulo;

	}

	

	public function setTitulo($titulo) {

		$this->titulo = $titulo;

	}

	

	public function getDescricao() {

		return $this->descricao;

	}

	

	public function setDescricao($descricao) {

		$this->descricao = $descricao;

	}

	

	public function getUnidadeVenda() {

		return $this->unidade_venda;

	}

	

	public function setUnidadeVenda($unidade_venda) {

		$this->unidade_venda = $unidade_venda;

	}

	

	public function getComposicao() {

		return $this->composicao;

	}

	

	public function setComposicao($composicao) {

		$this->composicao = $composicao;

	}

	

	public function isDestaque() {

		return $this->destaque;

	}

	

	public function setDestaque($destaque) {

		$this->destaque = $destaque;

	}

	

	public function isPromocao() {

		return $this->promocao;

	}

	

	public function setPromocao($promocao) {

		$this->promocao = $promocao;

	}

	

	public function getPreco() {

		return $this->preco;

	}

	

	public function setPreco($preco) {

		$this->preco = $preco;

	}

	

	public function getPrecoPromocional() {

		return $this->preco_promocional;

	}

	

	public function setPrecoPromocional($preco_promocional) {

		$this->preco_promocional = $preco_promocional;

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
	
	
	
	public function getDataAlteracao() {

		return $this->data_alteracao;

	}

	

	public function setDataAlteracao($data_alteracao) {

		$this->data_alteracao = $data_alteracao;

	}

	

	/**

     * Add assunto

     *

     * @param Assunto $assunto

     */

    public function addAssunto(Assunto $assunto)

    {

        $this->assuntos[] = $assunto;

		$assunto->addProduto($this);

    }

	

	public function resetAssunto()

	{

		foreach($this->assuntos as $assunto)

			$assunto->removerProduto($this);

		

		$this->assuntos = new ArrayCollection();

	}

	

	public function getAssuntos() {

		return $this->assuntos;

	}

}

