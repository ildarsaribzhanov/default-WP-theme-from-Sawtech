<?php
/****************************************************************************/
/*
	* Редиректы
	* Регистрация
	* Авторизация
	* Обновление информации пользователя
	* Обновенине аватара
	* Загрузка файла (аватара)
	* Установка стандартного аватара
	* Запрос сброса пароля по email
	* Сброс пароля и генерация нового
	* Генерацияя нового пароля пользователю
	* Отправка нового пароля на почту пользователю
*/
/****************************************************************************/

/*****************************************************************************/
/********** редирект всех пользователей из админки, кроме админов ************/
/*****************************************************************************/

function profile_redirect() {
	$curent_url = get_bloginfo( 'url' );            // текущая страница
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$curent_url .= $_SERVER['REQUEST_URI'];
	};
	$page_reg    = get_permalink( 21 );                // page registration
	$page_prof   = get_permalink( 23 );                // page profile
	$page_login  = 'wp-login.php';                    // вход
	$logout_link = '/wp-login.php?action=logout';    // ссылка выхода
	$page_admin  = 'wp-admin';                    // админка

	// в админке нефиг делать никому кроме O'дминов =)
	if ( ! current_user_can( 'administrator' ) && stripos( $curent_url, $page_admin ) !== false ) {
		wp_redirect( get_bloginfo( 'url' ) );
		exit;
	};

	// стандартную авторизацию закроем, но страницу выхода оставим
	if ( stripos( $curent_url, $page_login ) !== false && stripos( $curent_url, $logout_link ) === false ) {
		wp_redirect( $page_reg );
		exit;
	}

	// неавторизованным пользователям нечего делать на странице профиля
	if ( ! is_user_logged_in() && stripos( $curent_url, $page_prof ) !== false ) {
		wp_redirect( $page_reg );
		exit;
	};

	// точно так же как и авторизованным нечего делать на странице авторизации
	if ( is_user_logged_in() && stripos( $curent_url, $page_reg ) !== false ) {
		wp_redirect( $page_prof );
		exit;
	}

}


add_action( 'init', 'profile_redirect' );


/*****************************************************************************/
/******************** добавить нового пользователя ***************************/
/*****************************************************************************/
function registered_new_user( $user_params ) {

	global $wpdb;

	// заполненность полей
	if (
		$user_params['login'] == ''
		|| $user_params['pass1'] == ''
		|| $user_params['pass2'] == ''
		|| $user_params['email'] == ''
		|| $user_params['phone'] == ''
		|| $user_params['lname'] == ''
		|| $user_params['fname'] == ''
		|| $user_params['company'] == ''
		|| $user_params['good_prav'] == ''

	) {
		return 'Не все обязательные поля заполнены.<br />';
	}

	if ( $user_params['pass1'] != $user_params['pass2'] ) {
		return 'Пароли не совпадают.';
	}

	if ( username_exists( $user_params['login'] ) ) {
		return 'Такой логин уже используется на сайте.<br />';
	};

	// проверка email
	if ( email_exists( $user_params['email'] ) ) {
		return 'Такой email уже используется на сайте. Используйте другой email, или воспольщуйтесь формой восстановления пароля.<br />';
	};

	$userdata = array(
		'user_pass'    => $user_params['pass1'],        //обязательно
		'user_login'   => $user_params['login'],        //обязательно
		'user_email'   => $user_params['email'],        //обязательно
		'first_name'   => $user_params['fname'],
		'last_name'    => $user_params['lname'],
		'display_name' => $user_params['lname'] . ' ' . $user_params['fname'],
		'description'  => '',
		'rich_editing' => false  // false - выключить визуальный редактор для пользователя.
	);

	$newuser_ID = wp_insert_user( $userdata );    // ID нового пользователя

	update_user_meta( $newuser_ID, 'phone', $user_params['phone'] );  // телефон
	update_user_meta( $newuser_ID, 'company', $user_params['company'] );  // СМИ


	// Авторизуем пользователя
	$res = autorization_user( $user_params['login'], $user_params['pass1'] );

	if ( is_wp_error( $res ) ) {
		print_r( $res->get_error_message() );
	} else {
		wp_redirect( '/profile/' );
	};

	return true;
}


/*****************************************************************************/
/************************ авторизация пользоватлея ***************************/
/*****************************************************************************/
function autorization_user( $login, $pass ) {
	$creds                  = array();
	$creds['user_login']    = $login;
	$creds['user_password'] = $pass;
	$creds['remember']      = true;
	$res                    = wp_signon( $creds, false );


	return $res;
}


