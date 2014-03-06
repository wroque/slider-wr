<?php

/**
 *
 * Plugin Name: Slider Manager
 * Plugin URI: none
 * Description: Simple Slider Manager.
 * Version: 0.1
 * Author: Wladimir Roque
 * Author URI: https://github.com/wroque/
 * License: GPL
 *
 * */
require_once 'includes/classes.php';

function slider_wr_install() {
    global $wpdb;
    try {
        $schema = new Schema($wpdb);
        $schema->install();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function slider_wr_desinstall() {
    global $wpdb;
    try {
        $schema = new Schema($wpdb);
        $schema->data = array('item', 'slider');
        $schema->desinstall();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function slider_wr_css_js() {
    wp_register_style('style', plugins_url('/css/style.css', __FILE__));
    wp_enqueue_style('style');

    wp_register_style('bxslider', plugins_url('/css/jquery.bxslider.css', __FILE__));
    wp_enqueue_style('bxslider');

    wp_enqueue_script('bxslider_js', plugins_url('/js/jquery.bxslider.js', __FILE__), array('jquery'));
    wp_enqueue_script('bxslider_js');

    wp_enqueue_script('elita_validate', plugins_url('/js/elita.validate.js', __FILE__));
    wp_enqueue_script('elita_validate');
}

function slider_add_menu() {
    if (function_exists('add_menu_page')) {
        add_menu_page('Sliders', 'Sliders', 2, __FILE__, 'slider_wr_admin');
    }
}

function slider_wr_admin() {
    global $wpdb;
    $slider = new Slider($wpdb);
    if (isset($_POST['slider'])) {
        $slider->id = $_POST['slider']['ID'];
        $slider->save($_POST['slider']);
    }
    $sql = "select slider.ID, slider.slider_name, slider.created, count(item.ID) as cant"
            . " from {$wpdb->prefix}sliders as slider left join {$wpdb->prefix}items as item"
            . " on (slider.ID = item.slider_id) group by slider.ID";
    $sliders = $wpdb->get_results($sql);
    ob_start();
    require_once 'template/admin_slider.php';
    $out = ob_get_clean();
    $out .= item_wr_admin($sliders);

    echo <<<EOF
    <div class="wrap">

    <div id="alert"></div>

    <div id="tabs_container">
        <ul id="tabs">
            <li class="active"><a href="#tab1">Slider</a></li>
            <li><a href="#tab2">Item</a></li>
        </ul>
    </div>
    {$out}
    </div>
</div>
EOF;
}

function slider_wr_ajax() {
    global $wpdb;
    $data = null;
    $slider = new Slider($wpdb);
    if (isset($_POST['ID'])) {
        switch ($_POST['option']) {
            case 'get': $data = slider_wr($_POST['ID']);
                break;
            case 'edit': $data = $slider->find($_POST['ID']);
                break;
            case 'delete': $slider->delete($_POST['ID']);
                $data = $_POST['ID'];
                break;
        }
    }
    if (is_object($data)) {
        echo json_encode($data);
    } else {
        echo $data;
    }
    die();
}

function slider_wr($arg = null, $w = '278', $h = '65') {
    global $wpdb;
    if (is_numeric($arg)) {
        $arg = "slider.ID={$arg}";
    } else {
        $arg = "slider.slider_name='{$arg}'";
    }
    $sql = "select item.*, slider.slider_atts"
            . " from {$wpdb->prefix}items as item left join {$wpdb->prefix}sliders as slider"
            . " on (item.slider_id = slider.ID) where {$arg} order by item.item_order ASC";

    $items = $wpdb->get_results($sql);
    ob_start();
    require_once 'template/slider.php';
    $out = ob_get_clean();
    return $out;
}

function item_wr_admin($sliders) {
    global $wpdb;
    $item = new Item($wpdb);
    if (isset($_POST['item'])) {
        $item->id = $_POST['item']['ID'];
        $item->save($_POST['item']);
    }
    $sql = "select item.*, slider.slider_name"
            . " from {$wpdb->prefix}items as item left join {$wpdb->prefix}sliders as slider"
            . " on (item.slider_id = slider.ID) order by item.slider_id ASC";
    $items = $wpdb->get_results($sql);
    ob_start();
    require_once 'template/admin_item.php';
    $out = ob_get_clean();
    return $out;
}

function item_wr_ajax() {
    global $wpdb;
    $data = array();
    $Item = new Item($wpdb);
    if (isset($_POST['ID'])) {
        switch ($_POST['option']) {
            case 'edit':
                $data = $Item->find($_POST['ID']);
                break;
            case 'delete':
                $Item->delete($_POST['ID']);
                $data = $_POST['ID'];
                break;
        }
    }
    if (is_object($data)){
        echo json_encode($data);
    }
    die();
}

if (function_exists('add_action')) {

    add_action('activate_slider-wr/slider-wr.php', 'slider_wr_install');
    add_action('deactivate_slider-wr/slider-wr.php', 'slider_wr_desinstall');

    add_action('admin_menu', 'slider_add_menu');

    add_action('admin_init', 'slider_wr_css_js');
    add_action('init', 'slider_wr_css_js');

    add_action('wp_ajax_item_wr_admin', 'item_wr_admin');
    add_action('wp_ajax_nopriv_item_wr_admin', 'item_wr_admin');

    add_action('wp_ajax_slider_wr_ajax', 'slider_wr_ajax');
    add_action('wp_ajax_nopriv_slider_wr_ajax', 'slider_wr_ajax');

    add_action('wp_ajax_item_wr_ajax', 'item_wr_ajax');
    add_action('wp_ajax_nopriv_item_wr_ajax', 'item_wr_ajax');
}
