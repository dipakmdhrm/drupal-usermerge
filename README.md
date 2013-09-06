# User merge

## Development notes

First off, originally I though about releasing this code as a module separate from User Merge, which is why some of the wording is different (especially in the use of the word "account" instead of "user"—there's a whole philosophical thing behind it).

It's not totally compliant with Drupal's coding standards yet, but simply for readability (for instance, I prefer working with `if … endif`). But the plan was to standardize everything later.

### General changes

`usermerge.module` provides only the API, and doesn't actually do any merging of its own. It implements `hook_hook_info()`, so other modules can provide their own `<module>.usermerge.inc` files.

Core-specific functionality (default user properties, fields) is managed in `usermerge.usermerge.inc`, which also includes support for entities that have a `uid` column, and basic display support for non-default user properties that aren't structured like fields (such as `rdf_mapping`).

Immediate support for other modules is contained in module-specific files (I'll commit them when they're all ready) in the `includes` directory. These are loaded when needed by `usermerge_load_includes()` (since `usermerge_hook_info()` doesn't see them).

I've also changed slightly the way supported actions are displayed.

### Main hooks

#### `hook_usermerge_account_properties()`

It allows module to build the list of properties to be displayed in the data-review table. It's the very first part of the module I wrote, so it's probably a bit clumsy and can be cleaned up.

#### `hook_usermerge_build_review_form_elements()`

It uses the array produced by the previous hook to build the data-review form, which will then be displayed as a series of separate tables (each themed by `theme_usermerge_data_review_form_table()`).
