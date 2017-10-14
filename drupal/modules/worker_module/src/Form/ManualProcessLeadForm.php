<?php

namespace Drupal\worker_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\SuspendQueueException;

use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class Lead Form
 * @package Drupal\worker_module\Form
 */
class ManualProcessLeadForm extends FormBase {
    
    
  /**
   * @var QueueFactory
   */
  protected $queue_factory;
  
    /**
     * @var QueueWorkerManagerInterface
     */
    protected $queue_manager;
  
  
    /**
     * {@inheritdoc}
     */
    public function __construct(QueueFactory $queue_factory, QueueWorkerManagerInterface $queue_manager) {
      $this->queue_factory = $queue_factory;
      $this->queue_manager = $queue_manager;
    }
  
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('queue'),
        $container->get('plugin.manager.queue_worker')
      );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'admin_awesome_lead_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state){
        $queue = $this->queue_factory->get('lead_processor');
        $form['help'] = [
            '#type' => 'markup',
            '#markup' => $this->t('Submitting this form will process the a Manual Queue which containes @number items.',
             [ '@number' => $queue->numberOfItems() ]
            )
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Process queue'),
            '#button_type' => 'primary',
            '#disabled' => !$queue->numberOfItems()
            
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state){}

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $queue = $this->queue_factory->get('lead_processor');
        $queue_worker = $this->queue_manager->createInstance('lead_manual_processor');

        while ($item = $queue->claimItem()) {
            try {
                $queue_worker->processItem($item->data);
                $queue->deleteItem($item);
            } catch(SuspendQueueException $e) {
                $queue->releaseItem($item);
                break;
            } catch (\Exception $e) {
                watchdow_exception('worker_module', $e);
            }
        }
        
    }
}