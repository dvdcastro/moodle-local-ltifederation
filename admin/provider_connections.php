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
 * Admin page: LTI Provider connections.
 *
 * @package     local_ltifederation
 * @copyright   2026 David Castro
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

use local_ltifederation\form\provider_form;
use local_ltifederation\encryption_helper;
use local_ltifederation\task\sync_tools;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/ltifederation/admin/provider_connections.php'));
$PAGE->set_title(get_string('provider_connections', 'local_ltifederation'));
$PAGE->set_heading(get_string('provider_connections_heading', 'local_ltifederation'));
$PAGE->set_pagelayout('admin');

require_login();
require_capability('local/ltifederation:manageproviders', $context);

// Breadcrumbs.
$PAGE->navbar->add(get_string('administrationsite'), new moodle_url('/admin/index.php'));
$PAGE->navbar->add(get_string('pluginname', 'local_ltifederation'));
$PAGE->navbar->add(get_string('provider_connections', 'local_ltifederation'), $PAGE->url);

$action   = optional_param('action', '', PARAM_ALPHA);
$id       = optional_param('id', 0, PARAM_INT);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);

// Handle delete action.
if ($action === 'delete' && $id > 0) {
    require_sesskey();
    if ($confirm) {
        $DB->delete_records('local_ltifederation_catalog_cache', ['providerid' => $id]);
        $DB->delete_records('local_ltifederation_providers', ['id' => $id]);
        \core\notification::success(get_string('provider_deleted', 'local_ltifederation'));
        redirect($PAGE->url);
    } else {
        $provider = $DB->get_record('local_ltifederation_providers', ['id' => $id]);
        if ($provider) {
            echo $OUTPUT->header();
            echo $OUTPUT->confirm(
                get_string('provider_delete_confirm', 'local_ltifederation', $provider->label),
                new moodle_url($PAGE->url, ['action' => 'delete', 'id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]),
                $PAGE->url
            );
            echo $OUTPUT->footer();
            exit;
        }
    }
}

// Handle sync now action.
if ($action === 'sync' && $id > 0) {
    require_sesskey();
    $provider = $DB->get_record('local_ltifederation_providers', ['id' => $id]);
    if ($provider) {
        $task = new sync_tools();
        $task->set_custom_data(['providerid' => $id]);
        \core\task\manager::queue_adhoc_task($task, true);
        \core\notification::success(get_string('provider_sync_queued', 'local_ltifederation'));
    }
    redirect($PAGE->url);
}

// Provider add/edit form.
$form = new provider_form($PAGE->url);

if ($form->is_cancelled()) {
    redirect($PAGE->url);
} else if ($data = $form->get_data()) {
    // Save provider.
    $record = new stdClass();
    $record->label       = $data->label;
    $record->providerurl = rtrim($data->providerurl, '/');
    $record->autosync    = (int) $data->autosync;
    $record->timemodified = time();

    // Encrypt the wstoken before storing.
    if (!empty($data->wstoken)) {
        $record->wstoken = encryption_helper::encrypt($data->wstoken);
    }

    if ($data->id > 0) {
        $record->id = $data->id;
        $DB->update_record('local_ltifederation_providers', $record);
    } else {
        $record->timecreated = time();
        $DB->insert_record('local_ltifederation_providers', $record);
    }
    \core\notification::success(get_string('provider_saved', 'local_ltifederation'));
    redirect($PAGE->url);
}

// Pre-populate form for edit.
if ($action === 'edit' && $id > 0) {
    $provider = $DB->get_record('local_ltifederation_providers', ['id' => $id]);
    if ($provider) {
        // Do not pre-populate the token for security; user must re-enter if they want to change.
        $formdata = clone $provider;
        $formdata->wstoken = ''; // Clear for security.
        $form->set_data($formdata);
    }
}

// Render page.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('provider_connections_heading', 'local_ltifederation'));
echo html_writer::tag('p', get_string('provider_connections_desc', 'local_ltifederation'));

// Add provider button.
$addurl = new moodle_url($PAGE->url, ['action' => 'edit', 'id' => 0]);
echo html_writer::div(
    html_writer::link(
        $addurl,
        get_string('add_provider', 'local_ltifederation'),
        ['class' => 'btn btn-primary mb-3']
    ),
    'mb-3'
);

// Show form if in add/edit mode.
if ($action === 'edit') {
    echo html_writer::start_div('card mb-4');
    echo html_writer::start_div('card-body');
    $form->display();
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Providers table.
$providers = $DB->get_records('local_ltifederation_providers', null, 'timecreated ASC');

if (empty($providers)) {
    echo $OUTPUT->notification(get_string('no_providers', 'local_ltifederation'), 'info');
} else {
    $table = new html_table();
    $table->attributes['class'] = 'table table-striped generaltable';
    $table->head = [
        get_string('provider_label', 'local_ltifederation') .
            $OUTPUT->help_icon('provider_label', 'local_ltifederation'),
        get_string('provider_url', 'local_ltifederation') .
            $OUTPUT->help_icon('provider_url', 'local_ltifederation'),
        get_string('provider_autosync', 'local_ltifederation') .
            $OUTPUT->help_icon('provider_autosync', 'local_ltifederation'),
        get_string('provider_lastsync', 'local_ltifederation') .
            $OUTPUT->help_icon('provider_lastsync', 'local_ltifederation'),
        get_string('provider_syncstatus', 'local_ltifederation') .
            $OUTPUT->help_icon('provider_syncstatus', 'local_ltifederation'),
        get_string('provider_actions', 'local_ltifederation'),
    ];

    foreach ($providers as $provider) {
        // Status indicator with color-coded icon.
        if ($provider->syncstatus === 'ok') {
            $statusicon = html_writer::tag('span', '&#10003;', ['class' => 'text-success font-weight-bold', 'title' => get_string('status_ok', 'local_ltifederation')]);
            $badge = $statusicon . ' ' . html_writer::span(get_string('status_ok', 'local_ltifederation'), 'badge badge-success ml-1');
        } else if ($provider->syncstatus === 'error') {
            $statusicon = html_writer::tag('span', '&#10007;', ['class' => 'text-danger font-weight-bold', 'title' => get_string('status_error', 'local_ltifederation')]);
            $badge = $statusicon . ' ' . html_writer::span(get_string('status_error', 'local_ltifederation'), 'badge badge-danger ml-1');
            // Show collapsed error details.
            if (!empty($provider->syncmessage)) {
                $badge .= html_writer::start_tag('details', ['class' => 'mt-1']);
                $badge .= html_writer::tag('summary', get_string('show_sync_error', 'local_ltifederation'), ['class' => 'small text-danger']);
                $badge .= html_writer::tag('pre', s($provider->syncmessage), ['class' => 'small text-danger mt-1 mb-0']);
                $badge .= html_writer::end_tag('details');
            }
        } else {
            $statusicon = html_writer::tag('span', '&mdash;', ['class' => 'text-muted', 'title' => get_string('provider_never_synced', 'local_ltifederation')]);
            $badge = $statusicon . ' ' . html_writer::span(get_string('provider_never_synced', 'local_ltifederation'), 'badge badge-secondary ml-1');
        }

        $lastsync = $provider->lastsync
            ? userdate($provider->lastsync, get_string('strftimedatetime', 'langconfig'))
            : get_string('provider_never_synced', 'local_ltifederation');

        // Action links.
        $editurl   = new moodle_url($PAGE->url, ['action' => 'edit', 'id' => $provider->id]);
        $deleteurl = new moodle_url($PAGE->url, ['action' => 'delete', 'id' => $provider->id, 'sesskey' => sesskey()]);
        $syncurl   = new moodle_url($PAGE->url, ['action' => 'sync', 'id' => $provider->id, 'sesskey' => sesskey()]);
        $catalogurl = new moodle_url('/local/ltifederation/admin/tool_catalog.php', ['providerid' => $provider->id]);

        $actions = implode(' ', [
            $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('provider_edit', 'local_ltifederation'))),
            $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('provider_delete', 'local_ltifederation'))),
            $OUTPUT->action_icon($syncurl, new pix_icon('t/reload', get_string('provider_sync_now', 'local_ltifederation'))),
            $OUTPUT->action_icon($catalogurl, new pix_icon('i/search', get_string('provider_view_catalog', 'local_ltifederation'))),
        ]);

        $table->data[] = [
            format_string($provider->label),
            format_string($provider->providerurl),
            $provider->autosync ? get_string('yes') : get_string('no'),
            $lastsync,
            $badge,
            $actions,
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
