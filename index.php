<?php
require( './wp-blog-header.php' );
require_once( 'header.php' );
?>

</head>

<body>

<!-- FB likes -->
<div id="fb-root"></div>
<script>(function (d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s);
		js.id = id;
		js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&appId=571497516273656&version=v2.0";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
<?php require( 'header_page.php' ); ?>

<!--main begin-->
<div class="b_maincontent">
	
	<?php require( 'sidebar.php' ); ?>

	<div class="b_left_sidebar">
		<?php query_posts( array( 'cat' => 3, 'posts_per_page' => 1000 ) ); ?>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<p><?php the_title(); ?></p>
			<div><?php the_content(); ?></div>

		<?php endwhile; ?>
		<?php else : ?>
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
</div>
<?php require( 'footer.php' ); ?>
</body>
</html>