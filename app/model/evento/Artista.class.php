<?php
/**
 * Artista Active Record
 */
class Artista extends TRecord
{
    const TABLENAME  = 'artista';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('nome');
        parent::addAttribute('email');
        parent::addAttribute('whatsapp');
        parent::addAttribute('cidade_origem');
        parent::addAttribute('descricao');
    }
}
