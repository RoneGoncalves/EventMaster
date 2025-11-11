<?php

use Adianti\Base\TStandardForm;
use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Wrapper\BootstrapFormBuilder; // ✅ ESSENCIAL
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TDataGridAction;




class CompraForm extends TPage
{
    protected $form;          
    protected $datagrid_item; 

    public function __construct()
    {
        parent::__construct();

        // ===== FORM PRINCIPAL (ÚNICO) =====
        $this->form = new BootstrapFormBuilder('form_compra');
        $this->form->setFormTitle('Cadastro de Compras');

        /**
         * =====================
         * ABA 1 - Dados da Compra
         * =====================
         */
        $this->form->appendPage('Dados da Compra');

        $id            = new TEntry('id');
        $id_evento     = new TDBCombo('id_evento', 'base', 'Evento', 'id', 'nome');
        $id_fornecedor = new TDBCombo('id_fornecedor', 'base', 'Fornecedor', 'id', 'nome');

        $id->setEditable(FALSE);
        $id_evento->enableSearch();
        $id_fornecedor->enableSearch();

        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Evento')], [$id_evento]);
        $this->form->addFields([new TLabel('Fornecedor')], [$id_fornecedor]);

        /**
         * =====================
         * ABA 2 - Itens da Compra
         * =====================
         */
        $this->form->appendPage('Itens da Compra');

        $descricao_produto = new TEntry('descricao_produto');
        $quantidade        = new TNumeric('quantidade', 2, ',', '.', true);
        $valor_unitario    = new TNumeric('valor_unitario', 2, ',', '.', true);
        $valor_total       = new TNumeric('valor_total', 2, ',', '.', true);

        $valor_total->setEditable(FALSE);

        $descricao_produto->setSize('100%');
        $quantidade->setSize('100%');
        $valor_unitario->setSize('100%');
        $valor_total->setSize('100%');

        // Ações de cálculo automático
        $quantidade->setExitAction(new TAction([$this, 'onCalcularValorItem']));
        $valor_unitario->setExitAction(new TAction([$this, 'onCalcularValorItem']));

        $this->form->addFields([new TLabel('Descrição')], [$descricao_produto]);
        $this->form->addFields([new TLabel('Quantidade')], [$quantidade]);
        $this->form->addFields([new TLabel('Valor Unitário')], [$valor_unitario]);
        $this->form->addFields([new TLabel('Valor Total')], [$valor_total]);

        $btn_add = new TButton('btn_add_item');
        $btn_add->setLabel('Adicionar Item');
        $btn_add->setImage('fa:plus green');
        $btn_add->class = 'btn btn-sm btn-success';
        $btn_add->setAction(new TAction([$this, 'onAddItem']), 'Adicionar Item');

        $this->form->addFields([], [$btn_add]);

        /**
         * =====================
         * DATAGRID DE ITENS
         * =====================
         */
        $this->datagrid_item = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_item->style = 'width: 100%';
        $this->datagrid_item->setHeight(150);

        $col_desc = new TDataGridColumn('descricao_produto', 'Descrição', 'left');
        $col_qtd  = new TDataGridColumn('quantidade', 'Qtd', 'center');
        $col_vu   = new TDataGridColumn('valor_unitario', 'Valor Unit.', 'right');
        $col_vt   = new TDataGridColumn('valor_total', 'Valor Total', 'right');

        $col_vu->setTransformer(function($v) {
            if (is_numeric($v)) {
                return number_format((float) $v, 2, ',', '.');
            }
            return $v !== null && $v !== '' ? $v : '0,00';
        });

        $col_vt->setTransformer(function($v) {
            if (is_numeric($v)) {
                return number_format((float) $v, 2, ',', '.');
            }
            return $v !== null && $v !== '' ? $v : '0,00';
        });


        $this->datagrid_item->addColumn($col_desc);
        $this->datagrid_item->addColumn($col_qtd);
        $this->datagrid_item->addColumn($col_vu);
        $this->datagrid_item->addColumn($col_vt);

        $del_action = new TDataGridAction([$this, 'onDeleteItem']);
        $del_action->setLabel('Remover');
        $del_action->setImage('fa:trash red');
        $del_action->setField('id');
        $this->datagrid_item->addAction($del_action);

        $this->datagrid_item->createModel();

        $this->form->addContent([$this->datagrid_item]);

        /**
         * =====================
         * AÇÕES PRINCIPAIS
         * =====================
         */
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save blue');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['CompraList', 'onClear']), 'fa:list brown');

        /**
         * =====================
         * CONTAINER FINAL
         * =====================
         */
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    public static function onCalcularValorItem($param)
    {
        try {
            $quantidade = isset($param['quantidade']) ? (float) str_replace(',', '.', $param['quantidade']) : 0;
            $valor_unitario = isset($param['valor_unitario']) ? (float) str_replace(',', '.', $param['valor_unitario']) : 0;

            $valor_total = $quantidade * $valor_unitario;

            TForm::sendData('form_compra', (object) ['valor_total' => number_format($valor_total, 2, ',', '.')]);
        }
        catch (Exception $e) {
            new TMessage('error', 'Erro ao calcular valor total: ' . $e->getMessage());
        }
    }

    public function onAddItem($param)
    {
        $data = $this->form->getData();

        if (empty($data->descricao_produto) || empty($data->quantidade) || empty($data->valor_unitario)) {
            new TMessage('warning', 'Preencha todos os campos do item antes de adicionar.');
            return;
        }

        $items = TSession::getValue('itens_compra') ?? [];

        $item = [
            'id'               => uniqid(),
            'descricao_produto'=> $data->descricao_produto,
            'quantidade'       => (float) str_replace(',', '.', $data->quantidade),
            'valor_unitario'   => (float) str_replace(',', '.', $data->valor_unitario),
            'valor_total'      => (float) str_replace(',', '.', $data->valor_total),
        ];

        $items[] = $item;
        TSession::setValue('itens_compra', $items);

        $this->reloadItens();

        $this->clearItemFields();

        $this->form->setData($data);
    }

    public function onDeleteItem($param)
    {
        $items = TSession::getValue('itens_compra') ?? [];
        $new = [];

        foreach ($items as $item) {
            if ($item['id'] != $param['id']) {
                $new[] = $item;
            }
        }

        TSession::setValue('itens_compra', $new);
        $this->reloadItens();
    }

    public function reloadItens()
    {
        $this->datagrid_item->clear();

        $itens = TSession::getValue('itens_compra') ?? [];

        foreach ($itens as $item) {
            $row = new stdClass;
            $row->id = $item['id'];
            $row->descricao_produto = $item['descricao_produto'];

            $quantidade = (float) ($item['quantidade'] ?? 0);
            $valor_unitario = (float) ($item['valor_unitario'] ?? 0);
            $valor_total = (float) ($item['valor_total'] ?? 0);

            $row->quantidade = number_format($quantidade, 2, ',', '.');
            $row->valor_unitario = number_format($valor_unitario, 2, ',', '.');
            $row->valor_total = number_format($valor_total, 2, ',', '.');

            $this->datagrid_item->addItem($row);
        }
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('base');

            $data = $this->form->getData();
            $this->form->validate();

            $compra = new Compra;
            $compra->fromArray((array) $data);
            $compra->store();

            $items = TSession::getValue('itens_compra') ?? [];

            foreach ($items as $itemData) {
                $item = new CompraItem;
                $item->id_compra = $compra->id;
                $item->descricao_produto = $itemData['descricao_produto'];
                $item->quantidade = $itemData['quantidade'];
                $item->valor_unitario = $itemData['valor_unitario'];
                $item->valor_total = $itemData['valor_total'];
                $item->store();
            }

            TTransaction::close();

            new TMessage('info', 'Compra e itens registrados com sucesso!');
            $this->reloadItens();

            $this->form->setData($data);
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param)
{
    try {
        if (isset($param['key'])) {
            $key = $param['key'];

            TTransaction::open('base');

            $compra = new Compra($key);
            $this->form->setData($compra);

            $repository = new TRepository('CompraItem');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('id_compra', '=', $compra->id));

            $itens = $repository->load($criteria);

            $lista_itens = [];
            if ($itens) {
                foreach ($itens as $item) {
                    $lista_itens[] = [
                        'id'               => $item->id,
                        'descricao_produto'=> $item->descricao_produto,
                        'quantidade'       => (float) $item->quantidade,
                        'valor_unitario'   => (float) $item->valor_unitario,
                        'valor_total'      => (float) $item->valor_total
                    ];
                }
            }

            TSession::setValue('itens_compra', $lista_itens);
            $this->reloadItens();

            TTransaction::close();
        } else {
            $this->form->clear();
        }
    } catch (Exception $e) {
        new TMessage('error', $e->getMessage());
        TTransaction::rollback();
    }
}


    public function clearItemFields()
    {
        $clear = new stdClass;
        $clear->descricao_produto = '';
        $clear->quantidade = '';
        $clear->valor_unitario = '';
        $clear->valor_total = '';

        TForm::sendData('form_compra', $clear);
    }


    public function onClear()
    {
        $this->form->clear();
        TSession::setValue('itens_compra', []);
        $this->datagrid_item->clear();
    }

    public function show()
    {
        parent::show();
        $this->reloadItens();
    }
}
