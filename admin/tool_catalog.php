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
 * Admin page: Tool catalog for a specific provider.
 *
 * @package     local_ltifederation
 * @copyright   2026 David Castro
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

use local_ltifederation\registration_engine;
use local_ltifederation\task\sync_tools;

$providerid = required_param('providerid', PARAM_INT);
$action     = optional_param('action', '', PARAM_ALPHA);
$ids        = optional_param_array('ids', [], PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/ltifederation/admin/tool_catalog.php', ['providerid' => $providerid]));
$PAGE->set_pagelayout('admin');

require_login();
require_capability('local/ltifederation:manageproviders', $context);

// Load provider.
$provider = $DB->get_record('local_ltifed_providers', ['id' => $providerid]);
if (!$provider) {
    throw new moodle_exception('error_invalid_provider', 'local_ltifederation');
}

$PAGE->set_title(get_string('tool_catalog_heading', 'local_ltifederation', $provider->label));
$PAGE->set_heading(get_string('tool_catalog_heading', 'local_ltifederation', $provider->label));

// Breadcrumbs.
$PAGE->navbar->add(get_string('administrationsite'), new moodle_url('/admin/index.php'));
$PAGE->navbar->add(get_string('pluginname', 'local_ltifederation'));
$PAGE->navbar->add(
    get_string('provider_connections', 'local_ltifederation'),
    new moodle_url('/local/ltifederation/admin/provider_connections.php')
);
$PAGE->navbar->add(get_string('tool_catalog_heading', 'local_ltifederation', $provider->label), $PAGE->url);

// Handle sync now.
if ($action === 'sync') {
    require_sesskey();
    $task = new sync_tools();
    $task->set_custom_data(['providerid' => $providerid]);
    \core\task\manager::queue_adhoc_task($task, true);
    \core\notification::success(get_string('provider_sync_queued', 'local_ltifederation'));
    redirect($PAGE->url);
}

// Handle register action (single or bulk).
if ($action === 'register' && !empty($ids)) {
    require_sesskey();
    $engine = new registration_engine();
    $success = 0;
    $errors  = [];

    foreach ($ids as $cacheentryid) {
        $entry = $DB->get_record('local_ltifed_catalog_cache', ['id' => $cacheentryid, 'providerid' => $providerid]);
        if (!$entry) {
            continue;
        }
        try {
            $engine->register_tool($entry, $provider);
            $success++;
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    if ($success > 0) {
        \core\notification::success(get_string('tools_registered_count', 'local_ltifederation', $success));
    }
    foreach ($errors as $err) {
        \core\notification::error(get_string('tool_registered_error', 'local_ltifederation', $err));
    }
    redirect($PAGE->url);
}

// --- Render page ---
echo $OUTPUT->header();

// Provider info card.
echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-body');
echo html_writer::tag('h5', get_string('provider_info_label', 'local_ltifederation') . ': ' . format_string($provider->label), ['class' => 'card-title']);

$inforows = [
    get_string('provider_info_url', 'local_ltifederation')      => format_string($provider->providerurl),
    get_string('provider_info_lastsync', 'local_ltifederation') => $provider->lastsync
        ? userdate($provider->lastsync, get_string('strftimedatetime', 'langconfig'))
        : get_string('provider_never_synced', 'local_ltifederation'),
    get_string('provider_info_status', 'local_ltifederation')   => $provider->syncstatus ?: '-',
];

$dl = '';
foreach ($inforows as $label => $value) {
    $dl .= html_writer::tag('dt', $label, ['class' => 'col-sm-3']);
    $dl .= html_writer::tag('dd', $value, ['class' => 'col-sm-9']);
}
echo html_writer::tag('dl', $dl, ['class' => 'row']);

// Sync now button.
$syncurl = new moodle_url($PAGE->url, ['action' => 'sync', 'sesskey' => sesskey()]);
echo html_writer::link($syncurl, get_string('sync_now', 'local_ltifederation'), ['class' => 'btn btn-secondary mr-2']);

// Back link.
$backurl = new moodle_url('/local/ltifederation/admin/provider_connections.php');
echo html_writer::link($backurl, get_string('back_to_providers', 'local_ltifederation'), ['class' => 'btn btn-outline-secondary']);

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// Tool catalog table.
$tools = $DB->get_records('local_ltifed_catalog_cache', ['providerid' => $providerid], 'name ASC');

if (empty($tools)) {
    echo $OUTPUT->notification(get_string('no_tools', 'local_ltifederation'), 'info');
} else {
    // Register selected form.
    $formaction = new moodle_url($PAGE->url, ['action' => 'register', 'sesskey' => sesskey()]);
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $formaction->out(false)]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

    $table = new html_table();
    $table->attributes['class'] = 'table table-striped generaltable';

    // Headers with help icons.
    $table->head = [
        html_writer::checkbox('selectall', 1, false, '', ['id' => 'ltifed-selectall']),
        get_string('tool_name', 'local_ltifederation') .
            $OUTPUT->help_icon('tool_name', 'local_ltifederation'),
        get_string('tool_course', 'local_ltifederation') .
            $OUTPUT->help_icon('tool_course', 'local_ltifederation'),
        get_string('tool_ltiversion', 'local_ltifederation') .
            $OUTPUT->help_icon('tool_ltiversion', 'local_ltifederation'),
        get_string('tool_regstate', 'local_ltifederation') .
            $OUTPUT->help_icon('tool_regstate', 'local_ltifederation'),
        get_string('tool_actions', 'local_ltifederation'),
    ];

    foreach ($tools as $tool) {
        // Registration state badge.
        switch ($tool->regstate) {
            case 'registered':
                $badge = html_writer::span(
                    get_string('regstate_registered', 'local_ltifederation'),
                    'badge badge-success'
                );
                break;
            case 'pending':
                $badge = html_writer::span(
                    get_string('regstate_pending', 'local_ltifederation'),
                    'badge badge-warning'
                );
                break;
            case 'error':
                $badge = html_writer::span(
                    get_string('regstate_error', 'local_ltifederation'),
                    'badge badge-danger'
                );
                break;
            default:
                $badge = html_writer::span(
                    get_string('regstate_none', 'local_ltifederation'),
                    'badge badge-secondary'
                );
        }

        // If remotestatus = 1 (removed from provider), add visual indicator.
        if ($tool->remotestatus) {
            $badge .= ' ' . html_writer::span('(removed)', 'badge badge-light text-muted');
        }

        // Per-row register link.
        $registerurl = new moodle_url($PAGE->url, [
            'action'    => 'register',
            'ids[]'     => $tool->id,
            'sesskey'   => sesskey(),
        ]);
        $reregisterurl = $registerurl; // Same URL – engine handles idempotency.

        $actionlabel = ($tool->regstate === 'registered')
            ? get_string('tool_reregister', 'local_ltifederation')
            : get_string('tool_register', 'local_ltifederation');

        $actionlink = html_writer::link($registerurl, $actionlabel, ['class' => 'btn btn-sm btn-outline-primary']);

        $table->data[] = [
            html_writer::checkbox('ids[]', $tool->id, false, '', ['class' => 'ltifed-tool-checkbox']),
            format_string($tool->name),
            format_string($tool->coursefullname ?? ''),
            format_string($tool->ltiversion ?? ''),
            $badge,
            $actionlink,
        ];
    }

    echo html_writer::table($table);

    // Bulk actions footer.
    echo html_writer::start_div('mt-2');
    echo html_writer::tag(
        'button',
        get_string('tool_register_selected', 'local_ltifederation'),
        ['type' => 'submit', 'class' => 'btn btn-primary', 'name' => 'action', 'value' => 'register']
    );
    echo html_writer::end_div();
    echo html_writer::end_tag('form');

    // JS for select-all checkbox.
    $PAGE->requires->js_amd_inline("
        document.getElementById('ltifed-selectall').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.ltifed-tool-checkbox');
            checkboxes.forEach(function(cb) { cb.checked = this.checked; }, this);
        });
    ");
}

echo $OUTPUT->footer();
