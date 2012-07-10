<?php
// ===================================================
// Load database info and local development parameters
// ===================================================
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/local-config.php' );
} else {
	define( 'WP_LOCAL_DEV', false );
	define( 'DB_NAME', $_ENV['OPENSHIFT_APP_NAME'] );
	define( 'DB_USER', $_ENV['OPENSHIFT_DB_USERNAME'] );
	define( 'DB_PASSWORD', $_ENV['OPENSHIFT_DB_PASSWORD'] );
	define( 'DB_HOST', $_ENV['OPENSHIFT_DB_HOST'] );
}

// ========================
// Custom Content Directory
// ========================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

// ================================================
// You almost certainly do not want to change these
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ========================================================================
// Salts, for security
// This is where we define the OpenShift specific secure variable functions
// ========================================================================

require_once('openshift.php');

// Set the default keys to use
$_default_keys = array(
  'AUTH_KEY'          => ' w*lE&r=t-;!|rhdx5}vlF+b=+D>a)R:nTY1Kdrw[~1,xDQS]L&PA%uyZ2:w6#ec',
  'SECURE_AUTH_KEY'   => '}Sd%ePgS5R[KwDxdBt56(DM:0m1^4)-k6_p8}|C:[-ei:&qA)j!X`:7d-krLZM*5',
  'LOGGED_IN_KEY'     => '$l^J?o)!zhp6s[-x^ckF}|BjU4d+(g1as)n/Q^s+k|,ZZc@E^h%Rx@VTm|0|?]6R',
  'NONCE_KEY'         => '#f^JM8d^!sVsq]~|4flCZHdaTy.-I.f+1tc[!h?%-+]U}|_8qc K=k;]mXePl-4v',
  'AUTH_SALT'         => 'I_wL2t!|mSw_z_ zyIY:q6{IHw:R1yTPAO^%!5,*bF5^VX`5aO4]D=mtu~6]d}K?',
  'SECURE_AUTH_SALT'  => '&%j?6!d<3IR%L[@iz=^OH!oHRXs4W|D,VCD7w%TC.uUa`NpOH_XXpGtL$A]{+pv9',
  'LOGGED_IN_SALT'    => 'N<mft[~OZp0&Sn#t(IK2px0{KloRcjvIJ1+]:,Ye]>tb*_aM8P&2-bU~_Z>L/n(k',
  'NONCE_SALT'        => 'u E-DQw%[k7l8SX=fsAVT@|_U/~_CUZesq{v(=y2}#X&lTRL{uOVzw6b!]`frTQ|'
);

// This function gets called by openshift_secure and passes an array
function make_secure_key($args) {
  $hash = $args['hash'];
  $key  = $args['variable'];
  $original = $args['original'];

  $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $chars .= '!@#$%^&*()';
  $chars .= '-_ []{}<>~`+=,.;:/?|';

  // Convert the hash to an int to seed the RNG
  srand(hexdec(substr($hash,0,8)));
  // Create a random string the same length as the default
  $val = '';
  for($i = 1; $i <= strlen($original); $i++){
    $val .= substr( $chars, rand(0,strlen($chars))-1, 1);
  }
  // Reset the RNG
  srand();
  // Set the value
  return $val;
}

// Generate OpenShift secure keys (or return defaults if not on OpenShift)
$array = openshift_secure($_default_keys,'make_secure_key');

// Loop through returned values and define them
foreach ($array as $key => $value) {
  define($key,$value);
}

// ==============================================================
// Table prefix
// Change this if you have multiple installs in the same database
// ==============================================================
$table_prefix  = 'wp_';

// ================================
// Language
// Leave blank for American English
// ================================
define( 'WPLANG', '' );

// ===========
// Hide errors
// ===========
//ini_set( 'display_errors', 0 );
//define( 'WP_DEBUG_DISPLAY', false );

// =================================================================
// Debug mode
// Debugging? Enable these. Can also enable them in local-config.php
// =================================================================
// define( 'SAVEQUERIES', true );
define( 'WP_DEBUG', true );

// ======================================
// Load a Memcached config if we have one
// ======================================
if ( file_exists( dirname( __FILE__ ) . '/memcached.php' ) )
	$memcached_servers = include( dirname( __FILE__ ) . '/memcached.php' );

// ===========================================================================================
// This can be used to programatically set the stage when deploying (e.g. production, staging)
// ===========================================================================================
define( 'WP_STAGE', '%%WP_STAGE%%' );
define( 'STAGING_DOMAIN', '%%WP_STAGING_DOMAIN%%' ); // Does magic in WP Stack to handle staging domain rewriting

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );
