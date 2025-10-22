<?php
/**
 * Compra Active Record
 */
class Compra extends TRecord
{
    const TABLENAME  = 'compras';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    private $evento;
    private $fornecedor;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id_evento');
        parent::addAttribute('id_fornecedor');
        parent::addAttribute('produto');
        parent::addAttribute('quantidade');
        parent::addAttribute('valor');
    }

    public function set_evento(Evento $object)
    {
        $this->evento = $object;
        $this->id_evento = $object->id;
    }

    public function get_evento()
    {
        if (empty($this->evento))
            $this->evento = new Evento($this->id_evento);

        return $this->evento;
    }

    public function set_fornecedor(Fornecedor $object)
    {
        $this->fornecedor = $object;
        $this->id_fornecedor = $object->id;
    }

    public function get_fornecedor()
    {
        if (empty($this->fornecedor))
            $this->fornecedor = new Fornecedor($this->id_fornecedor);

        return $this->fornecedor;
    }
}
