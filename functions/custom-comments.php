<?php

/****************************************************************************/
/*
	* Cвой формат вывода комментариев 
	* Получить количество опубликованных комментариев
	* Получить список последних комментариев
	* Убрать обработчик onclik с ссылки ответа
*/
/****************************************************************************/


/****************************************************************************/
/************************** Cвой формат комментариев  ***********************/
/****************************************************************************/

function mytheme_comment($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	switch ($comment->comment_type) :
		case '' :
			?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div class="comment_itm" id="comment-<?php comment_ID(); ?>">
				<div class="comment_itm_photo">
					<?php echo get_avatar($comment, 55); ?>
				</div>
				
				<div class="comment_itm_txt" id="commenttext-<?php comment_ID(); ?>">
					<?php comment_text(); ?>
				</div>
				
				<div class="comment_itm_attr">
					<span class="comment_itm_author"><?php comment_author(); ?></span> |
					<span class="comment_itm_date"><?php comment_date('d M Y') ?></span>
					<?php comment_reply_link(array(
						'before'     => '',
						'after'      => '',
						'reply_text' => 'Ответить',
						'depth'      => $depth,
						'max_depth'  => $args['max_depth']
					)); ?>
				</div>
			</div>
			<?php
			break;
		case 'pingback'  :
			break;
		case 'trackback' :
			break;
	endswitch;
}


/****************************************************************************/
/******************** Количество опубликованных комментариев ****************/
/****************************************************************************/
function get_count_approve_comment($id = 0)
{
	$comments_counter = wp_count_comments($id);
	
	return $comments_counter->approved;
}


/****************************************************************************/
/******************** Получить список последних комментариев ****************/
/****************************************************************************/
function get_last_comments($count = 5)
{
	$args = array(
		'number' => $count,
		'status' => 'approve',
	);
	
	return get_comments($args);
}


/****************************************************************************/
/******************** Убрать обработчик onclik с ссылки ответа **************/
/****************************************************************************/
function remove_onclick_moveForm($link)
{
	return preg_replace('/onclick.*?>/', '>', $link);
}

add_filter('comment_reply_link', 'remove_onclick_moveForm');