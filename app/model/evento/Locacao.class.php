<?php
/**
 * Locacao Active Record
 * @author 
 */
class Locacao extends TRecord
{
    const TABLENAME  = 'locacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}

    private $evento;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id_evento');
        parent::addAttribute('nome');
        parent::addAttribute('endereco');
        parent::addAttribute('email');
        parent::addAttribute('whatsapp');
        parent::addAttribute('cidade');
        parent::addAttribute('descricao');
        parent::addAttribute('valor');
    }

    /**
     * Method set_evento
     * Sample of usage: $locacao->evento = $object;
     * @param $object Instance of Evento
     */
    public function set_evento(Evento $object)
    {
        $this->evento = $object;
        $this->id_evento = $object->id;
    }

    /**
     * Method get_evento
     * Sample of usage: $locacao->evento->attribute;
     * @returns Evento instance
     */
    public function get_evento()
    {
        if (empty($this->evento))
            $this->evento = new Evento($this->id_evento);

        return $this->evento;
    }
}
