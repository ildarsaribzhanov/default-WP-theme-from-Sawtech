<?php
/****************************************************************************/
/**************************** Постраничная навигация ************************/
/****************************************************************************/

/*
	$count_posts - Общее соличество постов
	$first_page_count - если на первой странице иное количество постов, например из-за виджета соц. сетей
	$all_pages_count - Количество постов на остальных страницах
	
	Работает на любой странице.


	Для цикла

	$posts_per_page = get_option('posts_per_page');
	$first_page_count = 5;

	if( isset($_GET['page']) ){
		$page = (int)$_GET['page'];
	};
	if( $page < 1 ){
		$page = 1;
	};

	// Отступ от начала записей
	if( $page > 1 ){
		$offset = $first_page_count + ($page-2) * $posts_per_page;
	} else {
		$offset = 0;
	};

	$arg = array(
		'posts_per_page' => $posts_per_page,
		'offset' => $offset
	);

	$posts_front = get_posts($arg);



	// Непосредственно вывод пагинатора
	$query_front = new WP_Query( $arg );
	$found_posts = $query_front->found_posts;
	wp_corenavi($found_posts, $posts_per_page, $posts_per_page);


	В общем случае вызывается так:
	wp_corenavi($wp_query->found_posts);

*/
function wp_corenavi($count_posts = 1, $first_page_count = 1, $all_pages_count = 1)
{
	if (isset($_SERVER['REDIRECT_URL'])) {
		$redirect_url = $_SERVER['REDIRECT_URL'];
	} else {
		$redirect_url = '';
	};
	$current_url = get_bloginfo('url') . $redirect_url;
	
	// если параметры не заданы явно, то возьмем их из базы
	if ($first_page_count == 0) {
		$first_page_count = get_option('posts_per_page');
	};
	
	if ($all_pages_count == 0) {
		$all_pages_count = get_option('posts_per_page');
	};
	
	$current = 1;
	if (isset($_GET['page'])) {
		$current = (int)$_GET['page'];
	};
	
	if (isset($_GET['s'])) {
		$search = '?s=' . $_GET['s'] . '&';
	} else {
		$search = '?';
	};
	
	if ($current < 1) {
		$current = 1;
	}
	$pagination = '<div class="pagination_main">';
	
	$count_pages = 1;
	
	if ($count_posts > $first_page_count) {
		$count_posts = $count_posts - $first_page_count;
		$count_pages = ceil($count_posts / $all_pages_count) + 1;
	};
	
	if ($count_pages == 1) {
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В начало</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Предыдущая</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">1</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Следующая</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В конец</span>';
		$pagination .= '</div>';
		echo $pagination;
		
		return;
	};
	
	// обработка начальный ссылок
	if ($current == 1) {
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В начало</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Предыдущая</span>';
	} else {
		$pagination .= '<a href="' . $current_url . $search . 'page=1" class="pagination_itm pagination_itm_link">В начало</a>';
		$pagination .= '<a href="' . $current_url . $search . 'page=' . ($current - 1) . '" class="pagination_itm pagination_itm_link">Предыдущая</a>';
	};
	
	if ($count_pages <= 7) {
		for ($i = 1; $i <= $count_pages; $i++) {
			if ($current == $i) {
				$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
			} else {
				$pagination .= '<a href="' . $current_url . $search . 'page=' . $i . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
			};
		};
	} else {
		if ($current <= 4) {
			for ($i = 1; $i <= 6; $i++) {
				if ($current == $i) {
					$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
				} else {
					$pagination .= '<a href="' . $current_url . $search . 'page=' . $i . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
				};
			};
			
			$pagination .= '<span class="pagination_itm">...</span>';
			
		} elseif ($current >= $count_pages - 3) {
			$pagination .= '<span class="pagination_itm">...</span>';
			
			for ($i = $count_pages - 6; $i <= $count_pages; $i++) {
				if ($current == $i) {
					$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
				} else {
					$pagination .= '<a href="' . $current_url . $search . 'page=' . $i . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
				};
			};
			
		} else {
			$pagination .= '<span class="pagination_itm">...</span>';
			
			for ($i = $current - 2; $i <= $current + 2; $i++) {
				if ($current == $i) {
					$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
				} else {
					$pagination .= '<a href="' . $current_url . $search . 'page=' . $i . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
				};
			};
			$pagination .= '<span class="pagination_itm">...</span>';
			
		};
	}
	
	
	// обработка конечных ссылок
	if ($current == $count_pages) {
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Следующая</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В конец</span>';
	} else {
		$pagination .= '<a href="' . $current_url . $search . 'page=' . ($current + 1) . '" class="pagination_itm pagination_itm_link">Следующая</a>';
		$pagination .= '<a href="' . $current_url . $search . 'page=' . $count_pages . '" class="pagination_itm pagination_itm_link">В конец</a>';
	};
	
	$pagination .= '</div>';
	echo $pagination;
	
	return;
}


/****************************************************************************/
/******************* Постраничная навигация c крутым ЧПУ ********************/
/****************************************************************************/

