<div class="b_header">
	<?php wp_nav_menu(array(
		'menu'       => 'name_menu',
		'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'menu_class' => 'menu',
		'menu_id'    => 'menu_id',
	)); ?>
</div>