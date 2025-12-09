<?php


/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u217525907_a31y5' );

/** Database username */
define( 'DB_USER', 'u217525907_s0lIy' );

/** Database password */
define( 'DB_PASSWORD', 'akdrBwEIWS' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '7y_;S6)F ~]}Aqs5{JwJG7wiJ5^`K&KxeiC5en,pk#GYLmoX_=^mA:2|-WtY8Fmt' );
define( 'SECURE_AUTH_KEY',   'm.e^C<1bI)%e~ER o/?%%KfY.0+*)jAL(xyU+HX v.K8WVruj=IIQBH+MM Qf)T&' );
define( 'LOGGED_IN_KEY',     '|z7E+RmA8DL)&.Yqco*Ns,POlfb&W2[N1*3Xvow@M*PuA/.!k~^|Q`Ly~TQwyi.]' );
define( 'NONCE_KEY',         'lol4t2#sZso<zJoSS)!rl>V):RmWSd>)4Y8/^X@ieO,=jB$[-Hd`Xr KmfZhKU:{' );
define( 'AUTH_SALT',         '59o/$wvwflZhPX>)CsR/p{93Y.D7_r/kKD(a;&SaLI|T&-~oyW<*5yPg~<j*F%xQ' );
define( 'SECURE_AUTH_SALT',  'B3UH!G?Y$:X)-u>zt$TP:o[467t%Yq`WWu2KhPH>3Pj9&Ng^>r|jO[<VZ6CbIp8E' );
define( 'LOGGED_IN_SALT',    'H3BV<CMdX;Q`M^sF)vn`Y<m<D#i$i_R}?OJ|=.#s!dE?7gU6YKn XI#4*m6%156s' );
define( 'NONCE_SALT',        '2B~ip/[PKMelT-$fEcTR|Exe Few1wqQ8!wMsmDfh@;a;JW4G 0Iv_dV^om8Q~&r' );
define( 'WP_CACHE_KEY_SALT', 'e~To@&a1Zg+Gpr|x<!2]xKZMuY XcDgVm(^Yx*qwrCRHR^GNuf_cKefe&zq8d&CA' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', 'f40945862da138d0776341000c58186d' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
