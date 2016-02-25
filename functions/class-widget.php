<?php

/*
My_New_Best_Wordpress_widget - Название класса виджета, можно и, скорее, 
нужно изменить на корректное, чтобы было понятно, что виджет делает.
*/

class My_New_Best_Wordpress_widget extends WP_Widget
{

	// Конструктор виджета
	public function __construct()
	{
		parent::__construct('My_New_Best_Wordpress_widget', 'Последние зарегистрированные пользователи',
			array('description' => 'Этот виджет отображает последних зарегистрированных пользователей'));
	}
	

	// Это то как будет выводиться виджет на сайте
	public function widget($args, $instance)
	{

		extract($args);
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if ( ! empty($title)) {
			echo $before_title . $title . $after_title;
		}

		$args = array(
			'post_type' => 'post',
			'orderby'   => 'rand',
			'order'     => 'desc',
			'number'    => $instance['count_posts']
		);

		$get_posts = get_posts($args);

		foreach ($postsspa as $itm) {
			?>
			<div>
				<h3><a href="<?php echo get_permalink($itm->ID) ?>"><?php echo get_the_title($itm->ID); ?></a></h3>
				<div class="excerpt"><?php echo $itm->post_excerpt; ?></div>
			</div>
			<?php
		}
		echo $after_widget;
	}
	

	// Функция обновления настроек виджета. Когда в админке кликаем "Сохранить"
	public function update($new_instance, $old_instance)
	{
		$instance                = array();
		$instance['title']       = strip_tags($new_instance['title']);
		$instance['count_posts'] = strip_tags($new_instance['count_posts']);

		return $instance;
	}

	// Форма настройки виджета
	public function form($instance)
	{
		$title       = isset($instance['title']) ? $instance['title'] : 'Последние зарегистрированные пользователи';
		$count_posts = isset($instance['count_posts']) ? $instance['count_posts'] : '5';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Заголовок:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
				name="<?php echo $this->get_field_name('title'); ?>" type="text"
				value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id('count_posts'); ?>"><?php _e('Количество отображаемых пользователей:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('count_posts'); ?>"
				name="<?php echo $this->get_field_name('count_posts'); ?>" type="text"
				value="<?php echo $count_posts; ?>" />
		</p>
		<?php
	}
}

add_action('widgets_init', create_function('', 'register_widget( "My_New_Best_Wordpress_widget" );'));

?>