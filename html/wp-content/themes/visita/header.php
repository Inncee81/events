<?php
/**
* Visita - Header Template
*
* @file header.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/themes/visita/header.php
* @since available since 0.1.0
*/
?>
<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]>--> <html class="no-js" <?php language_attributes(); ?>> <!--[endif]-->
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />

<meta name="theme-color" content="#200d35" />
<meta name="MobileOptimized" content="width" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<?php visita_site_metatags(); ?>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="manifest" href="<?php stylesheet_directory_uri('/manifest.json') ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?php stylesheet_directory_uri('/icons/icon-192x192.png') ?>">
<link rel="apple-touch-icon" href="<?php stylesheet_directory_uri('/icons/apple-icon-72x72.png') ?>" />
<link rel="apple-touch-icon" sizes="72x72" href="<?php stylesheet_directory_uri('/icons/apple-icon-72x72.png') ?>" />
<link rel="apple-touch-icon" sizes="114x114" href="<?php stylesheet_directory_uri('/icons/apple-icon-114x114.png') ?>" />
<link rel="apple-touch-icon" sizes="144x144" href="<?php stylesheet_directory_uri('/icons/apple-icon-144x144.png') ?>" />
<link rel="apple-touch-icon" sizes="180x180" href="<?php stylesheet_directory_uri('/icons/apple-icon-180x180.png') ?>" />

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
