<?php

use Adianti\Widget\Form\TText;
use Adianti\Widget\Form\TEntry;

/**
 * ArtistaForm
 * @author Ronaldo
 */
class ArtistaForm extends TPage
{
    protected $form;

    public function __construct($param)
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_artista');
        $this->form->setFormTitle('Cadastro de Artistas');
        TSession::setValue('form_name', 'form_artista');

        // Campos do formulário
        $id             = new TEntry('id');
        $nome           = new TEntry('nome');
        $email          = new TEntry('email');
        $whatsapp       = new TEntry('whatsapp');
        $cidade_origem  = new TEntry('cidade_origem');
        $descricao      = new TText('descricao');

        $id->setEditable(FALSE);
        $descricao->setSize('100%', 80);

        // Adiciona campos ao formulário
        $this->form->addFields([new TLabel('ID', '12')], [$id]);
        $this->form->addFields([new TLabel('Nome', '12')], [$nome]);
        $this->form->addFields([new TLabel('Email', '12')], [$email]);
        $this->form->addFields([new TLabel('WhatsApp', '12')], [$whatsapp]);
        $this->form->addFields([new TLabel('Cidade de Origem', '12')], [$cidade_origem]);
        $this->form->addFields([new TLabel('Descrição', '12')], [$descricao]);

        // Botões de ação
        $saveBtn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $saveBtn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['ArtistaList', 'onClear']), 'fa:list-alt brown');

        $this->formFields = [$id, $nome, $email, $whatsapp, $cidade_origem, $descricao];

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

            $object = new Artista;
            $object->fromArray((array) $data);
            $object->store();

            $this->form->setData($object);
            TTransaction::close();

            new TMessage('info', 'Cadastro de artista realizado com sucesso!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear();
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];
                TTransaction::open('base');

                $object = new Artista($key);
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
