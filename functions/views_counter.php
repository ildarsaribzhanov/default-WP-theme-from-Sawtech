<?php
/*
	* Увеличить счетчик просмотров
	* Получить количество просмотров
*/


/****************************************************************************/
/********************** Увеличить счетчик просмотров поста ******************/
/****************************************************************************/
function count_view_update($id = false)
{
	if (is_single()) {
		if ( ! $id) {
			global $post;
			$id = $post->ID;
		};
		
		$count_view = get_post_meta($id, 'count_view', 1);
		if ($count_view) {
			$count_view++;
		} else {
			$count_view = 1;
		};
		update_post_meta($id, 'count_view', $count_view);
	};
	
	return;
}


add_action('wp_head', 'count_view_update');


/****************************************************************************/
/*********************** Получить количество просмотров *********************/
/****************************************************************************/
function get_count_view($id = false)
{
	if ($id === false) {
		global $post;
		$id = $post->ID;
	};
	$count_view = get_post_meta($id, 'count_view', 1);
	
	return (int)$count_view;
}