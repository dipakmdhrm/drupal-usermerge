# User merge

## Notes for version 2.x

A new interface allows users with the right permissions to choose how each user property should be merged. This includes the ability to merge fields, referencing entities, and other entities owned by the selected users. This aims to provide a more finely tuned merge process, as well as to minimize errors and information loss.

### General changes

`usermerge.module` provides only the API, and doesn't actually do any merging of its own. It implements `hook_hook_info()`, so other modules can provide their own `<module>.usermerge.inc` files.

Core-specific functionality (default user properties, fields) is managed in `usermerge.usermerge.inc`, which also includes support for entities that have a `uid` column, and basic display support for non-default user properties that aren't structured like fields (such as `rdf_mapping`).

Immediate support for other modules is contained in module-specific files in the `includes` directory. These are loaded when needed by `usermerge_load_includes()` (since `usermerge_hook_info()` doesn't see them). Modules supported out of the box include:

- Entity Reference
- Multiple E-mail
- Profile (code from User merge 1, left untouched)
- RDF
- Real Name
- User Reference (References)
