<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

$com_info = array(
    'menu_name' => lang('LiqPay', 'payment_method_liqppay'), // Menu name
    'description' => lang('Метод оплаты LiqPay', 'payment_method_liqppay'),            // Module Description
    'admin_type' => 'window',       // Open admin class in new window or not. Possible values window/inside
    'window_type' => 'xhr',         // Load method. Possible values xhr/iframe
    'w' => 600,                     // Window width
    'h' => 550,                     // Window height
    'version' => '0.1',             // Module version
    'author' => 'dev@imagecms.net'  // Author info
);

/* End of file module_info.php */