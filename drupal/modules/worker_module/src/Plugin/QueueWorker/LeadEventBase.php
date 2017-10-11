<?php

namespace Drupal\worker_module\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Mail\MailManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\worker_module\Model\LeadModel;

/**
 * {@inheritdoc}
 */
class LeadEventBase extends QueueWorkerBase implements ContainerFactoryPluginInterface {

    /**
     * @var Drupal\Core\Mail\MailManager
     */
    protected $mail;

    /**
     * {@inheritdoc}
     */
    function __construct(MailManager $mail){
        $this->mail = $mail;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $config, $plugin_id, $plugin_definition){
        return new static($container->get('plugin.manager.mail'));
    }

    public function processItem(LeadModel $data) {
        $data->getSalesforceData();
    }

}
