<?php
class LocacaoList extends Adianti\Base\TStandardList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        parent::setDatabase('base');
        parent::setActiveRecord('Locacao');
        parent::setDefaultOrder('id', 'asc');
        parent::addFilterField('nome', 'LIKE', 'nome');

        // === Formulário de busca ===
        $this->form = new BootstrapFormBuilder('form_search_Locacao');
        $this->form->setFormTitle('Locações');

        $nome = new TEntry('nome');
        $this->form->addFields([new TLabel('Locação', '11')], [$nome], []);

        $this->form->setData(TSession::getValue('Locacao_filter_data'));

        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addActionLink('Adicionar Locação', new TAction(['LocacaoForm', 'onClear']), 'fa:plus green');

        // === DataGrid ===
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // === Colunas ===
        $column_id = new TDataGridColumn('id', 'ID', 'center', 10);
        $column_evento = new TDataGridColumn('evento->nome', 'Evento', 'center', 20);
        $column_nome = new TDataGridColumn('nome', 'Nome', 'center', 20);
        $column_cidade = new TDataGridColumn('cidade', 'Cidade', 'center', 20);
        $column_valor = new TDataGridColumn('valor', 'Valor', 'center', 20);

        $moneyFormat = fn($v) => number_format($v, 2, ',', '.');
        $column_valor->setTransformer($moneyFormat);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_evento);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_valor);

        // === Ações ===
        $action_edit = new TDataGridAction(['LocacaoForm', 'onEdit']);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);

        $action_del = new TDataGridAction([$this, 'onDelete']);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);

        $this->datagrid->createModel();

        // === Navegação ===
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // === Layout ===
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
        TSession::setValue('Papeleta_filter_id_sub_alinea', NULL);
    }
}
