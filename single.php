<?php
/**
 * The Template for displaying all single posts.
 *
 * @package ProGo
 * @subpackage BookIt
 * @since BookIt 1.0
 */

get_header(); ?>
        <div id="container" class="container_12">
			<div id="main" role="main" class="grid_7">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div class="entry">
						<?php the_content(); ?>
					</div><!-- .entry -->

					<div class="entry-utility">
            <div class="alignleft">
				<?php progo_posted_on(); ?><br />
				<?php if ( count( get_the_category() ) ) : ?>
					<span class="cat-links">
						<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'progo' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
					</span>
				<?php endif; ?>
				<?php edit_post_link( __( 'Edit Post', 'progo' ), ' : ', '' ); ?>
                </div>
                <div class="alignright">
 <?php if (function_exists('sharethis_button')) { sharethis_button(); } ?>
 </div>
			</div><!-- .entry-utility -->
				</div><!-- #post-## -->
<?php endwhile; // end of the loop. ?>

			</div><!-- #main -->
<?php get_sidebar('blog'); ?>
		</div><!-- #container -->
<?php get_footer(); ?>
