<?php
/**
 * CompraList
 * @author Ronaldo
 */
class CompraList extends Adianti\Base\TStandardList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        parent::setDatabase('base');
        parent::setActiveRecord('Compra');
        parent::setDefaultOrder('id', 'desc');

        // Campos usados para filtros
        parent::addFilterField('id_evento', '=', 'id_evento');
        parent::addFilterField('id_fornecedor', '=', 'id_fornecedor');

        // ===== FORMULÁRIO DE BUSCA =====
        $this->form = new BootstrapFormBuilder('form_search_compra');
        $this->form->setFormTitle('Compras');

        // Campo evento (combobox ligado ao banco)
        $evento = new TDBCombo('id_evento', 'base', 'Evento', 'id', 'nome');
        $evento->enableSearch(); // ativa busca no select2
        $evento->setSize('100%');

        // Campo fornecedor (combobox ligado ao banco)
        $fornecedor = new TDBCombo('id_fornecedor', 'base', 'Fornecedor', 'id', 'nome');
        $fornecedor->enableSearch();
        $fornecedor->setSize('100%');

        // Adiciona campos ao formulário
        $this->form->addFields([new TLabel('Evento')], [$evento]);
        $this->form->addFields([new TLabel('Fornecedor')], [$fornecedor]);

        // Mantém dados entre requisições
        $this->form->setData(TSession::getValue('Compra_filter_data'));

        // Botões
        $findBtn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $findBtn->class = 'btn btn-sm btn-primary';

        $this->form->addActionLink('Registrar Compra', new TAction(['CompraForm', 'onClear']), 'fa:plus green');

        // ===== DATAGRID =====
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // Colunas
        $col_id         = new TDataGridColumn('id', 'ID', 'center', 10);
        $col_evento     = new TDataGridColumn('evento->nome', 'Evento', 'center', 20);
        $col_fornecedor = new TDataGridColumn('fornecedor->nome', 'Fornecedor', 'center', 20);

        // Adiciona colunas
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_evento);
        $this->datagrid->addColumn($col_fornecedor);

        // Ações
        $edit_action = new TDataGridAction(['CompraForm', 'onEdit']);
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

        // ===== MONTAGEM FINAL =====
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
        TSession::setValue('Compra_filter_data', NULL);
    }
}
