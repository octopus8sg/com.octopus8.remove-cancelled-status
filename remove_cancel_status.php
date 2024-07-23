<?php

require_once 'remove_cancel_status.civix.php';

use CRM_RemoveCancelStatus_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function remove_cancel_status_civicrm_config(&$config): void {
  _remove_cancel_status_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function remove_cancel_status_civicrm_install(): void {
  _remove_cancel_status_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function remove_cancel_status_civicrm_enable(): void {
  _remove_cancel_status_civix_civicrm_enable();
}

function remove_cancel_status_civicrm_permission(array &$permissions): void {
  $permissions['remove cancelled status'] = [
    'label' => E::ts('CiviCRM: remove cancelled status'),
    'description' => E::ts('Remove Cancelled status in Contribution Form'),
  ];
}

function remove_cancel_status_civicrm_buildForm($formName, &$form) {
  // Check if the current form is the contribution form
  if ($formName == 'CRM_Contribute_Form_Contribution') {
    $currentUser = wp_get_current_user();
    $userRoles = $currentUser->roles;
    civi::log()->debug("Current user roles: " . implode(', ', $userRoles));

    $remove_status = CRM_Core_Permission::check('remove cancelled status') && !CRM_Core_Permission::check('administer CiviCRM');
    // Check if the current user has the "remove cancelled status" permission
    if ($remove_status) {
      // Get the current contribution status options
      $contributionStatusOptions = $form->getElement('contribution_status_id')->_options;

      // Remove the "Cancelled" contribution status option
      foreach ($contributionStatusOptions as $key => $option) {
        if ($option['text'] == 'Cancelled') {
          unset($contributionStatusOptions[$key]);
        }
      }

      // Update the contribution status options on the form
      $form->getElement('contribution_status_id')->_options = $contributionStatusOptions;
    }
  }
}