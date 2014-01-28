<?php
/**
 * @file
 * Adds support for RDF.
 * Supplemental include loaded via usermerge_load_includes().
 *
 */

/**
 * Implement hook_usermerge_account_properties_alter() on behalf of rdf.
 */
function rdf_usermerge_account_properties_alter(&$properties, $user_to_delete, $user_to_keep) {
  // Sets the default to the value of $user_to_keep
  $properties['other']['items']['rdf_mapping']['default'] = $user_to_keep->rdf_mapping;
}