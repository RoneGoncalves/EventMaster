<?php
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;

/**
 * CompraForm
 * @author Ronaldo
 */
class CompraForm extends TPage
{
    protected $form;

    public function __construct($param)
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_compra');
        $this->form->setFormTitle('Cadastro de Compras');

        // Campos
        $id            = new TEntry('id');
        $id_evento     = new TDBCombo('id_evento', 'base', 'Evento', 'id', 'nome');
        $id_fornecedor = new TDBCombo('id_fornecedor', 'base', 'Fornecedor', 'id', 'nome');
        $produto       = new TEntry('produto');
        $quantidade    = new TEntry('quantidade');
        $valor         = new TEntry('valor');

        $id->setEditable(FALSE);
        $quantidade->setMask('9999');
        $valor->setNumericMask(2, ',', '.');

        // Campos no formulário
        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Evento')], [$id_evento]);
        $this->form->addFields([new TLabel('Fornecedor')], [$id_fornecedor]);
        $this->form->addFields([new TLabel('Produto')], [$produto]);
        $this->form->addFields([new TLabel('Quantidade')], [$quantidade]);
        $this->form->addFields([new TLabel('Valor')], [$valor]);

        // Ações
        $btnSave = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btnSave->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['CompraList', 'onClear']), 'fa:list brown');

        // Container
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

            $object = new Compra;
            $object->fromArray((array) $data);
            $object->store();

            TTransaction::close();

            new TMessage('info', 'Compra registrada com sucesso!');
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
                $object = new Compra($key);

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
