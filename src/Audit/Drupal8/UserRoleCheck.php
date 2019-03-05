<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\RemediableInterface;
use Drutiny\Annotation\Param;

/**
 * Audit to check ig user role has users associated to it.
 * @Param(
 *  name = "roles",
 *  description = "The machine name of the user role",
 * )
 */
class UserRoleCheck extends Audit {

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
    foreach ($users as $userKey => $user) {
      foreach($user['roles'] as $role) {
        foreach ($rolesToFind as $roleToFind) {
          if ($role == $roleToFind) {
            $results[] = "User {$user['name']} ({$user['uid']}) is in the ${role} group.";
          }
        }
      }
    }

    // Return results.
    if (!empty($results)) {
      $sandbox->setParameter('results', $results);
      return FALSE;
    }

    return TRUE;

  }

}
