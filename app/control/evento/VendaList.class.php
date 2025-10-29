<?php
/**
 * VendaList
 * @author Ronaldo
 */
class VendaList extends Adianti\Base\TStandardList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        // Configurações básicas
        parent::setDatabase('base');
        parent::setActiveRecord('Venda');
        parent::setDefaultOrder('id', 'desc');

        // Campos usados nos filtros
        parent::addFilterField('id_cliente', '=', 'id_cliente');
        parent::addFilterField('id_evento', '=', 'id_evento');

        // ===== FORMULÁRIO DE BUSCA =====
        $this->form = new BootstrapFormBuilder('form_search_venda');
        $this->form->setFormTitle('Vendas');

        // Campo cliente
        $cliente = new TDBCombo('id_cliente', 'base', 'Cliente', 'id', 'nome');
        $cliente->enableSearch(); // ativa select2
        $cliente->setSize('100%');

        // Campo evento
        $evento = new TDBCombo('id_evento', 'base', 'Evento', 'id', 'nome');
        $evento->enableSearch();
        $evento->setSize('100%');

        // Adiciona campos ao formulário
        $this->form->addFields([new TLabel('Cliente')], [$cliente]);
        $this->form->addFields([new TLabel('Evento')], [$evento]);

        // Mantém dados entre buscas
        $this->form->setData(TSession::getValue('Venda_filter_data'));

        // Botões
        $findBtn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $findBtn->class = 'btn btn-sm btn-primary';

        $this->form->addActionLink('Registrar Venda', new TAction(['VendaForm', 'onClear']), 'fa:plus green');

        // ===== DATAGRID =====
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // Colunas
        $col_id         = new TDataGridColumn('id', 'ID', 'center', 10);
        $col_evento     = new TDataGridColumn('evento->nome', 'Evento', 'center', 20);
        $col_cliente    = new TDataGridColumn('cliente->nome', 'Cliente', 'center', 20);
        $col_produto    = new TDataGridColumn('produto', 'Produto', 'center', 20);
        $col_quantidade = new TDataGridColumn('quantidade', 'Qtd', 'center', 10);
        $col_valor      = new TDataGridColumn('valor', 'Valor', 'center', 15);

        // Formata valor
        $col_valor->setTransformer(fn($v) => number_format($v, 2, ',', '.'));

        // Adiciona colunas
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_evento);
        $this->datagrid->addColumn($col_cliente);
        $this->datagrid->addColumn($col_produto);
        $this->datagrid->addColumn($col_quantidade);
        $this->datagrid->addColumn($col_valor);

        // Ações
        $edit_action = new TDataGridAction(['VendaForm', 'onEdit']);
        $edit_action->setLabel(_t('Edit'));
        $edit_action->setImage('far:edit blue');
        $edit_action->setField('id');

        $del_action = new TDataGridAction([$this, 'onDelete']);
        $del_action->setLabel(_t('Delete'));
        $del_action->setImage('far:trash-alt red');
        $del_action->setField('id');

        $this->datagrid->addAction($edit_action);
        $this->datagrid->addAction($del_action);

        $this->datagrid->createModel();

        // ===== PAGINAÇÃO =====
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // ===== CONTAINER FINAL =====
        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $panel->addFooter($this->pageNavigation);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    }

    public function onClear()
    {
        $this->form->clear();
        TSession::setValue('Venda_filter_data', NULL);
    }
}
