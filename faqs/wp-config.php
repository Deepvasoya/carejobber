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
define( 'DB_NAME', 'u576912955_gavVQ' );

/** Database username */
define( 'DB_USER', 'u576912955_OOgmZ' );

/** Database password */
define( 'DB_PASSWORD', 'tyyroEEMqL' );

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
define( 'AUTH_KEY',          'fK)YJ>_|Oqu&Jv,-@M} WuyuI07`&gr?UF!ig9DiuWrE5<U7/5E j=>L,fg.,,M^' );
define( 'SECURE_AUTH_KEY',   'r<=Czhm*eO^9[AgskQUr ovZ1qzZdFe:qdI0D%0KZ_K!wkvygwRjAX[jeg,yv.Z/' );
define( 'LOGGED_IN_KEY',     'c>Os$eq97N_HrhjUKGNbg<,~@nrb7Xwe;2MYm8xL9tp,S9fqpVlG_`[08zwzIP&$' );
define( 'NONCE_KEY',         'G?xoDrdxW}Y%JiP6yquQ a~/ilr>RCzKk)nB(H:P@[GPt.(RwMpPy4__!%sI/ff:' );
define( 'AUTH_SALT',         'pXrUkLie<)L26zUO` tmd<*Gv!a[So@#]Llez;*bVw>DmAaM:a@3dHDJraIg+%>S' );
define( 'SECURE_AUTH_SALT',  'Y&YCRezlE`6:]Xm@)SqluFr{]o-YwJxOV8Yja 4+wt C:jg4y`0I-lk~BTf#k.BV' );
define( 'LOGGED_IN_SALT',    ';-TE>Gp]jbB[ge-V&r56{IT//tQuJ:]0$DK|o_T@a*!hZ_H]#ROtGjPN^*}+)XV~' );
define( 'NONCE_SALT',        'nbD,hKo5E.MX%m[8YsK5zrjM*Io g;S+q~?K!*p(m*w^_JS9GPL_Y#e%32XWvF+)' );
define( 'WP_CACHE_KEY_SALT', 'N)!7Ym5A@Y8z/&?H}UR&&QSV)ZiFpmUlA{+|:#9$tk6+^^o2M.~I-M[:kF284=3~' );


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
define( 'COOKIEHASH', '9e07a46dc3188d130ada2351e0481b5e' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
