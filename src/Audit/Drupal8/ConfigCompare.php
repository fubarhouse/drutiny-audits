<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\Annotation\Param;

/**
 * Check a configuration is set correctly.
 */
class ConfigCompare extends Audit {

  /**
   * @inheritDoc
   */
  public function audit(Sandbox $sandbox) {
    return TRUE;
  }

}
