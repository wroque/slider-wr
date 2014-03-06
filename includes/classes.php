<?php
/**
 * Description of classes
 *
 * @author wroque
 */
require_once 'core.php';

class Slider extends Model { 
    
    public $name = 'slider';

    public function save($data) {
        $data['slider_atts'] = array_filter($data['slider_atts']);
        $data['slider_atts'] = json_encode($data['slider_atts']);
        parent::save($data);
    }
}

class Item extends Model {

    public $name = 'item';

    public $validation = array(
        'extensions' =>  array('png', 'jpg', 'jpeg', 'gif'),
        'size' => 10485760
    );

    public function save($data) {
        if (empty($data['link_img'])) {
            try {
				if (strpos($data['item_link'], '#') === false) {
					$parsed = parse_url($data['item_link']);
					if (empty($parsed['scheme'])) {
						$data['item_link'] = 'http://' . ltrim($data['item_link'], '/');
					}
				}
                if (!empty($_FILES['file']['name'])) {
                    $data['item_link_img'] = $this->upload();
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        parent::save($data);
    }

    public function upload() {
        $info = pathinfo($_FILES['file']['name']);
        if (empty($info['filename'])) {
            throw new Exception('File not uploaded.');
        }
        if (!in_array($info['extension'], $this->validation['extensions'])) {
            throw new Exception('Invalid file.');
        }
        if ($_FILES['file']['size'] > $this->validation['size']) {
            throw new Exception('File exceeds limit.');
        }
        if (!function_exists('wp_handle_upload')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $uploadedfile = $_FILES['file'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if (!$movefile) {
            throw new Exception('Possible file upload.');
        }
        return $movefile['url'];
    }
}
