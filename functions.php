<?php
    /**
     * UnderStrap functions and definitions
     *
     * @package Understrap
     */

    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    // UnderStrap's includes directory.
    $understrap_inc_dir = 'inc';

    // Array of files to include.
    $understrap_includes = array(
        '/theme-settings.php', // Initialize theme default settings.
        '/setup.php', // Theme setup and custom theme supports.
        '/widgets.php', // Register widget area.
        '/enqueue.php', // Enqueue scripts and styles.
        '/template-tags.php', // Custom template tags for this theme.
        '/pagination.php', // Custom pagination for this theme.
        '/hooks.php', // Custom hooks.
        '/extras.php', // Custom functions that act independently of the theme templates.
        '/customizer.php', // Customizer additions.
        '/custom-comments.php', // Custom Comments file.
        '/class-wp-bootstrap-navwalker.php', // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567.
        '/editor.php', // Load Editor functions.
        '/block-editor.php', // Load Block Editor functions.
        '/deprecated.php', // Load deprecated functions.
    );

    // Load WooCommerce functions if WooCommerce is activated.
    if ( class_exists( 'WooCommerce' ) ) {
        $understrap_includes[] = '/woocommerce.php';
    }

    // Load Jetpack compatibility file if Jetpack is activiated.
    if ( class_exists( 'Jetpack' ) ) {
        $understrap_includes[] = '/jetpack.php';
    }

    // Include files.
    foreach ( $understrap_includes as $file ) {
        require_once get_theme_file_path( $understrap_inc_dir . $file );
    }

    /**
     * Add category before shop page content
     */
    // add_action( 'woocommerce_before_main_content', 'us_woocommerce_before_main_content', 9 );
//     add_action( 'woocommerce_before_shop_loop', 'us_woocommerce_before_shop_loop', 8 );
    function us_woocommerce_before_shop_loop() {

        $term_id   = get_queried_object()->term_id;
        $parent_id = get_queried_object()->parent;

        if ( $term_id == $parent_id ) {
            echo "<ul class='products columns-3'>";
            woocommerce_output_product_categories();
            echo "</ul>";
        }

        if ( $parent_id > 0 ) {
            $term_id = $parent_id;
        }

        if ( $term_id ) {
            echo "<ul class='products columns-3'>";
            woocommerce_output_product_categories( array(
                'parent_id' => $term_id,
            ) );
            echo "</ul>";
        }
        echo "<div class='clearfix'></div>";

    }

    /**
     * Add justify gallery before 
     * woocommerce shop loop content
     * Shop page
     */
    add_action( 'woocommerce_before_shop_loop', 'us_woocommerce_before_shop_loop_jg', 8 );
    function us_woocommerce_before_shop_loop_jg() {
		if( isset( $_GET['cg']) && $_GET['cg'] == 1 ) {
			$cat_args = array(
				'orderby'    => 'name',
				'order'      => 'asc',
				'hide_empty' => true,
			);

		$product_categories = get_terms( 'product_cat', $cat_args );?>

		<div id="justified_gallery" class="justified-category-list js-justifyGallery">
			<?php foreach ( $product_categories as $product_category ) {
				// $thumbnail_id = get_term_meta( $product_category->term_id, 'thumbnail' );
				$thumbnail_id = get_term_meta( $product_category->term_id );

				$thumbnail = wp_get_attachment_image_url( $thumbnail_id, 'large' );

				if ( !$thumbnail ) {
					continue;
				}
			?>

			<a href="<?php echo esc_url( get_term_link( $product_category, 'product_cat' ) ) ?>">

				<?php if ( $thumbnail ): ?>
					<img src="<?php echo esc_url( $thumbnail ) ?>" alt="<?php echo esc_attr( $product_category->name ); ?>" />
				<?php endif;?>

				<div class="contents">
					<?php if ( $product_category->name ): ?>
						<h2><?php echo esc_html( $product_category->name ); ?></h2>
					<?php endif;?>
				</div>
			</a>

		</div>
	
	<?php } } }

            add_action( 'wp_head', function () {
            ?>
		<style>
			.products {
				/* display: flex; */
				margin: 0;
				list-style: none;
			}
		</style>

	<?php
    } );
    
	/**
	 * Set url based query on shop page
	 * http://wp.local/shop/?wcpagination=0 here ?wcpagination=0 can b any number
	 * @param object $wcq
	 * @return object
	 */
    function us_woocommerce_product_query( $wcq ) {
	    if( isset( $_GET['wcpagination'] ) && $_GET['wcpagination'] == 0 ) {
			$wcq->set('posts_per_page', -1 );
		}
		return $wcq;
    }
    add_filter( 'woocommerce_product_query', 'us_woocommerce_product_query' );
	
	/**
	 * Shop page columns
	 * http://wp.local/shop/?nc=2 here ?nc=0 will show 2 columns
	 * @param int $wcq
	 * @return int
	 */
	function us_loop_shop_columns( $nc ) {
	    if( isset( $_GET['nc'] ) && $_GET['nc'] ) {
			$nc = sanitize_text_field( $_GET['nc']);
		}
		return $nc;
    }
    add_action( 'loop_shop_columns', 'us_loop_shop_columns' );
	
	/**
	 * Unset checkout page fields
	 *
	 * @param array $fields
	 * @return array
	 */
	function us_woocommerce_checkout_fields( $fields ) {
		
		unset( $fields['billing']['billing_company']);
		unset( $fields['billing']['billing_country']);
		unset( $fields['billing']['billing_address_1']);
		unset( $fields['billing']['billing_address_2']);
		unset( $fields['billing']['billing_city']);
		unset( $fields['billing']['billing_state']);
		unset( $fields['billing']['billing_postcode']);
		
		unset( $fields['shipping']['shipping_company']);
		unset( $fields['shipping']['shipping_country']);
		unset( $fields['shipping']['shipping_address_1']);
		unset( $fields['shipping']['shipping_address_2']);
		unset( $fields['shipping']['shipping_city']);
		unset( $fields['shipping']['shipping_state']);
		unset( $fields['shipping']['shipping_postcode']);
		
		return $fields;
	}
	add_filter('woocommerce_checkout_fields', 'us_woocommerce_checkout_fields');
	