<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
  |--------------------------------------------------------------------------
  | DETERMINES CMS IS INSTALLED OR NOT
  |--------------------------------------------------------------------------
  |
  |  This value changing due installation of system. So this file
  |  needs write permittion
  |
 */
$config['is_installed'] = TRUE;


/*
  |--------------------------------------------------------------------------
  | TIMEZONE
  |--------------------------------------------------------------------------
  |
  |  This value will be passed to date_default_timezone_set() function
  |
 */
$config['default_time_zone'] = 'Europe/Kiev';


/*
  |--------------------------------------------------------------------------
  | POSSIBLE LOCATIONS OF MODULES
  |--------------------------------------------------------------------------
  |
  |  Modules can be located in different folders (but always somewere in
  |  application folder. This options stands for logical separation of
  |  module groups.
  |
 */
$config['modules_locations'] = [
    //'modules_shop',
    'modules'
];


/*
  |--------------------------------------------------------------------------
  | PROFILER TOOLBAR
  |--------------------------------------------------------------------------
  |
  |  Shows information about memory usage, queries...
  |  (but Propel has different connection - it has own queries logger)
  |
 */
$config['enable_profiler'] = false;


/*
  |--------------------------------------------------------------------------
  |  MABILIS TEMPLATES CONFIGS
  |--------------------------------------------------------------------------
  |
  |  Find explanations for this configs in Mabilis lib
  |
 */
$config['tpl_compile_path'] = PUBPATH . 'system/cache/templates_c/';
$config['tpl_force_compile'] = FALSE;
$config['tpl_compiled_ttl'] = 84600;
$config['tpl_compress_output'] = TRUE;
$config['tpl_use_filemtime'] = TRUE;


/*
  |--------------------------------------------------------------------------
  |  USING OLD CART VERSION
  |--------------------------------------------------------------------------
  |
  |  Depracated since ~4.4. Should be always false.
  |
 */
$config['use_deprecated_cart_methods'] = FALSE;


/*
  |--------------------------------------------------------------------------
  |  COMPOSER AUTOLOAD FILE
  |--------------------------------------------------------------------------
  |
  |  Path to autogenerated composer autoload file
  |
 */
$config['composer_autoload'] = APPPATH . 'third_party/autoload.php';


/*
  |--------------------------------------------------------------------------
  | CMS hooks
  |--------------------------------------------------------------------------
  |
  |  This is library with hooks (like framework hooks) for cms
  |  It is not using often now.
  |
 */
$config['rebuild_hooks_tree'] = FALSE;