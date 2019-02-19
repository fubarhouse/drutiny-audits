<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\RemediableInterface;
use Drutiny\Annotation\Param;

/**
 * Generic modules uninstalled check.
 * @Param(
 *  name = "modules",
 *  description = "List of modules to check that are uninstalled.",
 *  type = "array",
 * )
 */
class ModulesUninstalled extends Audit implements RemediableInterface {

  /**
   * @inheritdoc
   */
  public function audit(Sandbox $sandbox) {
    $modules = $sandbox->getParameter('modules');
    if (empty($modules)) {
      return TRUE;
    }

    $uninstalled = [];
    foreach ($modules as $moduleName) {

        $notUninstalled[] = $moduleName;

    }
    if (!empty($notUninstalled)) {
      $sandbox->setParameter('notUninstalled', $notUninstalled);
      return FALSE;
    }
    // Seems like the best way to comma separate things.
    else {
      $sandbox->setParameter('uninstalled', '`' . implode('`, `', $modules) . '`');
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
