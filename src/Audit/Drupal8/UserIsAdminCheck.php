<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\Annotation\Param;

/**
 * Audit to check if user and roles have the is_admin property associated to them.
 */
class UserIsAdminCheck extends Audit {

  /**
   * Return a generated user role object from a user config object.
   *
   * @param array $users
   *   The user configuration object.
   *
   * @return array
   *   An array of roles and users.
   */
  private function userObject($users = []) {
    $results = array();
    foreach ($users as $userKey => $user) {
      foreach($user['roles'] as $role) {
        $results[$role] = $user;
      }
    }
    return $results;
  }

  /**
   * @inheritdoc
   */
  public function audit(Sandbox $sandbox) {

    // Parameters.
    $rolesToFind = $sandbox->getParameter('roles', array("administrator"));

    // Create an empty array for users and results.
    $users = array();
    $results = array();

    // Get all user IDs.
    $uids = $sandbox->drush()->sqlQuery('SELECT (uid) FROM users;');
    foreach ($uids as $uid) {
      $users[$uid] = $uid;
    }

    // Get all user information.
    foreach ($users as $key => $user) {
      $userData = $sandbox->drush(['format' => 'json'])->userInformation("--uid={$user}");
      $users[$key] = $userData[count($userData)];
    }

    // Generate results
    $users = $this->userObject($users);
    foreach ($users as $user) {
      if (isset($user['is_admin'])) {
        if ((bool) $user['is_admin'] === TRUE) {
          $results[] = "User {$user['name']} is not allowed to be an administrator.";
        }
      }
    }

    // Get all user roles.
    $roles = $sandbox->drush(['format' => 'json'])->rls();
    foreach ($roles as $role) {
      if (isset($role['is_admin'])) {
        if ((bool) $role['is_admin'] === TRUE) {
          $results[] = "Role {$role['label']} is not allowed to be an administrator.";
        }
      }
    }

    if (!empty($results)) {
      $sandbox->setParameter('results', $results);
      return FALSE;
    }

    return TRUE;

  }

}
