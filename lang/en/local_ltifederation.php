<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language strings for local_ltifederation.
 *
 * @package     local_ltifederation
 * @copyright   2026 David Castro
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin name.
$string['pluginname'] = 'LTI Federation';

// Capabilities.
$string['ltifederation:providecatalog'] = 'Provide LTI tool catalog via web service';
$string['ltifederation:manageproviders'] = 'Manage LTI federation provider connections';

// Settings.
$string['setting_role'] = 'Site role';
$string['setting_role_desc'] = 'Select whether this site acts as a provider (exposes tools), consumer (subscribes to remote catalogs), or both.';
$string['role_provider'] = 'Provider (exposes tools)';
$string['role_consumer'] = 'Consumer (subscribes to remote catalogs)';
$string['role_both'] = 'Both (provider and consumer)';

// Admin navigation.
$string['provider_connections'] = 'LTI Provider connections';

// Provider connections page.
$string['provider_connections_heading'] = 'LTI Provider connections';
$string['provider_connections_desc'] = 'Manage connections to remote Moodle sites that publish LTI 1.3 tool catalogs.';
$string['add_provider'] = 'Add provider';
$string['no_providers'] = 'No providers configured. Click "Add provider" to add one.';
$string['provider_label'] = 'Label';
$string['provider_url'] = 'Provider URL';
$string['provider_autosync'] = 'Auto-sync';
$string['provider_lastsync'] = 'Last sync';
$string['provider_syncstatus'] = 'Status';
$string['provider_actions'] = 'Actions';
$string['provider_edit'] = 'Edit';
$string['provider_delete'] = 'Delete';
$string['provider_sync_now'] = 'Sync now';
$string['provider_view_catalog'] = 'View catalog';
$string['provider_delete_confirm'] = 'Are you sure you want to delete provider "{$a}"? This will also remove all cached tool catalog entries.';
$string['provider_saved'] = 'Provider saved successfully.';
$string['provider_deleted'] = 'Provider deleted.';
$string['provider_sync_queued'] = 'Catalog sync has been queued.';
$string['provider_sync_queued_info'] = 'Catalog sync has been queued and will run on the next cron cycle. Refresh this page after cron runs to see updated results.';
$string['provider_sync_ok'] = 'Sync completed successfully.';
$string['provider_sync_error'] = 'Sync failed: {$a}';
$string['provider_never_synced'] = 'Never';
$string['status_ok'] = 'OK';
$string['status_error'] = 'Error';

// Provider connections column help strings (Phase 2).
$string['provider_label_help'] = 'The human-readable label used to identify this provider connection.';
$string['provider_label_help_link'] = 'provider_label, local_ltifederation, provider_label';
$string['provider_url_help'] = 'The base URL of the remote Moodle site that publishes the LTI tool catalog.';
$string['provider_url_help_link'] = 'provider_url, local_ltifederation, provider_url';
$string['provider_autosync_help'] = 'When enabled, this provider\'s catalog will be automatically synced daily by the scheduled task.';
$string['provider_autosync_help_link'] = 'provider_autosync, local_ltifederation, provider_autosync';
$string['provider_lastsync_help'] = 'The date and time when the catalog was last fetched from this provider.';
$string['provider_lastsync_help_link'] = 'provider_lastsync, local_ltifederation, provider_lastsync';
$string['provider_syncstatus_help'] = 'The result of the last sync operation. Green checkmark = OK, red X = error, grey dash = never synced.';
$string['provider_syncstatus_help_link'] = 'provider_syncstatus, local_ltifederation, provider_syncstatus';

// Sync error display.
$string['show_sync_error'] = 'Show error details';
$string['show_error_details'] = 'Show error details';

// Provider form fields.
$string['label'] = 'Label';
$string['label_help'] = 'A human-readable name to identify this LTI provider (e.g. "Central Moodle").';
$string['label_help_link'] = 'label, local_ltifederation, label';
$string['providerurl'] = 'Provider URL';
$string['providerurl_help'] = 'The base URL of the remote Moodle site that publishes the LTI tool catalog (e.g. https://provider.example.com).';
$string['providerurl_help_link'] = 'providerurl, local_ltifederation, providerurl';
$string['wstoken'] = 'Web service token';
$string['wstoken_help'] = 'The web service token generated on the remote provider site with access to the ltifederationcatalog service.';
$string['wstoken_help_link'] = 'wstoken, local_ltifederation, wstoken';
$string['autosync'] = 'Auto-sync catalog';
$string['autosync_help'] = 'When enabled, this provider\'s catalog will be synced automatically via scheduled tasks.';
$string['autosync_help_link'] = 'autosync, local_ltifederation, autosync';

// Tool catalog page.
$string['tool_catalog_heading'] = 'Tool catalog: {$a}';
$string['tool_catalog_desc'] = 'LTI 1.3 tools available from this provider. Register tools to make them available locally.';
$string['tool_name'] = 'Tool name';
$string['tool_course'] = 'Course';
$string['tool_ltiversion'] = 'LTI version';
$string['tool_regstate'] = 'Registration state';
$string['tool_actions'] = 'Actions';
$string['tool_register'] = 'Register';
$string['tool_reregister'] = 'Re-register';
$string['tool_register_selected'] = 'Register selected';
$string['tool_select_all'] = 'Select all';
$string['no_tools'] = 'No tools found. Click "Sync now" to fetch the catalog from this provider.';
$string['regstate_none'] = 'Not registered';
$string['regstate_pending'] = 'Pending';
$string['regstate_registered'] = 'Registered';
$string['regstate_error'] = 'Error';
$string['regstate_removed'] = 'Removed from provider';
$string['tool_registered_ok'] = 'Tool registered successfully.';
$string['tool_registered_error'] = 'Registration failed: {$a}';
$string['tools_registered_count'] = '{$a} tool(s) registered successfully.';
$string['provider_info_label'] = 'Provider';
$string['provider_info_url'] = 'URL';
$string['provider_info_lastsync'] = 'Last sync';
$string['provider_info_status'] = 'Status';
$string['sync_now'] = 'Sync now';
$string['back_to_providers'] = 'Back to providers';