/*
	$count_posts - Общее соличество постов
	$first_page_count - если на первой странице иное количество постов, например из-за виджета соц. сетей
	$all_pages_count - Количество постов на остальных страницах

	На главной странице работет, если в качестве главной выбрана конктретная страница
	
	Для цикла

	$posts_per_page = get_option('posts_per_page');
	$first_page_count = 5;

	$page = get_query_var('paged');

	if( $page < 1 ){
		$page = 1;
	};

	// Отступ от начала записей
	if( $page > 1 ){
		$offset = $first_page_count + ($page-2) * $posts_per_page;
	} else {
		$offset = 0;
	};

	$arg = array(
		'posts_per_page' => $posts_per_page,
		'offset' => $offset
	);

	$posts_front = get_posts($arg);

	
	// Пример вызова
	$query_front = new WP_Query( $arg );
	$found_posts = $query_front->found_posts;
	wp_corenavi_good($found_posts, $posts_per_page, $posts_per_page);

	В общем случае вызывается так:
	wp_corenavi_good($wp_query->found_posts);
*/
function wp_corenavi_good($count_posts = 1, $first_page_count = 0, $all_pages_count = 0)
{
	// если параметры не заданы явно, то возьмем их из базы
	if ($first_page_count == 0) {
		$first_page_count = get_option('posts_per_page');
	};
	
	if ($all_pages_count == 0) {
		$all_pages_count = get_option('posts_per_page');
	};
	
	
	// текущая страница
	$current = 1;
	if (get_query_var('paged')) {
		$current = (int)get_query_var('paged');
	};
	
	// Для страницы поиска
	if (isset($_GET['s'])) {
		$search = '?s=' . $_GET['s'] . '&';
	} else {
		$search = '';
	};
	
	
	if ($current < 1) {
		$current = 1;
	}
	$pagination = '<div class="pagination_main">';
	
	
	// Общее количество страниц
	$count_pages = 1;
	
	if ($count_posts > $first_page_count) {
		$count_posts = $count_posts - $first_page_count;
		$count_pages = ceil($count_posts / $all_pages_count) + 1;
	};
	
	// Вывод пагинации если страцниа всего одна
	if ($count_pages == 1) {
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В начало</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Предыдущая</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">1</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Следующая</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В конец</span>';
		$pagination .= '</div>';
		echo $pagination;
		
		return;
	};
	
	// обработка начальный ссылок
	if ($current == 1) {
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В начало</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Предыдущая</span>';
	} else {
		$pagination .= '<a href="' . get_pagenum_link(1) . $search . '" class="pagination_itm pagination_itm_link">В начало</a>';
		$pagination .= '<a href="' . get_pagenum_link($current - 1) . $search . '" class="pagination_itm pagination_itm_link">Предыдущая</a>';
	};
	
	// Если меньше 7 страниц, то все ссылки влезут явно, иначе нужно сокращение (многоточие)
	if ($count_pages <= 7) {
		for ($i = 1; $i <= $count_pages; $i++) {
			if ($current == $i) {
				$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
			} else {
				$pagination .= '<a href="' . get_pagenum_link($i) . $search . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
			};
		};
	} else {
		if ($current <= 4) {
			for ($i = 1; $i <= 6; $i++) {
				if ($current == $i) {
					$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
				} else {
					$pagination .= '<a href="' . get_pagenum_link($i) . $search . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
				};
			};
			
			$pagination .= '<span class="pagination_itm">...</span>';
			
		} elseif ($current >= $count_pages - 3) {
			$pagination .= '<span class="pagination_itm">...</span>';
			
			for ($i = $count_pages - 6; $i <= $count_pages; $i++) {
				if ($current == $i) {
					$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
				} else {
					$pagination .= '<a href="' . get_pagenum_link($i) . $search . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
				};
			};
			
		} else {
			$pagination .= '<span class="pagination_itm">...</span>';
			
			for ($i = $current - 2; $i <= $current + 2; $i++) {
				if ($current == $i) {
					$pagination .= '<span class="pagination_itm pagination_itm_activ">' . $i . '</span>';
				} else {
					$pagination .= '<a href="' . get_pagenum_link($i) . $search . '" class="pagination_itm pagination_itm_link">' . $i . '</a>';
				};
			};
			$pagination .= '<span class="pagination_itm">...</span>';
			
		};
	}
	
	
	// обработка конечных ссылок
	if ($current == $count_pages) {
		$pagination .= '<span class="pagination_itm pagination_itm_activ">Следующая</span>';
		$pagination .= '<span class="pagination_itm pagination_itm_activ">В конец</span>';
	} else {
		$pagination .= '<a href="' . get_pagenum_link($current + 1) . $search . '" class="pagination_itm pagination_itm_link">Следующая</a>';
		$pagination .= '<a href="' . get_pagenum_link($count_pages) . $search . '" class="pagination_itm pagination_itm_link">В конец</a>';
	};
	
	$pagination .= '</div>';
	echo $pagination;
	
	return;
}