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
    protected $form;

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
        $valor->setEditable(FALSE);
        $quantidade->setMask('9999');
        $valor->setNumericMask(2, ',', '.');

        // Eventos de atualização dinâmica
        $id_evento->setChangeAction(new TAction([$this, 'onUpdateValor']));
        $quantidade->setExitAction(new TAction([$this, 'onUpdateValor']));

        // Adicionar campos ao formulário
        $this->form->addFields([new TLabel('ID')], [$id],[],[]);
        $this->form->addFields([new TLabel('Evento')], [$id_evento],[],[]);
        $this->form->addFields([new TLabel('Cliente')], [$id_cliente],[],[]);
        $this->form->addFields([new TLabel('Produto')], [$produto],[],[]);
        $this->form->addFields([new TLabel('Quantidade')], [$quantidade],[],[]);
        $this->form->addFields([new TLabel('Valor Total')], [$valor],[],[]);

        // Ações
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(['VendaList', 'onClear']), 'fa:list brown');

        $this->formFields = [$id, $id_evento, $id_cliente, $produto, $quantidade, $valor];
        $this->form->setFields($this->formFields);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    /**
     * Atualiza o campo valor automaticamente com base no evento e quantidade
     */
    public static function onUpdateValor($param)
    {
        try {
            $data = (object) $param;

            $valor_total = 0;
            $formatted = '';

            if (!empty($data->id_evento)) {
                TTransaction::open('base');

                // tenta obter evento e o valor do convite (pode ser null)
                $evento = new Evento($data->id_evento);
                $preco_convite = isset($evento->valor_convite) && is_numeric($evento->valor_convite)
                                 ? (float) $evento->valor_convite
                                 : 0.0;

                $quantidade = isset($data->quantidade) && is_numeric($data->quantidade)
                              ? (int) $data->quantidade
                              : 0;

                $valor_total = $preco_convite * $quantidade;

                TTransaction::close();
            }

            if (is_numeric($valor_total)) {
                $formatted = number_format((float)$valor_total, 2, ',', '.');
            } else {
                $formatted = '';
            }

            $obj = new stdClass;
            $obj->valor = $formatted;

            TForm::sendData('form_venda', $obj);

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Salva os dados da venda
     */
    public function onSave($param)
    {
        try {
            TTransaction::open('base');

            $data = $this->form->getData();
            $this->form->validate();

            // Converter valor para formato numérico antes de salvar
            $data->valor = str_replace(['.', ','], ['', '.'], $data->valor);

            $object = new Venda;
            $object->fromArray((array) $data);
            $object->store();

            TTransaction::close();

            // === Reatribui os dados para o formulário ===
            // Reconverte o valor numérico para formato brasileiro
            $data->valor = number_format($object->valor, 2, ',', '.');

            // Atualiza o formulário com os dados usados
            $this->form->setData($data);

            // Exibe mensagem sem limpar o formulário
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

                // somente formatar se for numérico
                if (isset($object->valor) && is_numeric($object->valor)) {
                    $object->valor = number_format((float)$object->valor, 2, ',', '.');
                } else {
                    $object->valor = '';
                }

                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear(TRUE);
            }
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onClear($param)
    {
        $this->form->clear();
    }
}
