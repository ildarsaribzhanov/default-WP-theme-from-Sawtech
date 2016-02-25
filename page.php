<?php /* Template name: Страница */
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

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<h1 class="title_in"><?php the_title(); ?></h1>
					<div class="b_inser_cont"><?php the_content(); ?></div>
				<?php endwhile;
				else : ?>
					<div>
						<h3 class="title_in">Не найдено</h3>
						<div class="b_inser_cont">К сожалению, по вашему запросу ничего не найдено.</div>
					</div>
				<?php endif; ?>

			</div>

		</div>
		<!--main end-->


	</div>
	<!--band end-->

	<div class="b_clear"></div>
</div>

<?php require('footer.php'); ?>

