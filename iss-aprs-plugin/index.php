<?php
/*
Plugin Name:    International Space Station (ISS) APRS Plugin
Plugin URI:     https://github.com/mkbodanu4/iss-aprs-plugin
Description:    Add a map with a map of stations digipeated via ISS to your WordPress site with shortcode.
Version:        1.0.0a
Author:         UR5WKM
Author URI:     https://diy.manko.pro
Text Domain:    iss-aprs-plugin
*/

class ISS_APRS_Plugin
{
    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        add_shortcode('iss_tracker_map', array($this, 'map_shortcode'));

        add_action('wp_ajax_iss_data', array($this, 'ajax_data'));
        add_action('wp_ajax_nopriv_iss_data', array($this, 'ajax_data'));

        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'setting_page'));

        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array($this, 'deactivate'));

        load_plugin_textdomain('iss-aprs-plugin', FALSE, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function deactivate()
    {
        delete_option('iss_frontend_url');
        delete_option('iss_api_key');

        unregister_setting('iss_options_group', 'iss_frontend_url');
        unregister_setting('iss_options_group', 'iss_api_key');
    }

    public function register_settings()
    {
        register_setting('iss_options_group', 'iss_frontend_url');
        register_setting('iss_options_group', 'iss_api_key');
    }

    public function setting_page()
    {
        add_options_page(
            __('Plugin Settings', 'iss-aprs-plugin'),
            __('ISS APRS Tracker', 'iss-aprs-plugin'),
            'manage_options',
            'iss-setting',
            array($this, 'html_form')
        );
    }

    public function html_form()
    {
        ?>
        <style>
            .iss_table {
                border: 1px solid #d3d3d3;
                border-collapse: collapse;
                width: 100%;
            }

            .iss_table td, .iss_table th {
                border: 1px solid #d3d3d3;
                padding: 5px;
                background-color: #fbfbfb;
            }

            .iss_shortcode {
                padding: 24px 10px;
                background-color: #fbfbfb;
                font-size: 17px;
                text-align: center
            }
        </style>
        <div class="wrap">
            <h2><?= __('ISS APRS Plugin', 'iss-aprs-plugin'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('iss_options_group'); ?>
                <h3><?= __('API Settings', 'iss-aprs-plugin'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="iss_frontend_url">
                                <?= __('URL', 'iss-aprs-plugin') . ":"; ?>
                            </label>
                        </th>
                        <td>
                            <input type='text' class="regular-text" id="iss_frontend_url" name="iss_frontend_url"
                                   placeholder="<?= __('E.g.', 'iss-aprs-plugin'); ?> https://demo.com/folder/"
                                   value="<?= get_option('iss_frontend_url'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="iss_api_key">
                                <?= __('API Key', 'iss-aprs-plugin') . ":"; ?>
                            </label>
                        </th>
                        <td>
                            <input type='text' class="regular-text" id="iss_api_key" name="iss_api_key"
                                   placeholder="<?= __('E.g.', 'iss-aprs-plugin'); ?> xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                   value="<?= get_option('iss_api_key'); ?>">
                        </td>
                    </tr>
                </table>

                <h3><?= __('Map', 'iss-aprs-plugin') . ":"; ?></h3>
                <div>
                    <div class="iss_shortcode">
                        [<b>iss_tracker_map</b>
                        map_header="<i><?= __('ISS Tracker', 'iss-aprs-plugin'); ?></i>"
                        map_header_link="<i>https://diy.manko.pro/iss-aprs-tracker/</i>"
                        show_filters="<i>Yes</i>"
                        short_details="<i>No</i>"
                        from="<i>1 hour ago</i>"
                        map_height="<i>480</i>"
                        map_zoom="<i>1</i>"
                        map_center="<i>49.0139,31.2858</i>"]
                    </div>
                    <table class="iss_table">
                        <tr>
                            <th>
                                <?= __('Attribute', 'iss-aprs-plugin'); ?>
                            </th>
                            <th>
                                <?= __('Explanation', 'iss-aprs-plugin'); ?>
                            </th>
                            <th>
                                <?= __('Mandatory?', 'iss-aprs-plugin'); ?>
                            </th>
                            <th>
                                <?= __('Example', 'iss-aprs-plugin'); ?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <i>map_header</i>
                            </td>
                            <td>
                                <?= __('Map header', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('ISS Tracker', 'iss-aprs-plugin'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>map_header_link</i>
                            </td>
                            <td>
                                <?= __('URL to be used in map header text', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('https://diy.manko.pro/', 'iss-aprs-plugin'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>show_filters</i>
                            </td>
                            <td>
                                <?= __('Show filters above map, Yes or No', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                Yes
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>short_details</i>
                            </td>
                            <td>
                                <?= __('Show only call sign in details popup, Yes or No', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                No
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>from</i>
                            </td>
                            <td>
                                <?= __('Filter data starting from this date (format YYYY-MM-DD HH:MM:SS), also accept relative date like "1 hour ago", "6 days ago" etc', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('No', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                1 hour ago
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>map_height</i>
                            </td>
                            <td>
                                <?= __('Map height (px)', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('Yes', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                480
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>map_zoom</i>
                            </td>
                            <td>
                                <?= __('Map zoom', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('Yes', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                1
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <i>map_center</i>
                            </td>
                            <td>
                                <?= __('Map center coordinates', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                <?= __('Yes', 'iss-aprs-plugin'); ?>
                            </td>
                            <td>
                                49.0139,31.2858
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>

        </div>
        <?php
    }

    public function map_shortcode($attributes)
    {
        $guid = substr(md5(mt_rand()), 0, 7);

        $args = shortcode_atts(array(
            'map_header' => '',
            'map_header_link' => '',
            'show_filters' => 'Yes',
            'short_details' => 'No',
            'from' => '',
            'to' => '',
            'map_height' => 480,
            'map_zoom' => 1,
            'map_center' => '',
        ), $attributes);

        if (!$args['map_zoom'] || !$args['map_center'] || !$args['map_center']) {
            return __('Missing mandatory attributes, check shortcode', 'iss-aprs-plugin');
        }

        ob_start();
        ?>
        <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>modules/leaflet/leaflet.css"/>
        <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>modules/pikaday2-datetimepicker/pikaday.css"/>
        <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>modules/font-awesome/css/font-awesome.min.css"/>
        <style>
            #iss_map_box_<?= $guid; ?> {
                position: relative;
                height: <?= intval($args['map_height']).'px'; ?>;
            }

            #iss_map_<?= $guid; ?> {
                height: 100%;
            }

            #iss_map_overlay_<?= $guid; ?> {
                position: absolute;
                top: 0;
                height: 100%;
                width: 100%;
                background-color: #fff;
                z-index: 999999;
                opacity: 0.4;
                display: none;
            }

            .iss_text_bold {
                font-weight: bold;
            }

            <?php if ($args['map_header']) { ?>
            #iss_map_header_box_<?= $guid; ?> {
                position: absolute;
                top: 10px;
                z-index: 401;
                text-align: center;
                width: 100%;
            }

            #iss_map_header_<?= $guid; ?> {
                display: inline-block;
                background: #ffffffdb;
                box-shadow: 0 0 3px #9d9d9d;
                border: 0;
                border-radius: 5px;
                padding: 5px;
            }

            <?php } ?>

            <?php if (strtolower($args['show_filters']) === "yes") { ?>
            #iss_map_filters_<?= $guid; ?> {
                position: absolute;
                top: 10px;
                right: 10px;
                z-index: 401;
                margin-left: 60px;
            }

            #call_sign_<?= $guid; ?>,
            #from_<?= $guid; ?> {
                max-width: 140px;
                text-align: center;
                margin-bottom: 4px;
                box-shadow: 0 0 3px #9d9d9d;
            }

            #refresh_<?= $guid; ?>,
            #reset_<?= $guid; ?> {
                font-family: "Roboto", "Open Sans", sans-serif;
                font-weight: 400;
                padding: 10px 12px;
                min-height: 37px;
                border-radius: 0;
                -webkit-appearance: none;
                -webkit-transition: background 0.2s;
                transition: background 0.2s;
                box-shadow: 0 0 3px #9d9d9d;
            }

            #refresh_<?= $guid; ?> {
                background: #185345;
                color: #fff;
                border: solid 1px #185345;
            }

            #refresh_<?= $guid; ?>:hover {
                background: #101e1a;
            }

            #refresh_<?= $guid; ?>:disabled {
                background: #788680;
                border: solid 1px #788680;
            }

            #reset_<?= $guid; ?> {
                background: #531818;
                color: #fff;
                border: solid 1px #531818;
            }

            #reset_<?= $guid; ?>:hover {
                background: #261414;
            }

            #reset_<?= $guid; ?>:disabled {
                background: #703f3f;
                border: solid 1px #703f3f;
            }

            .pika-time th,
            .pika-time td,
            .pika-table th,
            .pika-table td {
                padding: 0;
                border: none;
            }

            @media screen and (max-width: 400px) {
                #call_sign_<?= $guid; ?>,
                #from_<?= $guid; ?> {
                    min-height: 25px;
                    font-weight: normal;
                    padding: 0;
                }

                #refresh_<?= $guid; ?>,
                #reset_<?= $guid; ?> {
                    padding: 2px 5px;
                    min-height: 25px;
                }
            }

            <?php } ?>
        </style>
        <div id="iss_map_box_<?= $guid; ?>">
            <div id="iss_map_<?= $guid; ?>"></div>
            <?php if ($args['map_header']) { ?>
                <div id="iss_map_header_box_<?= $guid; ?>" style="display: none;">
                    <div id="iss_map_header_<?= $guid; ?>">
                        <?php if ($args['map_header_link']) { ?><a href="<?= $args['map_header_link']; ?>"
                                                                   target="_blank"><?php } ?>
                            <?= $args['map_header']; ?>
                            <?php if ($args['map_header_link']) { ?></a><?php } ?>
                    </div>
                </div>
            <?php } ?>
            <?php if (strtolower($args['show_filters']) === "yes") { ?>
                <div id="iss_map_filters_<?= $guid; ?>" style="display: none;">
                    <div>
                        <input type="text" id="call_sign_<?= $guid; ?>"
                               placeholder="<?= __('Call Sign', 'iss-aprs-plugin'); ?>" value="">
                        <input type="text" id="from_<?= $guid; ?>"
                               placeholder="<?= __('From date', 'iss-aprs-plugin'); ?>"
                               value="">
                        <button onclick="iss_map_reset_<?= $guid; ?>();" id="reset_<?= $guid; ?>"
                                title="<?= __('Reset filters', 'iss-aprs-plugin'); ?>">
                            <i class="fa fa-times"></i>
                        </button>
                        <button onclick="iss_map_reload_data_<?= $guid; ?>(true);" id="refresh_<?= $guid; ?>"
                                title="<?= __('Refresh map', 'iss-aprs-plugin'); ?>">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                </div>
            <?php } ?>
            <div id="iss_map_overlay_<?= $guid; ?>"></div>
        </div>

        <script src="<?= plugin_dir_url(__FILE__); ?>modules/moment/moment-with-locales.min.js"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>modules/leaflet/leaflet.js"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>modules/pikaday2-datetimepicker/pikaday.js"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>modules/orb.js/orb-satellite.v2.min.js"></script>
        <script>
            var iss_map_<?= $guid; ?>,
                iss_map_markers_<?= $guid; ?> = [],
                iss_map_from_<?= $guid; ?> = '',
                iss_map_to_<?= $guid; ?> = '',
                iss_updating_<?= $guid; ?> = false,
                iss_interval_<?= $guid; ?>,
                iss_interval_ms_<?= $guid; ?> = 15000;

            function prevent_scroll_<?= $guid; ?>(e) {
                e.preventDefault();
                e.stopPropagation();

                return false;
            }

            function iss_map_reload_data_<?= $guid; ?>(reset) {
                if (iss_updating_<?= $guid; ?>)
                    return;
                iss_updating_<?= $guid; ?> = true;

                if (reset !== undefined && reset === true) {
                    clearInterval(iss_interval_<?= $guid; ?>);
                    iss_interval_<?= $guid; ?> = setInterval(function () {
                        iss_map_reload_data_<?= $guid; ?>();
                    }, iss_interval_ms_<?= $guid; ?>);
                }

                <?php if (strtolower($args['show_filters']) === "yes") { ?>
                document.getElementById('refresh_<?= $guid; ?>').setAttribute('disabled', 'disabled');
                <?php } ?>
                document.getElementById('iss_map_overlay_<?= $guid; ?>').style.display = 'block';
                document.getElementById('iss_map_overlay_<?= $guid; ?>').addEventListener('wheel', prevent_scroll_<?= $guid; ?>);

                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "<?= admin_url('admin-ajax.php');?>", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
                xhttp.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        var json = JSON.parse(this.response);

                        if (json.data !== undefined) {
                            var marker;

                            iss_map_markers_<?= $guid; ?>.forEach(function (marker) {
                                iss_map_<?= $guid; ?>.removeLayer(marker);
                            });
                            iss_map_markers_<?= $guid; ?> = [];

                            for (const [call_sign, packet] of Object.entries(json.data)) {
                                var latlng = new L.LatLng(packet.lat, packet.lng);

                                var symbol = 47, symbol_table = 1;
                                if (packet.st && packet.s) {
                                    symbol_table = packet.st.charCodeAt(0);
                                    symbol = packet.s.charCodeAt(0);
                                }
                                var img = '<?= plugin_dir_url(__FILE__); ?>symbols/symbol-' + symbol + '-' + symbol_table + '.svg';

                                var iconSize = [24, 24];
                                var iconAnchor = [12, 24];

                                marker = L.marker(latlng, {
                                    title: call_sign,
                                    icon: L.icon({
                                        iconUrl: img,
                                        iconSize: iconSize,
                                        iconAnchor: iconAnchor
                                    })
                                }).addTo(iss_map_<?= $guid; ?>);
                                var packet_date = +new Date(packet.t * 1000),
                                    popup_content = '<div>' + moment(packet_date).fromNow() + '</div>' +
                                        '<div class="iss_text_bold">' + call_sign + '</div>';

                                <?php if (strtolower($args['short_details']) !== "yes") { ?>
                                popup_content += '<div>' + moment(packet_date).format("LLL") + '</div>';
                                if (packet.c !== undefined && packet.c) {
                                    popup_content += '<br/>' + '<div><b><?= __('Comment', 'iss-aprs-plugin'); ?></b>: ' + packet.c + '</div>';
                                }
                                <?php } ?>

                                marker.bindPopup(popup_content)
                                iss_map_markers_<?= $guid; ?>.push(marker);
                            }
                        }

                        var iss_xhttp = new XMLHttpRequest();
                        iss_xhttp.open("GET", "<?= plugin_dir_url(__FILE__); ?>cache/iss.txt", false);
                        iss_xhttp.onreadystatechange = function () {
                            if (this.readyState === 4 && this.status === 200) {
                                var txt = this.response.split("\n");

                                var tle = {
                                    first_line: txt[1],
                                    second_line: txt[2]
                                }
                                var satellite = new Orb.SGP4(tle);
                                var date = new Date();
                                var current_latlng = satellite.latlng(date);

                                var marker = L.marker(new L.LatLng(current_latlng.latitude, current_latlng.longitude), {
                                    title: 'ISS',
                                    icon: L.icon({
                                        iconUrl: '<?= plugin_dir_url(__FILE__); ?>symbols/svgicons/83-2.svg',
                                        iconSize: [24, 24],
                                        iconAnchor: [12, 12]
                                    })
                                }).addTo(iss_map_<?= $guid; ?>);

                                var popup_content = '<div>' + moment(date).fromNow() + '</div>' +
                                    '<div class="iss_text_bold">' + 'ISS' + '</div>';

                                <?php if (strtolower($args['short_details']) !== "yes") { ?>
                                popup_content += '<div>' + moment(date).format("LLL") + '</div>' +
                                    '<br/>' +
                                    '<div><b><?= __('Speed', 'iss-aprs-plugin'); ?></b>: ' + current_latlng.velocity.toFixed(2) + ' ' + current_latlng.unit_keywords.split(' ')[2] + '</div>' +
                                    '<div><b><?= __('Altitude', 'iss-aprs-plugin'); ?></b>: ' + current_latlng.altitude.toFixed(2) + ' ' + current_latlng.unit_keywords.split(' ')[1] + '</div>' +
                                    '<div>' +
                                    '<b><?= __('Frequency', 'iss-aprs-plugin'); ?></b>: ' +
                                    '145.825 MHz (APRS)' +
                                    '</div>';
                                <?php } ?>

                                marker.bindPopup(popup_content)
                                iss_map_markers_<?= $guid; ?>.push(marker);

                                marker = L.circle(new L.LatLng(current_latlng.latitude, current_latlng.longitude), {
                                    radius: 1931200, // 1200 miles, https://www.qsl.net/ah6rh/am-radio/spacecomm/getting-started-iss.html
                                    opacity: 0.2,
                                    stroke: false
                                }).addTo(iss_map_<?= $guid; ?>);
                                iss_map_markers_<?= $guid; ?>.push(marker);

                                var polyline_coordinates = [],
                                    previous_lng = null,
                                    previous_lat = null,
                                    polyline;

                                for (var step = 0; step <= 5400; step += 30) {
                                    var step_date = moment(date).add(step, 'seconds');
                                    var step_latlng = satellite.latlng(step_date.toDate());
                                    var latlng = new L.LatLng(step_latlng.latitude, step_latlng.longitude);

                                    if (previous_lng !== null && previous_lat !== null) {
                                        if (previous_lng > 0 && step_latlng.longitude < 0 && Math.abs(step_latlng.longitude - previous_lng) > 100) {
                                            polyline = L.polyline(polyline_coordinates, {
                                                color: 'red',
                                                opacity: 1.0,
                                                weight: 1
                                            }).addTo(iss_map_<?= $guid; ?>);
                                            iss_map_markers_<?= $guid; ?>.push(polyline);

                                            polyline_coordinates = [];
                                        } else
                                            polyline_coordinates.push(latlng);
                                    } else
                                        polyline_coordinates.push(latlng);

                                    previous_lng = step_latlng.longitude;
                                    previous_lat = step_latlng.latitude;
                                }

                                polyline = L.polyline(polyline_coordinates, {
                                    color: 'red',
                                    opacity: 1.0,
                                    weight: 1
                                }).addTo(iss_map_<?= $guid; ?>);
                                iss_map_markers_<?= $guid; ?>.push(polyline);
                            }
                        };
                        iss_xhttp.send();
                    }

                    iss_updating_<?= $guid; ?> = false;
                    <?php if (strtolower($args['show_filters']) === "yes") { ?>
                    document.getElementById('refresh_<?= $guid; ?>').removeAttribute('disabled');
                    <?php } ?>
                    document.getElementById('iss_map_overlay_<?= $guid; ?>').style.display = 'none';
                    document.getElementById('iss_map_overlay_<?= $guid; ?>').removeEventListener('wheel', prevent_scroll_<?= $guid; ?>);
                };
                var xhttp_params = {
                    _ajax_nonce: "<?= wp_create_nonce('nonce-name');?>",
                    action: 'iss_data',
                    get: 'map',
                };
                <?php if (strtolower($args['show_filters']) === "yes") { ?>
                if (document.getElementById('from_<?= $guid; ?>').value) {
                    xhttp_params.from = moment.utc(new Date(document.getElementById('from_<?= $guid; ?>').value).toUTCString()).format('YYYY-MM-DD HH:mm:ss');
                }
                if (document.getElementById('call_sign_<?= $guid; ?>').value) {
                    xhttp_params.call_sign = document.getElementById('call_sign_<?= $guid; ?>').value;
                }
                <?php } else { ?>
                xhttp_params.from = iss_map_from_<?= $guid; ?>.length > 0 ? moment.utc(new Date(iss_map_from_<?= $guid; ?>).toUTCString()).format('YYYY-MM-DD HH:mm:ss') : '';
                <?php } ?>
                xhttp.send(new URLSearchParams(xhttp_params).toString());
            }

            <?php if (strtolower($args['show_filters']) === "yes") { ?>
            function iss_map_reset_<?= $guid; ?>() {
                <?php if($args['from']) { ?>
                iss_map_from_<?= $guid; ?> = moment.utc('<?=  date('Y-m-d H:i:00', strtotime($args['from'])); ?>').local().format("YYYY-MM-DD HH:mm:ss");
                <?php  } ?>

                document.getElementById('call_sign_<?= $guid; ?>').value = '';
                window.location.hash = '';
                document.getElementById('from_<?= $guid; ?>').value = iss_map_from_<?= $guid; ?> && iss_map_from_<?= $guid; ?>.length > 0 ? iss_map_from_<?= $guid; ?> : '';
            }
            <?php } ?>

            function get_hash_parts_<?= $guid; ?>() {
                var hash = location.hash.substring(1),
                    dict = {};

                if (hash.length > 0) {
                    hash.split('&').map(function (item) {
                        var pair = item.split('=');
                        if (pair.length === 2) {
                            dict[pair[0]] = pair[1];
                        }
                    });
                }

                return dict;
            }

            function get_hash_<?= $guid; ?>(dict) {
                var hash = '#', parts = [],
                    keys = Object.keys(dict);

                if (keys.length > 0) {
                    keys.forEach(function (key) {
                        parts.push([key, dict[key]].join('='))
                    });
                }

                return hash + parts.join('&');
            }

            document.addEventListener("DOMContentLoaded", function (event) {
                moment.locale("<?=get_locale();?>");

                <?php if($args['from']) { ?>
                iss_map_from_<?= $guid; ?> = moment.utc('<?=  date('Y-m-d H:i:00', strtotime($args['from'])); ?>').local().format("YYYY-MM-DD HH:mm:ss");
                <?php  } ?>

                iss_map_<?= $guid; ?> = L.map('iss_map_<?= $guid; ?>').setView(JSON.parse("<?= json_encode(array_map(function ($float) {
                    return floatval($float);
                }, explode(",", $args['map_center']))); ?>"), <?= $args['map_zoom'] && is_numeric($args['map_zoom']) ? $args['map_zoom'] : 5; ?>);

                iss_map_<?= $guid; ?>.setMinZoom(1);

                var southWest = new L.latLng(-135, -225),
                    northEast = new L.latLng(135, 225);
                var bounds = new L.latLngBounds(southWest, northEast);
                iss_map_<?= $guid; ?>.setMaxBounds(bounds);
                iss_map_<?= $guid; ?>.on('drag', function () {
                    iss_map_<?= $guid; ?>.panInsideBounds(bounds, {animate: false});
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a target="_blank" href="https://diy.manko.pro">UR5WKM</a> | © <a target="_blank" href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(iss_map_<?= $guid; ?>);

                <?php if (strtolower($args['show_filters']) === "yes") { ?>
                var hash_dict = get_hash_parts_<?= $guid; ?>();

                if (hash_dict['call_sign'] !== undefined) {
                    document.getElementById('call_sign_<?= $guid; ?>').value = hash_dict['call_sign'];
                }

                document.getElementById('from_<?= $guid; ?>').value = iss_map_from_<?= $guid; ?> && iss_map_from_<?= $guid; ?>.length > 0 ? iss_map_from_<?= $guid; ?> : '';
                var from_date = new Pikaday({
                    field: document.getElementById('from_<?= $guid; ?>')
                });

                document.getElementById('call_sign_<?= $guid; ?>').onkeyup = function (e) {
                    var hash_dict = get_hash_parts_<?= $guid; ?>();
                    var call_sign = document.getElementById('call_sign_<?= $guid; ?>').value.trim();
                    if (call_sign.length > 0) {
                        hash_dict['call_sign'] = call_sign
                    } else {
                        if (hash_dict['call_sign'] !== undefined) {
                            delete hash_dict['call_sign'];
                        }
                    }
                    window.location.hash = get_hash_<?= $guid; ?>(hash_dict);
                };
                <?php } ?>

                <?php if ($args['map_header']) { ?>
                document.getElementById('iss_map_header_box_<?= $guid; ?>').style.display = 'block';
                <?php } ?>

                <?php if (strtolower($args['show_filters']) === "yes") { ?>
                document.getElementById('iss_map_filters_<?= $guid; ?>').style.display = 'block';
                <?php } ?>

                iss_map_reload_data_<?= $guid; ?>();
                iss_interval_<?= $guid; ?> = setInterval(function () {
                    iss_map_reload_data_<?= $guid; ?>();
                }, iss_interval_ms_<?= $guid; ?>);
            });
        </script>
        <?php
        $html = ob_get_clean();

        return $html;
    }

    public function ajax_data()
    {
        header("Content-type:application/json");

        $frontend_url = get_option('iss_frontend_url');
        $api_url = trim($frontend_url, '/') . '/api.php';
        $api_key = get_option('iss_api_key');

        $get = filter_var($_POST['get'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $call_sign = filter_var($_POST['call_sign'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $from = filter_var($_POST['from'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $params = array(
            'key' => $api_key,
            'get' => $get,
        );

        if ($call_sign) $params['call_sign'] = $call_sign;
        if ($from) $params['from'] = $from;

        $request_url = $api_url . '?' . http_build_query($params);

        if (!$frontend_url || !$api_key) {
            echo json_encode(array(
                "error" => "Invalid request"
            ));
            wp_die();
        }

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $request_url);
        curl_setopt($handler, CURLOPT_HEADER, FALSE);
        curl_setopt($handler, CURLINFO_HEADER_OUT, FALSE);
        curl_setopt($handler, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($handler, CURLOPT_MAXREDIRS, 10);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($handler, CURLOPT_TIMEOUT, 30);
        curl_setopt($handler, CURLOPT_USERAGENT, "WordPress at " . get_home_url());
        $result = curl_exec($handler);
        $http_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
        curl_close($handler);

        if (!$result) {
            http_response_code($http_code);
            echo json_encode(array(
                "code" => $http_code,
                "error" => "No response from remote API"
            ));
            wp_die();
        }

        $json = json_decode($result);

        if (!$json) {
            http_response_code(500);
            echo json_encode(array(
                "code" => $http_code,
                "error" => "Invalid remote API response",
                //"url" => $request_url,
                //"raw" => $result
            ));
            wp_die();
        }

        echo json_encode($json);

        wp_die();
    }
}

$ISS_APRS_Plugin = new ISS_APRS_Plugin();