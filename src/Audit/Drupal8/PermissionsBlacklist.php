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
class PermissionsBlacklist extends Audit {

  /**
   *
   */
  public function audit(Sandbox $sandbox) {
    $perms = $sandbox->getParameter('permissions');
    $roles = $sandbox->getParameter('roles');
    $affectedRoles = array();
    foreach ($roles as $role) {
      try {
        $config = $sandbox->drush(['format' => 'json'])->configGet("user.role.{$role}");
      }
      catch (\Exception $e) {
        // If the configuration object could not be found, ignore the step.
        continue;
      }
      finally {
        if (isset($config)) {
          foreach ($config['permissions'] as $permission) {
            foreach ($perms as $perm) {
              if ($perm === $permission) {
                $blacklistedPermissions[] = $permission;
                if (!in_array($role, $affectedRoles)) {
                  $affectedRoles[] = $role;
                }
              }
            }
          }
        }
        // Cleanup to prevent memory leaks.
        unset($config);
      }
    }

    if (empty($blacklistedPermissions)) {
      return TRUE;
    }

    if (!empty($affectedRoles)) {
      $sandbox->setParameter('affectedRoles', $affectedRoles);
    }

    $sandbox->setParameter('blacklistedPermissions', $blacklistedPermissions);
    return FALSE;
  }

}
