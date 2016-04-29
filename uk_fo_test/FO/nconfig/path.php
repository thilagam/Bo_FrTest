<?

//root path
define('ROOT_PATH','/home/sites/site7/web/');


//spec file path
define('SPEC_FILE_PATH','/home/sites/site7/web/FO/client_spec');

//corrector spec file path
define('CORRECTOR_SPEC_FILE_PATH','/home/sites/site7/web/FO/correction_spec');

//poll spec file path
define('POLL_SPEC_FILE_PATH','/home/sites/site7/web/FO/poll_spec');

//resources path
define('RESOURCES_PATH','/home/sites/site7/users/');

//html cache path
define('HTML_PATH','/home/sites/site7/html/');

//VDS cache path
define('SSD_CACHE_PATH','/home/sites/site7/data_s117/');

// Set application root path

define('APP_PATH_ROOT', ROOT_PATH. 'FO/');

// Set environment root path
define('ENV_PATH_ROOT',  ROOT_PATH);

// Set model root path
define('MODEL_PATH_ROOT', ROOT_PATH . 'FO/models/');

// Set the view script root path
define('SCRIPT_VIEW_PATH',  ROOT_PATH . 'FO/views/scripts/');

// Set the versions script root path
define('SCRIPT_VERSION_PATH',  ROOT_PATH . 'FO/views/versions/');

// Set the view compile root path
define('COMPILE_PATH',  ROOT_PATH . 'FO/views/compile/');

// Set the view cache root path
define('CACHE_PATH',  ROOT_PATH . 'FO/views/cache/');

// Set the data path
define('DATA_PATH',  RESOURCES_PATH . 'xmldb_editplace/');

// Set the library path
define('LIB_PATH',  ROOT_PATH . 'library/');

// Set the library path
define('SMARTY_PATH',  ROOT_PATH . 'library/Smarty/');

// Set the config path
define('CONFIG_PATH',  ROOT_PATH . 'FO/nconfig/');

 // Set image path to use in views
define('IMAGE_PATH_ROOT', '/FO/images');

// Set FO root path to use in views
define('FO_ROOT_FOLDER', '/FO');

//CUSTOM SCRIPT PATH BY ARUN
define("CUSTOM_SCRIPT_PATH",APP_PATH_ROOT."nlibrary/script/");


// Set include path
set_include_path( MODEL_PATH_ROOT . PATH_SEPARATOR . ENV_PATH_ROOT . PATH_SEPARATOR . APP_PATH_ROOT);
