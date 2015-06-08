<?php
function kama_breadcrumbs( $sep = ' » ' ) {

	global $post, $wp_query, $wp_post_types;

	// для локализации
	$l = array(
		'home'       => 'Главная',
		'paged'      => 'Страница %s',
		'404'        => 'Ошибка 404',
		'search'     => 'Результаты поиска по запросу - <b>%s</b>',
		'author'     => 'Архив автора: <b>%s</b>',
		'year'       => 'Архив за <b>%s</b> год',
		'month'      => 'Архив за: <b>%s</b>',
		'day'        => '',
		'attachment' => 'Медиа: %s',
		'tag'        => 'Записи по метке: <b>%s</b>',
		'tax_tag'    => '%s из "%s" по тегу: <b>%s</b>',
	);

	$open_bc  = '<div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">';
	$close_bc = '</div>';
	$patt1    = '<span typeof="v:Breadcrumb"><a href="%s" rel="v:url" property="v:title">';
	$sep .= '</span>'; // закрываем span после разделителя!
	$patt = $patt1 . '%s</a>';

	if ( $paged = $wp_query->query_vars['paged'] ) {
		$pg_patt = $patt1;
		$pg_end  = '</a>' . $sep . sprintf( $l['paged'], $paged );
	}

	$out = '';

	// Для главной страницы сразу выведемссылку главную
	if ( is_front_page() ) {
		return print $open_bc . ( $paged ? sprintf( $pg_patt, get_bloginfo( 'url' ) ) : '' ) . $l['home'] . $pg_end . $close_bc;

		// 404 страница
	} elseif ( is_404() ) {
		$out = $l['404'];

		// Страница поиска
	} elseif ( is_search() ) {
		$out = sprintf( $l['search'], strip_tags( $GLOBALS['s'] ) );

		// Страница автора (архив автора)
	} elseif ( is_author() ) {
		$q_obj = &$wp_query->queried_object;
		$out   = ( $paged ? sprintf( $pg_patt, get_author_posts_url( $q_obj->ID, $q_obj->user_nicename ) ) : '' ) . sprintf( $l['author'], $q_obj->display_name ) . $pg_end;

		// Архивы по дате год/месяц/день
	} elseif ( is_year() || is_month() || is_day() ) {
		$y_url  = get_year_link( $year = get_the_time( 'Y' ) );
		$m_url  = get_month_link( $year, get_the_time( 'm' ) );
		$y_link = sprintf( $patt, $y_url, $year );
		$m_link = sprintf( $patt, $m_url, get_the_time( 'F' ) );
		if ( is_year() ) {
			$out = ( $paged ? sprintf( $pg_patt, $y_url ) : '' ) . sprintf( $l['year'], $year ) . $pg_end;
		} elseif ( is_month() ) {
			$out = $y_link . $sep . ( $paged ? sprintf( $pg_patt, $m_url ) : '' ) . sprintf( $l['month'], get_the_time( 'F' ) ) . $pg_end;
		} elseif ( is_day() ) {
			$out = $y_link . $sep . $m_link . $sep . get_the_time( 'l' );
		}
	} // Страницы и древовидные типы записей
	elseif ( $wp_post_types[ $post->post_type ]->hierarchical ) {
		$parent = $post->post_parent;
		$crumbs = array();
		while ( $parent ) {
			$page     = &get_post( $parent );
			$crumbs[] = sprintf( $patt, get_permalink( $page->ID ), $page->post_title );
			$parent   = $page->post_parent;
		}
		$crumbs = array_reverse( $crumbs );
		foreach ( $crumbs as $crumb ) {
			$out .= $crumb . $sep;
		}
		$out = $out . $post->post_title;


		// Таксономии, вложения и не древовидные типы записей
	} else {

		// Определяем термины
		if ( is_singular() ) {

			$taxonomies = get_taxonomies( array( 'hierarchical' => true, 'public' => true ) );
			if ( count( $taxonomies ) == 1 ) {
				$taxonomies = 'category';
			}

			if ( $term = get_the_terms( $post->post_parent ? $post->post_parent : $post->ID, $taxonomies ) ) {
				$term = array_shift( $term );
			}

		} else {
			$term = &$wp_query->get_queried_object();
		}

		if ( ! $term && ! is_attachment() ) {
			return print "Error: Taxonomy is not defined!";
		}

		$pg_term_start = ( $paged && $term->term_id ) ? sprintf( $pg_patt, get_term_link( (int) $term->term_id, $term->taxonomy ) ) : '';

		if ( is_attachment() ) {
			if ( ! $post->post_parent ) {
				$out = sprintf( $l['attachment'], $post->post_title );
			} else {
				$out = crumbs_tax( $term->term_id, $term->taxonomy, $sep, $patt ) . sprintf( $patt, get_permalink( $post->post_parent ), get_the_title( $post->post_parent ) ) . $sep . $post->post_title;
			}
		} elseif ( is_single() ) {
			$out = crumbs_tax( $term->parent, $term->taxonomy, $sep, $patt ) . sprintf( $patt, get_term_link( (int) $term->term_id, $term->taxonomy ), $term->name ) . $sep . $post->post_title;


			// Метки, архивная страница типа записи, произвольные одноуровневые таксономии
		} elseif ( ! is_taxonomy_hierarchical( $term->taxonomy ) ) {

			// метка
			if ( is_tag() ) {
				$out = $pg_term_start . sprintf( $l['tag'], $term->name ) . $pg_end;

				// архивная страница произвольного типа записи
			} elseif ( ! $term->term_id ) {
				$home_after = sprintf( $patt, '/' . $term->name, $term->label ) . $pg_end;

				// таксономия
			} else {
				$post_label = $wp_post_types[ $post->post_type ]->labels->name;
				$tax_label  = $GLOBALS['wp_taxonomies'][ $term->taxonomy ]->labels->name;
				$out        = $pg_term_start . sprintf( $l['tax_tag'], $post_label, $tax_label, $term->name ) . $pg_end;
			}

			// Рубрики и таксономии
		} else {
			$out = crumbs_tax( $term->parent, $term->taxonomy, $sep, $patt ) . $pg_term_start . $term->name . $pg_end;
		}
	}

	// замена ссылки на архивную страницу для типа записи 
	if ( $post->post_type == 'book' ) {
		$home_after = sprintf( $patt, '/about_book', 'Книжки' ) . $sep;
	}

	// ссылка на архивную страницу произвольно типа поста
	if ( ! $home_after && ! empty( $post->post_type ) && $post->post_type != 'post' && ! is_page() && ! is_attachment() ) {
		$home_after = sprintf( $patt, '/' . $post->post_type, $wp_post_types[ $post->post_type ]->labels->name ) . $sep;
	}

	$home = sprintf( $patt, get_bloginfo( 'url' ), $l['home'] ) . $sep . $home_after;

	return print $open_bc . $home . $out . $close_bc;
}


function crumbs_tax( $term_id, $tax, $sep, $patt ) {
	$termlink = array();
	while ( (int) $term_id ) {
		$term2      = &get_term( $term_id, $tax );
		$termlink[] = sprintf( $patt, get_term_link( (int) $term2->term_id, $term2->taxonomy ), $term2->name ) . $sep;
		$term_id    = (int) $term2->parent;
	}
	$termlinks = array_reverse( $termlink );

	return implode( '', $termlinks );
}

?>