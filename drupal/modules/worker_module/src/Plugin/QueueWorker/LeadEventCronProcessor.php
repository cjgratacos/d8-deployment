<?php

namespace Drupal\worker_module\Plugin\QueueWorker;

/**
 * @QueueWorker(
 *  id = "lead_processor",
 *  title = @Translation("my custom lead queue processor"),
 *  cron = { "time" = 260 }
 * )
 */
class LeadEventCronProcessor extends LeadEventBase {

}