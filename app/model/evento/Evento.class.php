<?php
/**
 * Evento Active Record
 * @author 
 */
class Evento extends TRecord
{
    const TABLENAME  = 'evento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('nome');
        parent::addAttribute('data_evento');
        parent::addAttribute('horario');
        parent::addAttribute('prev_orcamento');
        parent::addAttribute('est_publico');
        parent::addAttribute('publico_efetivo');
        parent::addAttribute('valor_convite');
        parent::addAttribute('descricao');
    }
}