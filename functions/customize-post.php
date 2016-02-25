<?php
/****************************************************************************/
/*
	* Создание нового типа записей, со своей таксономией
	* Правила создания ЧПУ если чего-то там не заработало
	* Дополнительная колонка для пользовательских записей
	* Сделать новую колонку сортируемой
	* Сортировка custom post по метаполю meta_field
	* Сортировка по терминам таксономии (таксономия name_new_tax)
	* Фильтр по терминам таксономии
	* Частичный вывод постов
	* Изменить количество слов в стандартной цитате поста
	* Удаление конструкции [...] после конце excerpt
	* Получить массив категорий записи
*/
/****************************************************************************/


/****************************************************************************/
/************ Создание нового типа записей, со своей таксономией ************/
/****************************************************************************/
add_action('init', 'create_post_type');
function create_post_type()
{
	$labels = array(
		'name'               => __('Банеры'),
		'singular_name'      => __('Банеры'),
		'add_new'            => __('Добавить банер'),
		'add_new_item'       => __('Добавить новый банер'),
		'edit_item'          => __('Редактировать банер'),
		'new_item'           => __('Новый банер'),
		'view_item'          => __('Просмотр'),
		'search_items'       => __('Поиск'),
		'not_found'          => __('Не найдено'),
		'not_found_in_trash' => __('Среди удаленных нет искомого'),
		'parent_item_colon'  => ''
	);

	$support = array(
		'title',
		'editor',
		'author',
		'thumbnail',
		'excerpt',
		'trackbacks',
		'custom-fields',
		'comments',
		'revisions',
		'page-attributes',
		'post-formats'
	);

	register_post_type('new_post_type', array(
			'show_in_menu' => true, //где отображать
			'labels'       => $labels,
			'public'       => true,
			'supports'     => $support,
			'menu_icon'    => 'dashicons-tablet'
		));

	// Заголовки таксономии
	$labels = array(
		'name'              => __('Название'),
		'singular_name'     => __('Название'),
		'search_items'      => __('Найти'),
		'all_items'         => __('Все'),
		'parent_item'       => __('Родительский тип'),
		'parent_item_colon' => __('Родительский тип:'),
		'edit_item'         => __('Редактировать'),
		'update_item'       => __('Обновить'),
		'add_new_item'      => __('Добавить новый'),
		'new_item_name'     => __('Имя нового типа'),
	);

	register_taxonomy('name_new_tax', 'new_post_type', array(
		'hierarchical' => true,
		'labels'       => $labels,
		'rewrite'      => true
	));
}


/****************************************************************************/
/************ Правила создания ЧПУ если чего-то там не заработало, **********/
/******* или нужно, чтобы открывалось одно и тоже но на разных ссылках ******/
/****************************************************************************/
add_filter('rewrite_rules_array', 'my_insert_rewrite_rules');
function my_insert_rewrite_rules($rules)
{
	$newrules = array(
		'new_post_type/?$'                              => 'index.php?taxonomy=new_post_type',
		'new_post_type/([^/]+/?)$'                      => 'index.php?new_post_type=$matches[1]',
		'new_post_type/(.+?)/page/?([0-9]{1,})/?$'      => 'index.php?new_post_type=$matches[1]&paged=$matches[2]',
		'new_post_type_else/?$'                         => 'index.php?taxonomy=new_post_type',
		'new_post_type_else/([^/]+/?)$'                 => 'index.php?new_post_type=$matches[1]',
		'new_post_type_else/(.+?)/page/?([0-9]{1,})/?$' => 'index.php?new_post_type=$matches[1]&paged=$matches[2]'
	);

	return $newrules + $rules;
}


/****************************************************************************/
/*********** Дополнительная колонка для пользовательских записей ************/
/****************************************************************************/

add_filter("manage_edit-[new_post_type]_columns", "new_columns_partners");
add_action('manage_[new_post_type]_posts_custom_column', 'add_content_new_col', 10, 2);


function new_columns_partners($standart)
{
	$standart = array(
		'cb'         => '<input type="checkbox">',
		'title'      => 'Заголовок',
		'razdel'     => 'Тип',
		'meta_field' => 'Метаполе',
		'date'       => 'Дата'
	);

	return $standart;
}

// Добавление контента для новых полей
function add_content_new_col($column, $post_id)
{

	switch ($column) {

		case 'razdel' :
			$this_terms = get_the_terms($post_id, 'name_new_tax');

			$res = '';
			if ($this_terms) {
				foreach ($this_terms as $itm_term) {
					$res .= ' ' . $itm_term->name . ',';
				};
			} else {
				$res = 'Не определен ';
			}

			$res = substr($res, 0, -1);

			echo $res;
			
			break;

		case 'meta_field' :
			$this_meta = get_post_meta($post_id, 'meta_field', 1);

			if ( ! $this_meta) {
				$this_meta = 'Не задано';
			}
			echo $this_meta;
			
			break;
	}
}


