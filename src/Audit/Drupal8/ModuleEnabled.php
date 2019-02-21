<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\RemediableInterface;
use Drutiny\Annotation\Param;

/**
 * Generic modules are enabled check.
 * @Param(
 *  name = "module",
 *  description = "Machine name of module to check that is enabled.",
 *  type = "string",
 * )
 */
class ModuleEnabled extends Audit implements RemediableInterface {

  /**
   * @inheritdoc
   */
  public function audit(Sandbox $sandbox) {
    $module = $sandbox->getParameter('module');
    try {
      if (!$sandbox->drush()->moduleEnabled($module)) {
        throw new \Exception($module);
        return FALSE;
      }
    }
    catch (\Exception $e) {
      return TRUE;
    }

    // This should be unreachable, assume fail if it is reached.
    return FALSE;
  }

  /**
   * @inheritdoc
   */
  public function remediate(Sandbox $sandbox) {
    $modules = $sandbox->getParameter('module');
    $sandbox->drush()->en($module, '-y');
    return $this->audit($sandbox);
  }

}
