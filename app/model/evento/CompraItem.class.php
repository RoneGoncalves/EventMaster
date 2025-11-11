<?php
/**
 * CompraItem Active Record
 * Representa os itens vinculados a uma compra
 * @author Ronaldo
 */
class CompraItem extends TRecord
{
    const TABLENAME  = 'compra_item';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial'; // auto incremento

    private $compra; // relação com a compra (opcional)

    /**
     * Método construtor
     * @param int|null $id
     * @param bool $loadObject
     */
    public function __construct($id = NULL, $loadObject = TRUE)
    {
        parent::__construct($id, $loadObject);

        // define os atributos do banco
        parent::addAttribute('id_compra');
        parent::addAttribute('descricao_produto');
        parent::addAttribute('quantidade');
        parent::addAttribute('valor_unitario');
        parent::addAttribute('valor_total');
    }

    // ===== MÉTODOS AUXILIARES =====

    /**
     * Atualiza o valor_total com base em quantidade * valor_unitario
     */
    private function atualizaValorTotal()
    {
        if (!empty($this->data['quantidade']) && !empty($this->data['valor_unitario'])) {
            $this->data['valor_total'] = $this->data['quantidade'] * $this->data['valor_unitario'];
        }
    }

    /**
     * Retorna o objeto da compra associada
     */
    public function get_compra()
    {
        if (empty($this->compra) && !empty($this->id_compra)) {
            $this->compra = new Compra($this->id_compra);
        }
        return $this->compra;
    }
}
