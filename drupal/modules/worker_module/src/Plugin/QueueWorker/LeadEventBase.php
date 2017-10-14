<?php

namespace Drupal\worker_module\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Mail\MailManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\worker_module\Model\LeadModel;
use Drupal\webprofiler\Mail\MailManagerWrapper;
/**
 * {@inheritdoc}
 */
class LeadEventBase extends QueueWorkerBase implements ContainerFactoryPluginInterface {

    /**
     * @var Drupal\webprofiler\Mail\MailManagerWrapper
     */
    protected $mail;

    /**
     * {@inheritdoc}
     */
    function __construct(MailManagerWrapper $mail){
        $this->mail = $mail;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $config, $plugin_id, $plugin_definition){
        return new static($container->get('plugin.manager.mail'));
    }

  /**
   * Works on a single queue item.
   *
   * @param mixed $data
   *   The data that was passed to
   *   \Drupal\Core\Queue\QueueInterface::createItem() when the item was queued.
   *
   * @throws \Drupal\Core\Queue\RequeueException
   *   Processing is not yet finished. This will allow another process to claim
   *   the item immediately.
   * @throws \Exception
   *   A QueueWorker plugin may throw an exception to indicate there was a
   *   problem. The cron process will log the exception, and leave the item in
   *   the queue to be processed again later.
   * @throws \Drupal\Core\Queue\SuspendQueueException
   *   More specifically, a SuspendQueueException should be thrown when a
   *   QueueWorker plugin is aware that the problem will affect all subsequent
   *   workers of its queue. For example, a callback that makes HTTP requests
   *   may find that the remote server is not responding. The cron process will
   *   behave as with a normal Exception, and in addition will not attempt to
   *   process further items from the current item's queue during the current
   *   cron run.
   *
   * @see \Drupal\Core\Cron::processQueues()
   */
    public function processItem($data) {
        // $param['subject'] = t('This is an example');
        // $param['message'] = $data->inquiry;
        // $param['from'] =  $to = \Drupal::config('system.site')->get('mail');
        // $param['to'] = $data->email;
        // $param['username'] = $data->username;
        // $this->mail->mail('worker_module', 'lead_mail', $data->email, 'en', $param, NULL, true);        
        sleep(5);
        error_log("Item processed");
    }

}
