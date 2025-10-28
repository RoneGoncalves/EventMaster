<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventoList
 *
 * @author Ronaldo
 */
class EventoList extends Adianti\Base\TStandardList{
    //put your code here
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('base');            // defines the database
        parent::setActiveRecord('Evento');   // defines the active record
        parent::setDefaultOrder('id', 'desc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('nome', 'LIKE', 'nome'); // filterField, operator, formField
        
         // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Evento');  
        $this->form->setFormTitle('Eventos');
        
        $evento_nome = new TEntry('nome');
        
        
        $this->form->addFields([new TLabel('Evento', '11')],[$evento_nome], []);
        
        $this->form->setData( TSession::getValue('Evento_filter_data') );
        
        
        $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Adicionar Evento',  new TAction(array('EventoForm', 'onClear')), 'fa:plus green');

        // $this->form->addActionLink(_t('New'),  new TAction(array('EventoForm', 'onEdit')), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        //$this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center', 10);
        $column_nome = new TDataGridColumn('nome', 'Nome', 'center', 10);
        $column_data_evento = new TDataGridColumn('data_evento', 'Data', 'center', 10);
        $column_horario = new TDataGridColumn('horario', 'Horário', 'center', 20);
        $column_prev_orcamento = new TDataGridColumn('prev_orcamento', 'Orçamento Previsto', 'center', 20);
        $column_est_publico = new TDataGridColumn('est_publico', 'Público Estimado', 'center', 20);
        $column_publico_efetivo = new TDataGridColumn('publico_efetivo', 'Público Efetivo', 'center', 10);
        $column_valor_convite = new TDataGridColumn('valor_convite', 'Valor Convite', 'center', 10);

        $column_id->enableAutoHide(500);

        $moneyFormat = fn($v) => number_format($v, 2, ',', '.');
        $column_prev_orcamento->setTransformer($moneyFormat);
        $column_valor_convite->setTransformer($moneyFormat);
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_data_evento);
        $this->datagrid->addColumn($column_horario);
        $this->datagrid->addColumn( $column_prev_orcamento);
        $this->datagrid->addColumn($column_est_publico);
        $this->datagrid->addColumn($column_publico_efetivo);
        $this->datagrid->addColumn($column_valor_convite);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('EventoForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue ');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red ');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style='overflow-x:auto';
        $panel->addFooter($this->pageNavigation);
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    public function onClear()
    {
        $obj = new stdClass();
       
        $this->form->clear();   
       
        TSession::setValue('Papeleta_filter_id_sub_alinea',   NULL);
        $this->form->setData($obj);
    }
    
}