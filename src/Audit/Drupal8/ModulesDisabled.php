<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\RemediableInterface;
use Drutiny\Annotation\Param;

/**
 * Generic modules disabled check.
 * @Param(
 *  name = "modules",
 *  description = "List of modules to check that are disabled.",
 *  type = "array",
 * )
 */
class ModulesDisabled extends Audit implements RemediableInterface {

  /**
   * @inheritdoc
   */
  public function audit(Sandbox $sandbox) {
    $modules = $sandbox->getParameter('modules');
    if (empty($modules)) {
      return TRUE;
    }

    $disabled = [];
    foreach ($modules as $moduleName) {
      try {
        if ($sandbox->drush()->moduleEnabled($moduleName)) {
          throw new \Exception($moduleName);
        }
      }
      catch (\Exception $e) {
        $notDisabled[] = $moduleName;
      }
    }
    if (!empty($notDisabled)) {
      $sandbox->setParameter('notDisabled', $notDisabled);
      return FALSE;
    }
    // Seems like the best way to comma separate things.
    else {
      $sandbox->setParameter('disabled', '`' . implode('`, `', $modules) . '`');
    }

    return TRUE;
  }

  /**
   * @inheritdoc
   */
  public function remediate(Sandbox $sandbox) {
    $modules = $sandbox->getParameter('modules');
    $sandbox->drush()->pmu(implode(' ', $modules), '-y');
    return $this->audit($sandbox);
  }

}
