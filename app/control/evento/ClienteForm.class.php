<?php

use Adianti\Widget\Form\TEntry;

/**
 * ClienteForm
 * @author Ronaldo
 */
class ClienteForm extends TPage
{
    protected $form;

    public function __construct($param)
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_cliente');
        $this->form->setFormTitle('Cadastro de Clientes');
        TSession::setValue('form_name', 'form_cliente');

        // Campos
        $id        = new TEntry('id');
        $nome      = new TEntry('nome');
        $email     = new TEntry('email');
        $whatsapp  = new TEntry('whatsapp');

        $id->setEditable(FALSE);

        // Adiciona os campos ao formulário
        $this->form->addFields([new TLabel('ID', '12')], [$id],[],[]);
        $this->form->addFields([new TLabel('Nome', '12')], [$nome],[],[]);
        $this->form->addFields([new TLabel('Email', '12')], [$email],[],[]);
        $this->form->addFields([new TLabel('WhatsApp', '12')], [$whatsapp],[],[]);

        // Ações
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btnSave->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['ClienteList', 'onClear']), 'fa:list-alt brown');

        $this->formFields = [$id, $nome, $email, $whatsapp];

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    /**
     * Salva o registro no banco de dados
     */
    public function onSave($param)
    {
        try {
            TTransaction::open('base');

            $this->form->validate();
            $data = $this->form->getData();

            $object = new Cliente;
            $object->fromArray((array)$data);
            $object->store();

            $this->form->setData($object);
            TTransaction::close();

            new TMessage('info', 'Cliente cadastrado com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Limpa o formulário
     */
    public function onClear($param)
    {
        $this->form->clear();
    }

    /**
     * Carrega dados de um cliente existente para edição
     */
    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];
                TTransaction::open('base');

                $object = new Cliente($key);
                $this->form->setData($object);

                TTransaction::close();
            } else {
                $this->form->clear(TRUE);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
