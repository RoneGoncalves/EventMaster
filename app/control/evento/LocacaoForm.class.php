<?php

use Adianti\Widget\Form\TText;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TCombo;

/**
 * LocacaoForm Form
 * @author Ronaldo
 */
class LocacaoForm extends TPage
{
    protected $form; // form

    public function __construct($param)
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_locacao');
        $this->form->setFormTitle('Cadastro de Locações');

        TSession::setValue('form_name', 'form_Locacao');

        $this->form->appendPage('Informações da Locação');

        // === Campos ===
        $id         = new TEntry('id');
        $id_evento  = new TDBCombo('id_evento', 'base', 'Evento', 'id', 'nome');
        $nome       = new TEntry('nome');
        $endereco   = new TEntry('endereco');
        $email      = new TEntry('email');
        $whatsapp   = new TEntry('whatsapp');
        $cidade     = new TEntry('cidade');
        $descricao  = new TText('descricao');
        $valor      = new TEntry('valor');

        // === Configurações ===
        $id->setEditable(FALSE);
        $valor->setNumericMask(2, ',', '.');
        $descricao->setSize('100%', 80);

        // === Adiciona campos ===
        $this->form->addFields([new TLabel('ID')], [$id],[],[]);
        $this->form->addFields([new TLabel('Evento')], [$id_evento],[],[]);
        $this->form->addFields([new TLabel('Nome')], [$nome],[],[]);
        $this->form->addFields([new TLabel('Endereço')], [$endereco],[],[]);
        $this->form->addFields([new TLabel('E-mail')], [$email],[],[]);
        $this->form->addFields([new TLabel('WhatsApp')], [$whatsapp],[],[]);
        $this->form->addFields([new TLabel('Cidade')], [$cidade],[],[]);
        $this->form->addFields([new TLabel('Valor (R$)')], [$valor],[],[]);
        $this->form->addFields([new TLabel('Descrição')], [$descricao],[],[]);

        // === Ações ===
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btnSave->class = 'btn btn-sm btn-primary';

        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['LocacaoList', 'onClear']), 'fa:list-alt brown');

        $this->formFields = [$id, $id_evento, $nome, $endereco, $email, $whatsapp, $cidade, $descricao, $valor];

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('base');

            $data = $this->form->getData();

            // converte formato do valor
            $data->valor = str_replace(['.', ','], ['', '.'], $data->valor);

            $this->form->validate();

            $object = new Locacao;
            $object->fromArray((array) $data);
            $object->store();

            new TMessage('info', 'Locação cadastrada com sucesso!');
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('base');
                $object = new Locacao($key);

                $object->valor = number_format($object->valor, 2, ',', '.');

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

    public function onClear($param)
    {
        $this->form->clear();
    }
}
