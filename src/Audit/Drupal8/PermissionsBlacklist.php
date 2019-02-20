<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\Annotation\Param;

/**
 * BlackList Permissions
 * @Param(
 *  name = "roles",
 *  description = "An array of machine names associated to each role for validation",
 *  type = "array"
 * )
 * @Param(
 *  name = "permissions",
 *  description = "An array of permissions to ensure are not available to non-administrator roles",
 *  type = "array"
 * )
 */
class BlacklistPermissions extends Audit {

  /**
   *
   */
  public function audit(Sandbox $sandbox) {
    return FALSE;
  }

}
