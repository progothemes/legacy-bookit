<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 *
 * @package ProGo
 * @subpackage BookIt
 * @since BookIt 1.0
 */
?>
<div id="side" class="grid_4 prefix_1">
<?php
/* When we call the dynamic_sidebar() function, it'll spit out
 * the widgets for that widget area. If it instead returns false,
 * then the sidebar simply doesn't exist, so we'll hard-code in
 * some default sidebar stuff just in case.
 */

if ( is_page(21) ) {
if ( ! dynamic_sidebar( 'contact' ) ) : ?>
<p>by default we want some widgets to show up...</p>
<?php endif; // end primary widget area
} else {
if ( ! dynamic_sidebar( 'sidebar' ) ) : ?>
<p>by default we want some widgets to show up...</p>
<?php endif; // end primary widget area
}
?>
</div>