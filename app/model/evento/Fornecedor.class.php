<?php
/**
 * Fornecedor Active Record
 */
class Fornecedor extends TRecord
{
    const TABLENAME  = 'fornecedores';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('nome');
        parent::addAttribute('cpfCnpj');
        parent::addAttribute('email');
        parent::addAttribute('whatsapp');
    }
}
