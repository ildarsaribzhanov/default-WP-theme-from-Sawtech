<?php /* Themplate name: Название шаблона */
require('./wp-blog-header.php');
require_once('header.php');
?>

<div class="b_wraper">

	<div class="b_band">

		<?php require('header_page.php'); ?>

		<!--main begin-->
		<div class="b_maincontent">

			<?php require('sidebar.php'); ?>

			<div class="b_left_sidebar">

				<?php query_posts(array('cat' => 3, 'posts_per_page' => 1000)); ?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<p><?php the_title(); ?></p>
					<div><?php the_content(); ?></div>

				<?php endwhile; else : ?>
					<div>
						<h3>Не найдено</h3>
						К сожалению, по вашему запросу ничего не найдено.
					</div>
				<?php endif; ?>
				<?php wp_reset_query(); ?>

			</div>

		</div>
		<!--main end-->


	</div>
	<!--band end-->

	<div class="b_clear"></div>
</div>

<?php require('footer.php'); ?>
