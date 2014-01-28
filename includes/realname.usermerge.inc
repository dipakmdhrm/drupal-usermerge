<?php
/**
 * @file
 * Adds support for Real Name.
 * Supplemental include loaded via usermerge_load_includes().
 *
 */

/**
 * Implement hook_usermerge_account_properties_alter() on behalf of realname.
 */
function realname_usermerge_account_properties_alter(&$properties, $user_to_delete, $user_to_keep) {
  // Enables the "merge" option for account e-mail addresses
  $properties['other']['items']['realname']['criterion'] = 'no_merge';
}

/**
 * Implement hook_usermerge_merge_accounts_alter() on behalf of realname.
 */
function realname_usermerge_merge_accounts_alter($merged_account, $user_to_delete, $user_to_keep) {
  // Removes the Real Name, which will be recreated automatically
  $merged_account['realname'] = NULL;
  
  return $merged_account;
}