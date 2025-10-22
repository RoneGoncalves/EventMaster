<?php
/**
 * Venda Active Record
 */
class Venda extends TRecord
{
    const TABLENAME  = 'vendas';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    private $evento;
    private $cliente;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id_evento');
        parent::addAttribute('id_cliente');
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

    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->id_cliente = $object->id;
    }

    public function get_cliente()
    {
        if (empty($this->cliente))
            $this->cliente = new Cliente($this->id_cliente);

        return $this->cliente;
    }
}