/*****************************************************************************/
/******************** Обновление информации о пользователе *******************/
/*****************************************************************************/
function update_user_info( $param ) {
	// заполненность полей
	if (
		$param['email'] == ''
		|| $param['phone'] == ''
		|| $param['lname'] == ''
		|| $param['fname'] == ''

	) {
		return '<p>Не все обязательные поля заполнены.</p>';
	}

	$login   = wp_get_current_user()->user_login;
	$id_user = get_current_user_ID();

	$res_msg   = array();
	$err_msg   = array();
	$res_array = array();
	$err_pass  = false;

	$userdata = array(
		'ID'           => $id_user,
		'user_email'   => $param['email'],
		'first_name'   => $param['fname'],
		'last_name'    => $param['lname'],
		'display_name' => $param['lname'] . ' ' . $param['fname']
	);

	$res = wp_update_user( $userdata );    // ID пользователя

	if ( ! $res ) {
		return '<p>Ошибка обновления профиля. Обратитесь к администрации.</p>';
	};

	if ( isset( $param['phone'] ) ) {
		$res = update_user_meta( $id_user, 'phone', $param['phone'] );
	}


	if ( isset( $param['company'] ) ) {
		$res = update_user_meta( $id_user, 'company', $param['company'] );
	};

	// обновление пароля
	if (
		isset( $param['pass_act'] ) && $param['pass_act'] != ''
		&& isset( $param['pass1'] ) && $param['pass1'] != ''
		&& isset( $param['pass2'] ) && $param['pass2'] != ''
	) {

		// pass not equally
		if ( $param['pass1'] != $param['pass2'] ) {
			array_push( $res_msg, '<p>Введенные пароли не равны!</p>' );
			$err_pass = true;
		};

		if ( strlen( $param['pass1'] ) < 6 ) {
			array_push( $res_msg, '<p>Пароль должен быть не менее 8 символов</p>' );
			$err_pass = true;
		};

		// actual pass not true
		if ( is_wp_error( wp_authenticate( $login, $param['pass_act'] ) ) ) {
			array_push( $res_msg, '<p>Введен неверный текущий пароль!</p>' );
			$err_pass = true;
		};

		// если есть ошибки, выходим из обновления
		if ( $err_pass ) {
			return $res_msg;
		};


		$userdata = array(
			'ID'        => $id_user,
			'user_pass' => $param['pass1']
		);

		// обновление пароля
		wp_update_user( $userdata );

		// Авторизуем пользователя
		$res = autorization_user( $login, $param['pass1'] );

		array_push( $res_msg, '<p class="reg_good">Пароль обновлен.</p>' );
	};

	return '<p class="good_update">Информация обновлена</p>';

}


/*****************************************************************************/
/****************************** Обновление аватара ***************************/
/*****************************************************************************/
function update_avatar( $file ) {
	$ava_url = upload_file( $file );
	$rewsd   = array();

	if ( is_array( $ava_url ) ) {
		return $ava_url;

	} else {
		$id_user = get_current_user_ID();

		// удалим предыдущий аватар
		$filename = get_user_meta( $id_user, 'ulogin_photo', 1 );
		if ( $filename && strripos( $filename, 'http' ) !== false && strripos( $filename, 'com/' ) !== false && strripos( $filename, 'vk.me/' ) !== false ) {
			$filename = $_SERVER['DOCUMENT_ROOT'] . str_replace( get_bloginfo( 'url' ), '', $filename );
			unlink( $filename );

		};

		// запишем в базу новый аватар
		$res = update_user_meta( $id_user, 'ulogin_photo', $ava_url );
	};

	return true;
}


/*****************************************************************************/
/********************************* Загрузка файла ****************************/
/*****************************************************************************/

