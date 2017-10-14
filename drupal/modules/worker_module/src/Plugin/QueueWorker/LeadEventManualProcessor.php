<?php

namespace Drupal\worker_module\Plugin\QueueWorker;

/**
 * @QueueWorker(
 *  id = "lead_manual_processor",
 *  title = @Translation("my custom lead queue manual processor")
 * )
 */
class LeadEventManualProcessor extends LeadEventBase {

}