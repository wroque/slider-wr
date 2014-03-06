<?php

/**
 * Description of classes
 *
 * @author wroque
 */

class Schema {
    private $wpdb;

    public $data = array(
            'slider' =>
                array(
                    'key' => array(
                        'ID' => 'int(11) NOT NULL AUTO_INCREMENT'
                    ),
                    'name' => 'varchar(150)',
                    'atts' => 'varchar(250)',
                    'status' => 'tinyint DEFAULT 1'
                ),
            'item' =>
                array(
                    'key' => array(
                        'ID' => 'int(11) NOT NULL AUTO_INCREMENT'
                    ),
                    'key_2' => array(
                        'slider_id' => 'int(11) DEFAULT NULL'
                    ),
                    'link' => 'varchar(250)',
                    'link_img' => 'varchar(250)',
                    'title' => 'varchar(150)',
                    'target' => 'varchar(50)',
                    'description' => 'text',
                    'status' => 'tinyint DEFAULT 1',
                    'order' => 'int(11)'
                )
        );

    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
    }

    public function install() {
        if (empty($this->data)) {
            throw new Exception('Schema empty.');
        }
        foreach ($this->data as $name => $schema) {
            $sql = 'CREATE TABLE ' . $this->wpdb->prefix . $name . 's (';
            foreach ($schema as $field => $type) {
                if (strpos($field, 'key') !== false) {
                    $sql .= sprintf("`%s` %s, ", key($type), current($type));
                } else {
                    $sql .= "`{$name}_{$field}` {$type}, ";
                }
            }
            $sql .= 'created datetime DEFAULT NULL,';
            $sql .= "PRIMARY KEY (`ID`));";
            if (!$this->wpdb->query($sql)) {
                throw new Exception('Creation failure.');
            }
        }
    }

    public function desinstall() {
        if (empty($this->data)) {
            throw new Exception('Name empty.');
        }
        foreach ($this->data as $name) {
            $name = $this->wpdb->prefix . $name;
            $sql = "DROP TABLE {$name}s";
            if (!$this->wpdb->query($sql)) {
                throw new Exception('Delete failure.');
            }
        }
    }

}

class Model {

    public $id;
    public $name;
    public $data = array();
    
    private $wpdb;

    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
    }

    public function save($data) {
        unset($data['id']);
        $name = $this->wpdb->prefix . $this->name;
        if (empty($this->id)) {
            $format = self::format_data_save($data);
            $sql = sprintf("insert into `%ss` (`%s`) values ('%s')", $name, $format['fields'], $format['values']);
        } else {
            $format = self::format_data_edit($data);
            $sql = sprintf("update `%ss` set %s where `ID`=%d", $name, $format, $this->id);
        }
        $this->wpdb->query($sql);
    }

    protected static function format_data_edit(array $data) {
        $arr = array();
        foreach ($data as $field => $value) {
            array_push($arr, "`{$field}`='{$value}'");
        }
        $str = implode(',', $arr);
        return $str;
    }

    protected static function format_data_save(array $data) {
        $arr = array();
        $data['created'] = date('Y-m-d H:i:s');
        $arr['fields'] = implode('`,`', array_keys($data));
        $arr['values'] = implode("','", array_values($data));
        return $arr;
    }

    public function delete($id = null) {
        if (isset($id)) {
            $name = $this->wpdb->prefix . $this->name;
            $sql = sprintf("delete from %ss where `ID`=%d", $name, $id);
            $this->wpdb->query($sql);
        }
    }

    public function find($id = null, $fields = '*') {
        $name = $this->wpdb->prefix . $this->name;
        $sql = sprintf('SELECT %s FROM %ss WHERE ID=%d', $fields, $name, $id);
        $this->data = $this->wpdb->get_results($sql);
        return current($this->data);
    }

}