/****************************************************************************/
/************************ Сделать новую колонку сортируемой ******************/
/****************************************************************************/
add_filter('manage_edit-[new_post_type]_sortable_columns', 'add_views_sortable_column');
function add_views_sortable_column($sortable_columns)
{
	$sortable_columns['razdel']     = 'razdel';
	$sortable_columns['meta_field'] = 'meta_field';

	return $sortable_columns;
}


/****************************************************************************/
/***************** Сортировка custom post по метаполю meta_field ***********/
/****************************************************************************/
add_filter('request', 'add_column_views_request');
function add_column_views_request($vars)
{
	if (isset($vars['orderby']) && $vars['orderby'] == 'meta_field') {
		$vars['meta_key'] = 'meta_field';
		$vars['orderby']  = 'meta_value_num';
	}

	return $vars;
}


/****************************************************************************/
/******** Сортировка по терминам таксономии (таксономия name_new_tax) *********/
/****************************************************************************/
function orderby_newtax($vars, $wp_query)
{

	global $wpdb;

	if (isset($wp_query->query['orderby']) && $wp_query->query['orderby'] == 'name_new_tax') {

		$t_posts = $wpdb->prefix . 'posts';
		$t_rel   = $wpdb->prefix . 'term_relationships';
		$t_tax   = $wpdb->prefix . 'term_taxonomy';
		$t_term  = $wpdb->prefix . 'terms';

		$vars['join'] .= " LEFT JOIN $t_rel ON ($t_posts.ID = $t_rel.object_id) ";
		$vars['join'] .= " LEFT JOIN $t_tax ON ($t_rel.term_taxonomy_id = $t_tax.term_taxonomy_id) ";
		$vars['join'] .= " LEFT JOIN $t_term ON ($t_tax.term_id = $t_term.term_id) ";

		$vars['groupby'] .= " $t_posts.ID ";

		$vars['orderby'] = " $t_term.name ";
		$vars['orderby'] .= ('ASC' == strtoupper($wp_query->get('order'))) ? 'ASC' : 'DESC';
	}

	return $vars;
}

add_filter('posts_clauses', 'orderby_newtax', 10, 2);


/****************************************************************************/
/************************ Фильтр по терминам таксономии *********************/
/****************************************************************************/
add_action('restrict_manage_posts', 'filter_terms_list');

function filter_terms_list()
{
	$screen = get_current_screen();
	global $wp_query;
	if ($screen->post_type == 'new_post_type') {
		wp_dropdown_categories(array(
			'show_option_all' => 'Показать все термины',
			'taxonomy'        => 'name_new_tax',
			'name'            => 'name_new_tax',
			'orderby'         => 'name',
			'selected'        => (isset($wp_query->query['name_new_tax']) ? $wp_query->query['name_new_tax'] : ''),
			'hierarchical'    => true,
			'depth'           => 2,
			'show_count'      => false,
			'hide_empty'      => false,
		));
	}
}

add_filter('parse_query', 'perform_filtering');
function perform_filtering($query)
{
	$qv = &$query->query_vars;
	if (($qv['name_new_tax']) && is_numeric($qv['name_new_tax'])) {
		$term               = get_term_by('id', $qv['name_new_tax'], 'name_new_tax');
		$qv['name_new_tax'] = $term->slug;
	}
}

/****************************************************************************/
/************************ Частичный вывод постов ****************************/
/****************************************************************************/

// урезание поста $ID до размера $limit слов, если не задана цитата
function get_excerpt_post($ID, $limit)
{
	$this_post = get_post($ID);
	if ($this_post->post_excerpt != '') {
		return $this_post->post_excerpt;
	};

	return get_excerpt_text($this_post->post_content, $limit);
}


// урезание текста $txt до количества слов $limit
function get_excerpt_text($txt, $limit = 20)
{
	$txt = explode(' ', $txt, $limit);

	if (count($txt) >= $limit) {
		array_pop($txt);
		$txt = implode(" ", $txt) . '...';
	} else {
		$txt = implode(" ", $txt);
	}
	$txt = strip_tags($txt);

	return $txt;
}


/****************************************************************************/
/************* Изменить количество слов в стандартной цитате поста **********/
/****************************************************************************/
function new_excerpt_length($length)
{
	return 20;
}

add_filter('excerpt_length', 'new_excerpt_length');


/****************************************************************************/
/*************** Удаление конструкции [...] после конце excerpt *************/
/****************************************************************************/
function new_excerpt_more($more)
{
	return '...';
}

add_filter('excerpt_more', 'new_excerpt_more');


/****************************************************************************/
/********************** Получить массив категорий поста *********************/
/****************************************************************************/

function get_post_cat_array($ID = 0)
{
	$cats = get_the_category($ID);
	$res  = array();
	foreach ($cats as $itm) {
		array_push($res, $itm->term_id);
	};

	return $res;
}

?>