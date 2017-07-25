<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'visita');

/** MySQL database username */
define('DB_USER', 'visita');

/** MySQL database password */
define('DB_PASSWORD', 'visita');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define( 'WP_HOME', 'http://wp.visita.dev' );
define( 'WP_SITEURL', 'http://wp.visita.dev' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '$5/&htx.CMof$J|bY5{D-Qo+^3)2uUC|v.Nm|6Xdzl+o2hv(sQ:3%ykO2.&G,~V1');
define('SECURE_AUTH_KEY',  'l[:cb])}&_VdiuBXMq0`7(]_gcZlCfa#>MD[tKWdg6H-MaH}f>f9*|#:q//](`LE');
define('LOGGED_IN_KEY',    'Gx2#>tv(@$uzK-YkSx7]f~YIf/6yc;f+y3K/qq i9PH~8IuPaqRs&r$PeZJxk8QJ');
define('NONCE_KEY',        '{ZyZ6fYd~[KJ=8nftWABeqjR0NJ{:fpkbY7n+n@)-T|JAN?~scM^V*ei,WgX_(ha');
define('AUTH_SALT',        'wEMjZtE+ZzY1KS5D|QSo3NKEW3|%(J.M8PEjLh_TzkW*5+?:s<p.z5?-B+-Ed Ln');
define('SECURE_AUTH_SALT', ';rJQj?+[-E-|D@|KA ?-`9iqZ*&RhiIa^/+y3lVze%<5Unc7wtt`~a0hdF|}:#J}');
define('LOGGED_IN_SALT',   'jZ-Bdbq:KA%tkBC[+<gG!x2s{zlHC|`&=s$w/>8zmiWk*R,q;/o}[p/0,$ LuLO6');
define('NONCE_SALT',       '-(rwn20Mw8a?T|,,7b8|/m_a)`fQHL)JPZRR~z8X7XmWv@hJ^SIW,Cu<KJlX-gtM');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'visit_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_CACHE', false);
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('EMPTY_TRASH_DAYS', 15);
define('DISABLE_WP_CRON', true);
define('WP_DEBUG_DISPLAY', false);
define('WORKING_LOCALLY', true);

define('WP_HOME', 'http://wp.visita.dev/' );
define('WP_SITEURL', 'http://wp.visita.dev/' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
