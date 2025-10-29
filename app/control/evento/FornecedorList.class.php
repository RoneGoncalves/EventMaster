<?php
/**
 * FornecedorList
 */
class FornecedorList extends TStandardList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        parent::setDatabase('base');
        parent::setActiveRecord('Fornecedor');
        parent::setDefaultOrder('id', 'desc');
        parent::addFilterField('nome', 'like', 'nome');

        // form de busca
        $this->form = new BootstrapFormBuilder('form_search_Fornecedor');
        $this->form->setFormTitle('Fornecedores');

        $nome = new TEntry('nome');
        $this->form->addFields([new TLabel('Nome')], [$nome]);
        $this->form->setData(TSession::getValue('Fornecedor_filter_data'));

        $btn = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $this->form->addActionLink('Adicionar Fornecedor', new TAction(['FornecedorForm', 'onClear']), 'fa:plus green');

        // datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // colunas
        $col_id       = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_nome     = new TDataGridColumn('nome', 'Nome', 'left', '30%');
        $col_cpfCnpj  = new TDataGridColumn('cpfCnpj', 'CPF/CNPJ', 'center', '20%');
        $col_email    = new TDataGridColumn('email', 'E-mail', 'left', '25%');
        $col_whatsapp = new TDataGridColumn('whatsapp', 'WhatsApp', 'center', '15%');

        // adiciona colunas
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_cpfCnpj);
        $this->datagrid->addColumn($col_email);
        $this->datagrid->addColumn($col_whatsapp);

        // ações
        $action_edit = new TDataGridAction(['FornecedorForm', 'onEdit'], ['id' => '{id}']);
        $action_edit->setLabel('Editar');
        $action_edit->setImage('far:edit blue');
        $this->datagrid->addAction($action_edit);

        $action_del = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);
        $action_del->setLabel('Excluir');
        $action_del->setImage('far:trash-alt red');
        $this->datagrid->addAction($action_del);

        $this->datagrid->createModel();

        // navegação
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

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
        TSession::setValue('Fornecedor_filter_data', NULL);
    }
}
