<?php

namespace Drutiny\Fubarhouse\Audit\Drupal8;

use Drutiny\Audit;
use Drutiny\Sandbox\Sandbox;
use Drutiny\Annotation\Param;

/**
 * Audit to check ig user role has users associated to it.
 * @Param(
 *  name = "roles",
 *  description = "The machine name of the user role",
 * )
 * @Param(
 *   name = "allowed",
 *   description = "An array of user ID's to exclude from reporting",
 * )
 */
class UserRoleCheck extends Audit {

  /**
   * @inheritdoc
   */
  public function audit(Sandbox $sandbox) {

    // Parameters.
    $rolesToFind = $sandbox->getParameter('roles', array("administrator"));
    $allowedUserIDs = $sandbox->getParameter('allowed', array());

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
          foreach ($allowedUserIDs as $allowedUserID) {
            if ($user['uid'] !== $allowedUserID) {
              if ($role == $roleToFind) {
                $results[] = "User {$user['name']} ({$user['uid']}) is in the ${role} group.";
              }
            }
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
