<?php
/**
 * @file
 * Adds support for Field Collection.
 * Supplemental include loaded via usermerge_load_includes().
 *
 */

/**
 * Implement hook_usermerge_merge_form_alter() on behalf of field_collection.
 */
function field_collection_usermerge_data_review_form_alter(&$form, $user_to_delete, $user_to_keep) {
  // Used to remove "Add" links from the display of Field Collection data on the review table
  $fields = reset(field_info_instances('user'));

  foreach ( $fields as $field_name => $field_instance ) :
    if ( $field_instance['widget']['module'] == 'field_collection' ) :
      $field_collection_user_to_delete = field_view_field('user', $user_to_delete, $field_name, array('label' => 'hidden', 'settings' => array('edit' => NULL, 'delete' => NULL, 'add' => NULL)));
      $form['review']['fields'][$field_name]['options']['#options']['user_to_delete'] = strip_tags(drupal_render($field_collection_user_to_delete), '<em> <strong> <ul> <li>');

      $field_collection_user_to_keep = field_view_field('user', $user_to_keep, $field_name, array('label' => 'hidden', 'settings' => array('edit' => NULL, 'delete' => NULL, 'add' => NULL)));
      $form['review']['fields'][$field_name]['options']['#options']['user_to_keep'] = strip_tags(drupal_render($field_collection_user_to_keep), '<em> <strong> <ul> <li>');
    endif;
  endforeach;
}