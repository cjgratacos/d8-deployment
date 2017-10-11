<?php

namespace Drupal\worker_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\worker_module\Model\LeadForm;

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
    public function buildForm(array &$form, FormStateInterface $form_state){
        $form['name'] = [
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
    public function validateForm(array &$form, FormStateInterface $form_state){
        /** @var Drupal\Core\Queue\QueueFactory $queue_factory */
        $queue_factory = \Drupal::service('queue');
        /** @var Drupal\Core\Queue\QueueInterface $queue */
        $queue = $queue_factory->get('email_processor');
        $item = LeadForm::create()
                    ->withId($form_state->getValue('username'))
                    ->withEmail($form_state->getValue('email'))
                    ->withBody($form_state->getValue('inquiry'));
        $queue->createItem($item);
    }
}