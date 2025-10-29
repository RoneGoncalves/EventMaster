<?php
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TText;
use Adianti\Widget\Form\TNumeric;

/**
 * VendaForm
 * @author Ronaldo
 */
class VendaForm extends TPage
{
    protected $form; // form

    public function __construct($param)
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_venda');
        $this->form->setFormTitle('Cadastro de Vendas');

        TSession::setValue('form_name', 'form_venda');

        // Campos do formulário
        $id          = new TEntry('id');
        $id_evento   = new TDBCombo('id_evento', 'base', 'Evento', 'id', 'nome');
        $id_cliente  = new TDBCombo('id_cliente', 'base', 'Cliente', 'id', 'nome');
        $produto     = new TEntry('produto');
        $quantidade  = new TEntry('quantidade');
        $valor       = new TEntry('valor');

        $id->setEditable(false);
        $quantidade->setMask('9999');
        $valor->setNumericMask(2, ',', '.');

        // Adicionar campos no form
        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Evento')], [$id_evento]);
        $this->form->addFields([new TLabel('Cliente')], [$id_cliente]);
        $this->form->addFields([new TLabel('Produto')], [$produto]);
        $this->form->addFields([new TLabel('Quantidade')], [$quantidade]);
        $this->form->addFields([new TLabel('Valor')], [$valor]);

        // Ações
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['VendaList', 'onClear']), 'fa:list brown');

        $this->formFields = [$id, $id_evento, $id_cliente, $produto, $quantidade, $valor];

        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('base');

            $data = $this->form->getData();
            $this->form->validate();

            $data->valor = str_replace(['.', ','], ['', '.'], $data->valor);

            $object = new Venda;
            $object->fromArray((array) $data);
            $object->store();

            TTransaction::close();

            new TMessage('info', 'Venda registrada com sucesso!');
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
                $object = new Venda($key);

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
