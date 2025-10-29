<?php

/**
 * ArtistaList
 * Lista e gerencia artistas
 * @author Ronaldo
 */
class ArtistaList extends Adianti\Base\TStandardList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        parent::setDatabase('base');
        parent::setActiveRecord('\\Artista'); // <-- importante usar barra dupla
        parent::setDefaultOrder('id', 'desc');
        parent::addFilterField('nome', 'LIKE', 'nome');

        // Formulário de busca
        $this->form = new BootstrapFormBuilder('form_search_artista');
        $this->form->setFormTitle('Artistas');

        $nome = new TEntry('nome');
        $this->form->addFields([new TLabel('Nome')], [$nome]);

        $this->form->setData(TSession::getValue('Artista_filter_data'));

        $btnFind = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btnFind->class = 'btn btn-sm btn-primary';

        $this->form->addActionLink('Adicionar Artista', new TAction(['ArtistaForm', 'onClear']), 'fa:plus green');

        // Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // Colunas
        $column_id            = new TDataGridColumn('id', 'ID', 'center', 10);
        $column_nome          = new TDataGridColumn('nome', 'Nome', 'center', 150);
        $column_email         = new TDataGridColumn('email', 'Email', 'center', 150);
        $column_whatsapp      = new TDataGridColumn('whatsapp', 'WhatsApp', 'center', 100);
        $column_cidade_origem = new TDataGridColumn('cidade_origem', 'Cidade de Origem', 'center', 100);

        // Adiciona colunas
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_whatsapp);
        $this->datagrid->addColumn($column_cidade_origem);

        // Ações
        $action_edit = new TDataGridAction(['ArtistaForm', 'onEdit'], ['id' => '{id}']);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue');
        $this->datagrid->addAction($action_edit);

        $action_del = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red');
        $this->datagrid->addAction($action_del);

        $this->datagrid->createModel();

        // Paginação
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // Painel
        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $panel->addFooter($this->pageNavigation);

        // Container principal
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
        TSession::setValue('Artista_filter_data', NULL);
    }
}
