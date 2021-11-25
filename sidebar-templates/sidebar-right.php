<?php
/**
 * The right sidebar containing the main widget area
 *
 * @package Understrap
 */

if( is_shop() || is_cart() || is_checkout() ) {
	$understrap_sidebar = 'shop-sidebar';
} else {
	$understrap_sidebar = 'right-sidebar';
}

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( $understrap_sidebar ) ) {
	return;
}

if( is_archive( 'product_cat' )) {
	return;
}

// when both sidebars turned on reduce col size to 3 from 4.
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<?php if ( 'both' === $sidebar_pos ) : ?>
	<div class="col-md-3 widget-area" id="right-sidebar">
<?php else : ?>
	<div class="col-md-4 widget-area" id="right-sidebar">
<?php endif; ?>
<?php dynamic_sidebar( $understrap_sidebar ); ?>

</div><!-- #right-sidebar -->
