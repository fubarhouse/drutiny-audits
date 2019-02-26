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
    foreach ($roles as $role) {
      $config = $sandbox->drush(['format' => 'json'])->configGet("user.role.{$role}");
      foreach ($config['permissions'] as $permission) {
        foreach ($perms as $perm) {
          if ($perm === $permission) {
            $blacklistedPermissions[] = $permission;
          }
        }
      }
    }

    if (empty($blacklistedPermissions)) {
      return TRUE;
    }

    $sandbox->setParameter('blacklistedPermissions', $blacklistedPermissions);
    return FALSE;
  }

}
