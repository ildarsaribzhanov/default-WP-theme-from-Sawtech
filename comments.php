<div class="single_comments">

	<div class="single_comments_cont">
		<p class="single_comments_title">
			<img src="<?php bloginfo('template_url'); ?>/images/title_comments.png" alt="" />
		</p>
		<?php
			$count_comm = get_count_approve_comment( $post->ID );
		?>
		<?php if( !$count_comm ): ?>
			<span class="add_comment_top open_coment_form">Добавить комментарий</span>
		<?php endif; ?>

		<?php $args = array(
			'type' 		=> 'comment'
			,'callback'	=> 'mytheme_comment'

		); ?>

		<div class="single_comments_main">
			<ul>
				<?php  wp_list_comments($args); ?>
			</ul>
		</div>

		<?php if( $count_comm ): ?>
			<span class="add_comment_bot open_coment_form">Добавить комментарий</span>
		<?php endif; ?>
		
	</div>


	<div class="b_commentform" id="comment_form">
		<div class="b_commentform_main">

			<?php 
			$new_fields =  array(
				'author' => '<input id="author" class="comment_form_input_ltl" name="author" type="text" placeholder="Имя*" value="" size="30" required="required" />',
				'email'  => '<input id="email" class="comment_form_input_ltl" name="email" type="email" placeholder="Email*" value="" size="30" required="required" />'
				);
			if( is_user_logged_in() ){
				$user_name = get_userdata( get_current_user_id() )->display_name;
			} else {
				$user_name = '';
			}
			comment_form(
				array(
					'comment_notes_after' => ''
					,'fields' => apply_filters( 'comment_form_default_fields', $new_fields )
					,'comment_field' => '
						<p class="comment-form-comment">
							<textarea id="comment-' . get_the_ID() . '" name="comment" class="comment_txt" cols="45" rows="8" aria-required="true"  required="required" placeholder="Комментарий*"></textarea>
							<input type="hidden" nane="replytocom" value="3" />
						</p>'
						,'logged_in_as' => '<p class="logged-in-as">Вы зашли как '.$user_name . '<br />Комментарий будет опубликован после модерации</p>'
						,'id_form' => 'commentform-'.get_the_ID()
						,'title_reply' => ''
						,'id_submit' => 'submit_comment'
						,'label_submit' => 'Отправить'
						,'title_reply_to' => 'Оставить ответ на комментарий'
						,'cancel_reply_link' => ''
				)
			); ?>
		</div>
	</div>

</div>