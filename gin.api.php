<?php

/**
 * @file
 * Hooks for gin theme.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Register routes to apply Gin’s content edit form layout.
 *
 * Leverage this hook to achieve a consistent user interface layout on
 * administrative edit forms, similar to the node edit forms. Any module
 * providing a custom entity type or form mode may wish to implement this
 * hook for their form routes. Please note that not every content entity
 * form route should enable the Gin edit form layout, for example the
 * delete entity form does not need it.
 *
 * @return array
 *   An array of route names.
 *
 * @see GinContentFormHelper->isContentForm()
 * @see hook_gin_content_form_routes_alter()
 */
function hook_gin_content_form_routes() {
  return [
    // Layout a custom node form.
    'entity.node.my_custom_form',

    // Layout a custom entity type edit form.
    'entity.my_type.edit_form',
  ];
}

/**
 * Alter the registered routes to enable or disable Gin’s edit form layout.
 *
 * @param array $routes
 *   The list of routes.
 *
 * @see GinContentFormHelper->isContentForm()
 * @see hook_gin_content_form_routes()
 */
function hook_gin_content_form_routes_alter(array &$routes) {
  // Example: disable Gin edit form layout customizations for an entity type.
  $routes = array_diff($routes, ['entity.my_type.edit_form']);
}

/**
 * Register form ids to skip Gin’s content edit form layout.
 *
 * In some cases, routes alone are not sufficient to identify which forms should
 * or should not be included in Gin's edit form layout. If you need to include
 * or exclude additional forms by identifier, use this hook.
 *
 * Any form whose id starts with the given prefixes will be skipped when
 * applying the gin content edit form layout. One use case for this is
 * forms-within-forms, as in paragraph layouts.
 *
 * @return array
 *   An array of form id prefixes.
 *
 * @see GinContentFormHelper->isContentForm()
 * @see hook_gin_form_ids_to_ignore_alter()
 */
function hook_gin_form_ids_to_ignore() {
  return [
    'my_complex_form',
    'media_library_add_form_',
    'views_form_media_library_widget_',
    'views_exposed_form',
    'date_recur_modular_sierra_occurrences_modal',
    'date_recur_modular_sierra_modal',
  ];
}

/**
 * Alter the registered form ids to be ignored by Gin’s edit form layout.
 *
 * @param array $routes
 *   The list of routes.
 *
 * @see GinContentFormHelper->isContentForm()
 * @see hook_gin_form_ids_to_ignore()
 */
function hook_gin_form_ids_to_ignore_alter(array &$ids) {
  // Example: disable Gin edit form layout customizations for a custom form.
  $ids[] = 'my_custom_form';
}

/**
 * @} End of "addtogroup hooks".
 */