function upload_file( $file_array ) {
	$err               = array();
	$global_path       = $_SERVER['DOCUMENT_ROOT'];        // полный путь на сервере до папки сайта
	$local_path        = '/wp-content/uploads/avatars/';    // путь до папки загрузки
	$file_name         = $file_array["name"];                // имя файла
	$downloadfile_name = date( "dmy" ) . '-' . date( "Hi" ) . '_' . $file_name; // соль для имени файла

	if ( $file_array['size'] > 1000000 ) {
		array_push( $err, '<p class="reg_err">Размер файла слишком большой.<br />Допускается загружать аватар до 1 Мб</p>' );

		return $err;
	};

	$file_format = mb_strtolower( $file_array["name"] );
	$file_format = end( explode( ".", $file_format ) );

	$formats = array( 'jpg', 'jpeg', 'png', 'gif' );
	if ( ! in_array( $file_format, $formats ) ) {
		array_push( $err, '<p class="reg_err">Недопустимый формат файла. Допускается загружать только графические файлы .jpg, .png, .gif.</p>' );

		return $err;
	};

	// Проверяем загружен ли файл
	if ( is_uploaded_file( $file_array["tmp_name"] ) ) {
		// Если файл загружен успешно, перемещаем его из временной директории в конечную
		move_uploaded_file( $file_array["tmp_name"], $global_path . $local_path . $downloadfile_name );

		return get_bloginfo( 'url' ) . $local_path . $downloadfile_name;
	} else {
		array_push( $err, '<p class="reg_err">Ошибка загрузки файла "' . $file_array["name"] . '</p>' );

		return $err;
	}

}


/****************************************************************************/
/*************************** Стандартный аватар *****************************/
/****************************************************************************/
add_filter( 'avatar_defaults', 'newgravatar' );

function newgravatar( $avatar_defaults ) {
	$myavatar                     = get_bloginfo( 'template_directory' ) . '/images/ava_noname.png';
	$avatar_defaults[ $myavatar ] = "wpstarter";

	return $avatar_defaults;
}


/****************************************************************************/
/************************ Запрос сброса пароля по email *********************/
/****************************************************************************/
function get_reset_pass_link( $email = '' ) {
	global $wpdb;
	$user = get_user_by( 'email', $email );
	if ( ! $user ) {
		return false;
	};

	$reset_nonce = md5( date( 'd Y s m i ' ) . $email );

	$table_users = $wpdb->prefix . 'users';
	$wpdb->update(
		$table_users
		, array( 'user_activation_key' => $reset_nonce )
		, array( 'ID' => $user->ID )
		, array( '%s' )
		, array( '%d' )
	);

	$reset_link = get_permalink( 21 ) . '?fogotpass=fogot&email=' . $email . '&nonce=' . $reset_nonce;

	return $reset_link;
}


/****************************************************************************/
/************************ Сброс пароля и генерация нового *******************/
/****************************************************************************/
function is_current_reset_key( $email = '', $nonce = '' ) {
	global $wpdb;
	$user = get_user_by( 'email', $email );
	if ( ! $user ) {
		return false;
	};

	$table_users = $wpdb->prefix . 'users';
	$user_id     = $wpdb->get_var(
		$wpdb->prepare( "SELECT ID FROM $table_users WHERE user_email = %s AND user_activation_key = %s"
			, $email, $nonce )
	);

	if ( ! $user_id ) {
		return false;
	};

	// сброс ключа user_activation_key
	$wpdb->update(
		$table_users,
		array( 'user_activation_key' => '' ),
		array( 'ID' => $user->ID ),
		array( '%s' ),
		array( '%d' )
	);


	$new_pass = wp_generate_password( 12, true );
	wp_update_user( array( 'ID' => $user_id, 'user_pass' => $new_pass ) );

	send_new_pass( $email, $new_pass );

	return true;
}


/****************************************************************************/
/************* Отправка нового пароля на почту пользователю *****************/
/****************************************************************************/
function send_new_pass( $email = '', $pass = '' ) {
	$headers = "Content-type: text/html; charset=utf-8\r\n";
	$headers .= 'From: ' . get_bloginfo( 'name' ) . ' ' . "\r\n";
	$to      = $email;
	$subject = 'Новый пароль на сайте ' . get_bloginfo( 'name' );
	$message = '
		<p>Вы подтвердили сброс пароля на сайте <a href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'name' ) . '</a>.</p>
		<p>Ваш новый пароль: ' . $pass . '</p>
		<br />
		<p>Если вы не запрашивали сброс пароля, обратитсь к администрации.</p>
	';

	$res = wp_mail( $to, $subject, $message, $headers );

	if ( is_wp_error( $res ) ) {
		echo '<p class="fogot_err_show">Ошибка отправки сообщения. Свяжитесь с администрацией</p>';
	} else {
		echo '<p class="fogot_good_show">Вам отправлено письмо с новым паролем.</p>';
	};
}

?>