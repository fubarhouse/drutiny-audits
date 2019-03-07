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
 *  description = "Machine name of module to check that is disabled.",
 *  type = "string",
 * )
 */
class ModuleDisabled extends Audit implements RemediableInterface {

  /**
   * @inheritdoc
   */
  public function audit(Sandbox $sandbox) {
    $module = $sandbox->getParameter('module');
    try {
      // If the specified module is not enabled:
      if (!$sandbox->drush()->moduleEnabled($module)) {
        // Check the list of modules to see if it's there:
        $moduleList = $sandbox->drush()->pmList();
        if ((bool) strpos($moduleList, "({$module})") !== TRUE) {
          // If it's not in the list and it's not relevant:
          return Audit::IRRELEVANT;
        }
        else {
          // If it's in the list and it's not enabled, we have our desired state.
          return TRUE;
        }

      }
      else {
        // It is enabled, which is not expected.
        throw new \Exception($module);
      }
    }
    catch (\Exception $e) {
      return FALSE;
    }

    // This should be unreachable, assume fail if it is reached.
    return FALSE;
  }

  /**
   * @inheritdoc
   */
  public function remediate(Sandbox $sandbox) {
    $module = $sandbox->getParameter('module');
    $sandbox->drush()->pmu($module, '-y');
    return $this->audit($sandbox);
  }

}
