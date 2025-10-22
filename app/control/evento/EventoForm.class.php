<?php

/**
 * EventoForm Form
 * @author  <your name here>
 */
class EventoForm extends TPage
{
    protected $form; // form

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_Evento');
        // define the form title
        $this->form->setFormTitle('Cadastro de Eventos');
        
        TSession::setValue('form_name','form_Evento');
        
        $jquery = FuncApp::Stransp();
        TPage::include_js($jquery);  

        $this->form->appendPage('Informações do Evento');

        // create the form fields
        $id               = new TEntry('id');
        $nome             = new TEntry('nome');
        $data_evento      = new TDate('data_evento');
        $horario          = new TEntry('horario');
        $prev_orcamento   = new TEntry('prev_orcamento');
        $est_publico      = new TEntry('est_publico');
        $publico_efetivo  = new TEntry('publico_efetivo');
        $valor_convite    = new TEntry('valor_convite');
        $descricao        = new TText('descricao');

        $id->setEditable(FALSE);
        $data_evento->setMask('dd/mm/yyyy');

        // add the fields to the form
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('Data do Evento') ], [ $data_evento ] );
        $this->form->addFields( [ new TLabel('Horário') ], [ $horario ] );
        $this->form->addFields( [ new TLabel('Previsão de Orçamento') ], [ $prev_orcamento ] );
        $this->form->addFields( [ new TLabel('Estimativa de Público') ], [ $est_publico ] );
        $this->form->addFields( [ new TLabel('Público Efetivo') ], [ $publico_efetivo ] );
        $this->form->addFields( [ new TLabel('Valor do Convite') ], [ $valor_convite ] );
        $this->form->addFields( [ new TLabel('Descrição') ], [ $descricao ] );      
        $descricao->setSize('100%', 80);
        $prev_orcamento->setNumericMask(2, ',', '.');

        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser red');   
    }


}     
        