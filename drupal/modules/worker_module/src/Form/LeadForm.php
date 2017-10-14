<?php

namespace Drupal\worker_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;

/**
 * Class Lead Form
 * @package Drupal\worker_module\Form
 */
class LeadForm extends FormBase {
    
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'awesome_lead_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state){
        $form['username'] = [
            '#type' => 'textfield',
            '#attributes' => [
                'placeholder' => 'Username'
            ],
            '#required' => 'true'
        ];
        $form['email'] = [
            '#type' => 'email',
            '#attributes' => [
                'placeholder' => 'Email'
            ],
            '#required' => 'true'
        ];
        $form['inquiry'] = [
            '#type' => 'textarea',
            '#attributes' => [
                'placeholder' => 'Upto 200 characters allowed'
            ],
            '#required' => 'true'
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => 'Submit'
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state){}

    public function submitForm(array &$form, FormStateInterface $form_state) {
        /** @var Drupal\Core\Queue\QueueFactory $queue_factory */
        $queue_factory = \Drupal::service('queue');
        /** @var Drupal\Core\Queue\QueueInterface $queue */
        $queue = $queue_factory->get('lead_processor');
        $item = new \StdClass();
        $item->username = $form_state->getValue('username');
        $item->email = $form_state->getValue('email');
        $item->attempts = 0;
        $item->inquiry = $form_state->getValue('inquiry');


        for($i=0;$i<2000;$i++){
            $queue->createItem($item);
        }
    }
}