// Tool catalog column help.
$string['tool_name_help'] = 'The name of the LTI tool as published by the remote provider.';
$string['tool_name_help_link'] = 'tool_name, local_ltifederation, tool_name';
$string['tool_course_help'] = 'The course this tool belongs to on the remote provider site.';
$string['tool_course_help_link'] = 'tool_course, local_ltifederation, tool_course';
$string['tool_ltiversion_help'] = 'The LTI protocol version of this tool.';
$string['tool_ltiversion_help_link'] = 'tool_ltiversion, local_ltifederation, tool_ltiversion';
$string['tool_regstate_help'] = 'The current registration state of this tool on this site. Active = registered and configured; Pending = registration in progress; Error = registration failed; Not registered = not yet registered; Removed = no longer in provider catalog.';
$string['tool_regstate_help_link'] = 'tool_regstate, local_ltifederation, tool_regstate';

// Task names.
$string['task_sync_tools'] = 'Sync LTI tool catalog';
$string['task_sync_all_providers'] = 'Sync all LTI provider catalogs';
$string['task_cleanup_draft_registrations'] = 'Clean up expired LTI draft registrations';

// Errors.
$string['error_invalid_provider'] = 'Invalid provider ID.';
$string['error_invalidurl'] = 'Please enter a valid URL (e.g. https://example.com).';
$string['error_ssrf_blocked'] = 'Registration URL host does not match provider URL host. Blocked for security.';
$string['error_https_required'] = 'Registration URL must use HTTPS for security.';
$string['error_already_registered'] = 'This tool is already registered.';
$string['error_no_items_selected'] = 'No tools selected.';
$string['error_provider_not_found'] = 'Provider not found.';
$string['error_ws_call_failed'] = 'Web service call failed: {$a}';
$string['error_invalid_ws_response'] = 'Invalid or unexpected response from provider web service.';

// Privacy API strings.
$string['privacy:metadata:local_ltifederation_providers'] = 'Stores admin-configured provider connection settings. This data is not personal user data — it is set by the site administrator to connect to remote Moodle provider sites.';
$string['privacy:metadata:local_ltifederation_providers:label'] = 'A human-readable label for the provider connection.';
$string['privacy:metadata:local_ltifederation_providers:providerurl'] = 'The base URL of the remote Moodle provider site.';
$string['privacy:metadata:local_ltifederation_providers:wstoken'] = 'The encrypted web service token used to authenticate with the remote provider.';
$string['privacy:metadata:local_ltifederation_providers:autosync'] = 'Whether automatic catalog synchronisation is enabled for this provider.';
$string['privacy:metadata:local_ltifederation_providers:lastsync'] = 'Timestamp of the last successful catalog sync.';
$string['privacy:metadata:local_ltifederation_providers:syncstatus'] = 'The status of the last sync operation (ok or error).';
$string['privacy:metadata:local_ltifederation_providers:syncmessage'] = 'Any error message from the last sync operation.';
$string['privacy:metadata:local_ltifederation_catalog_cache'] = 'Stores a cache of LTI tool metadata fetched from remote provider sites. This is tool/course metadata from the provider, not personal user data.';
$string['privacy:metadata:local_ltifederation_catalog_cache:providerid'] = 'Reference to the provider this tool belongs to.';
$string['privacy:metadata:local_ltifederation_catalog_cache:remoteuuid'] = 'The unique identifier of the tool on the remote provider site.';
$string['privacy:metadata:local_ltifederation_catalog_cache:name'] = 'The name of the LTI tool as published by the provider.';
$string['privacy:metadata:local_ltifederation_catalog_cache:description'] = 'A description of the LTI tool as published by the provider.';
$string['privacy:metadata:local_ltifederation_catalog_cache:coursefullname'] = 'The full name of the course containing the tool on the provider site.';
$string['privacy:metadata:local_ltifederation_catalog_cache:ltiversion'] = 'The LTI version of this tool.';
$string['privacy:metadata:local_ltifederation_catalog_cache:registration_url'] = 'The dynamic registration URL for this tool on the provider.';
$string['privacy:metadata:local_ltifederation_catalog_cache:registration_token'] = 'The registration token associated with this tool\'s dynamic registration URL.';
$string['privacy:metadata:local_ltifederation_catalog_cache:regstate'] = 'The current registration state of this tool on this site.';
$string['privacy:metadata:local_ltifederation_catalog_cache:regerror'] = 'Any error message from a failed registration attempt.';
$string['privacy:metadata:remote_moodle_provider'] = 'This plugin communicates with configured remote Moodle provider sites on behalf of the site administrator to fetch LTI tool catalog data. The web service token is sent to authenticate the request. No personal user data is transmitted.';
$string['privacy:metadata:remote_moodle_provider:wstoken'] = 'The web service token sent to authenticate with the remote provider web service.';
