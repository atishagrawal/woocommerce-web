<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'woocommerce-web' );

/** MySQL database username */
define( 'DB_USER', 'woocommerce-web' );

/** MySQL database password */
define( 'DB_PASSWORD', 'woocommerce-web' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ':$V|TZ4n#}Z!G}MF5iH#[,4$$M0}r9<u:k8Ysz;#b<^ojnG%4a8LCJ=X0Q(.GL}i' );
define( 'SECURE_AUTH_KEY',  'JISnj$?B4uv9yII[7yF11ju`hM&gsgMSbl$XI`H~9u$e`e2RTQx[f4[Qw}uFj8lV' );
define( 'LOGGED_IN_KEY',    'Um73_.BVV`]vAJwm(Ebvd#k6npvm0t9oF,/2Rf0qq$@(9~,vGx{@%MN9Vyu).)z-' );
define( 'NONCE_KEY',        'b+G8mF^E*pQ>8jU/JYI=-;??ye_A4IZ^0sW*Qs91neB?hJUQ=ejT6KSVIt~Xyrp@' );
define( 'AUTH_SALT',        '6Mt:L*T3lD<t+g,Wm>7J<qTeYV5+,gIG6,fSu_hOUouR.JPL&-1x>w^O^&NlbWxb' );
define( 'SECURE_AUTH_SALT', 'Md=r/)/[;-m!6{p)VKEWL4N;,,^T/f@fpIlnwx;oAa>WUx;fOjnPZ6kU>hryR*Z;' );
define( 'LOGGED_IN_SALT',   '/T<B_!x%.Aog}|j$gj`$zL(97nS[d6%[VpE(PnpU}t$n9R2Ae85Rcm6!6-u(}}YE' );
define( 'NONCE_SALT',       'aa??qU/r=aJk$4uXb!oN_v(R(eu+nXxPHM,&SH^UcFpQ2)$a(e_ =g7=K#1|&!5$' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
