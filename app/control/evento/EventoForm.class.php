<?php

use Adianti\Widget\Form\TText;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TDate;

/**
 * EventoForm
 *
 * @version    1.0
 * @package    model
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */

/**
 * EventoForm Form
 * @author  <Ronaldo Gonçalves>
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

        $this->form = new BootstrapFormBuilder('form_evento');
        // define the form title
        $this->form->setFormTitle('Cadastro de Eventos');
        
        TSession::setValue('form_name','form_Evento');
        
        // $jquery = FuncApp::Stransp();
        // TPage::include_js($jquery);  

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

        $data_evento->setMask('dd/mm/yyyy');


        $id->setEditable(FALSE);
        
        $horario->setMask('99:99');

        // add the fields to the form
        $this->form->addFields( [ new TLabel('ID', '12') ], [ $id ],[],[] );
        $this->form->addFields( [ new TLabel('Nome do Evento', '12') ], [ $nome ],[],[] );
        $this->form->addFields( [ new TLabel('Data do Evento', '12') ], [ $data_evento ], [],[] );
        $this->form->addFields( [ new TLabel('Horário', '12') ], [ $horario ],[],[] );
        $this->form->addFields( [ new TLabel('Previsão de Orçamento', '12') ], [ $prev_orcamento ],[],[] );
        $this->form->addFields( [ new TLabel('Estimativa de Público', '12') ], [ $est_publico ],[],[] );
        $this->form->addFields( [ new TLabel('Público Efetivo', '12') ], [ $publico_efetivo ],[],[] );
        $this->form->addFields( [ new TLabel('Valor do Convite', '12') ], [ $valor_convite ],[],[] );
        $this->form->addFields( [ new TLabel('Descrição', '12') ], [ $descricao ],[],[] );      
        $descricao->setSize('100%', 80);
        $prev_orcamento->setNumericMask(2, ',', '.');
        $valor_convite->setNumericMask(2, ',', '.');

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'),  new TAction(array($this, 'onClear')), 'fa:eraser red');
        $this->form->addAction(_t('Back'), new TAction(array('EventoList', 'onClear')), 'fa:list-alt brown' );
                        
        $this->formFields = array($id,$nome,$data_evento,$horario,$prev_orcamento,$est_publico,$publico_efetivo,$valor_convite,$descricao);     

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onSave( $param )
    {
        try 
        {
            TTransaction::open('base'); // open a transaction

            $data = $this->form->getData(); // get form data as array
            $data->data_evento = TDate::date2us($data->data_evento); // convert to US date format

            $data->prev_orcamento = str_replace(['.', ','], ['', '.'], $data->prev_orcamento);
            $data->valor_convite  = str_replace(['.', ','], ['', '.'], $data->valor_convite);


            $this->form->validate(); // validate form data
                        
            $object = new Evento;  // create an empty object

            $object->fromArray( (array) $data); // load the object with data
            
            $object->store();

            new TMessage('info', 'Cadastro realizado com sucesso!');

            //var_dump($object);
            TTransaction::close(); // close the transaction
        }

        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onClear( $param )
    {
        $this->form->clear();

    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('base'); // open a transaction

                $object = new Evento($key); // instantiates the Active Record
                $object->data_evento = TDate::date2br($object->data_evento);
                $object->prev_orcamento = number_format($object->prev_orcamento, 2, ',', '.');
                $object->valor_convite  = number_format($object->valor_convite, 2, ',', '.');

                $this->form->setData( $object );

                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }


}     
        