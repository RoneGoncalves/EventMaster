<?php
/**
 * FornecedorForm
 */
class FornecedorForm extends TPage
{
    protected $form; // formulário

    public function __construct()
    {
        parent::__construct();

        // cria o formulário
        $this->form = new BootstrapFormBuilder('form_Fornecedor');
        $this->form->setFormTitle('Cadastro de Fornecedor');

        // cria os campos
        $id        = new THidden('id');
        $nome      = new TEntry('nome');
        $cpf_cnpj   = new TEntry('cpf_cnpj');
        $email     = new TEntry('email');
        $whatsapp  = new TEntry('whatsapp');

        // adiciona os campos
        $this->form->addFields([new TLabel('Nome')], [$nome]);
        $this->form->addFields([new TLabel('CPF/CNPJ')], [$cpf_cnpj]);
        $this->form->addFields([new TLabel('E-mail')], [$email]);
        $this->form->addFields([new TLabel('WhatsApp')], [$whatsapp]);
        $this->form->addFields([], [$id]);

        // validações
        $nome->addValidation('Nome', new TRequiredValidator);
        $cpf_cnpj->addValidation('CPF/CNPJ', new TRequiredValidator);

         // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'),  new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(array('FornecedorList', 'onClear')), 'fa:list-alt brown' );

        // container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('base');

            $this->form->validate();
            $data = $this->form->getData();

            $fornecedor = new Fornecedor;
            $fornecedor->fromArray((array) $data);
            $fornecedor->store();

            $this->form->setData($fornecedor);

            TTransaction::close();

            new TMessage('info', 'Fornecedor salvo com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                TTransaction::open('base');
                $fornecedor = new Fornecedor($param['key']);
                $this->form->setData($fornecedor);
                TTransaction::close();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear()
    {
        $this->form->clear();
    }
}
