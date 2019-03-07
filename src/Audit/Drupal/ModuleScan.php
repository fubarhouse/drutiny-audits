<?php

namespace Drutiny\Fubarhouse\Audit\Drupal;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;

class ModuleScan extends Audit {

  /**
   * @inheritdoc
   */
  public function audit(Sandbox $sandbox) {
    return TRUE;
  }

}
