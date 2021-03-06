<?php
/****************************************************************************/
/*
	* Включение поддержки миниатюр
	* Включение блока виджетов
	* Убрать верхнюю панель у пользователей
	* Установка ширины контент
	* Получение id видео из ссылки youtube
	* Страница настройки темы
 	* Версия файла, чтобы не сильно кэшировалось
*/
/****************************************************************************/

// поддержка миниатюр
if (function_exists('add_theme_support')) {
	add_theme_support('post-thumbnails');
}

// блоки виждетов
if (function_exists('register_sidebar')) {
	register_sidebar(array(
		'name' => 'mailchimp',
		'id'   => 'mailchimp',
	));
	register_sidebar(array(
		'name' => 'tweet',
		'id'   => 'tweet',
	));
}

// добавление поддержки меню
add_theme_support('menus');

// убрать верхнюю панель у пользователей
add_filter('show_admin_bar', '__return_false');


// Разрешить загрузку swf
add_filter('upload_mimes', 'my_upload_mimes');
function my_upload_mimes($mimes)
{
	$mimes['swf'] = 'application/x-shockwave-flash';
	
	return $mimes;
}


/****************************************************************************/
/****************** Ширина контента для форматирования видео ****************/
/****************************************************************************/
if ( ! isset($content_width)) {
	$content_width = 630;
}


/****************************************************************************/
/****************** Получение id видео из ссылки youtube ********************/
/****************************************************************************/
function yuotube_revers($url_video)
{
	preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
		$url_video, $match);
	$Youtube_video['img'] = 'http://img.youtube.com/vi/' . $match[1] . '/0.jpg';
	$Youtube_video['id']  = $match[1];
	
	return $Youtube_video;
}


/****************************************************************************/
/***************************** Настройки темы *******************************/
/****************************************************************************/

class ControlPanel
{
	
	var $default_settings = Array(
		'sendemail'       => 'ildar@sawtech.ru',
		'sbcr_widg_title' => 'Подписка<br />на новости',
		'sbcr_widg_txt'   => 'Чтобы всегда оставаться в теме подписывайся на новости и держи руку на пульсе.',
		'sbcr_foot_title' => 'Подпишитесь<br />на новости',
		'sbcr_foot_txt'   => 'И вы будете получать сразу на почту самую полезную и актуальную информацию от ОБЛАКО'
	);
	
	var $options;
	
	function __construct()
	{
		add_action('admin_menu', array(&$this, 'add_menu'));
		if ( ! is_array(get_option('themadmin'))) {
			add_option('themadmin', $this->default_settings);
		}
		$this->options = get_option('themadmin');
	}
	
	function add_menu()
	{
		add_theme_page('WP Theme Options', 'Настройки темы', 'edit_files', "themadmin", array(
			&$this,
			'optionsmenu'
		));
	}
	
	function optionsmenu()
	{
		if (isset($_POST['ss_action']) && $_POST['ss_action'] == 'save') {
			
			$this->options["sendemail"]       = $_POST['cp_sendemail'];
			$this->options["sbcr_widg_title"] = $_POST['cp_sbcr_widg_title'];
			$this->options["sbcr_widg_txt"]   = $_POST['cp_sbcr_widg_txt'];
			$this->options["sbcr_foot_title"] = $_POST['cp_sbcr_foot_title'];
			$this->options["sbcr_foot_txt"]   = $_POST['cp_sbcr_foot_txt'];
			
			update_option('themadmin', $this->options);
			
			echo '<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204); width: 400px; margin-left: 17px; margin-top: 17px;"><p>Ваши изменения <strong>сохранены</strong>.</p></div>';
		}
		echo '<h1>Настройки темы</h1>';
		echo '<form action="" method="post" class="themeform">';
		echo '<input type="hidden" id="ss_action" name="ss_action" value="save">';
		
		echo '
			<div class="cptab">
				
				<p>Email куда будут отправляться все заявки</p>
				<p><input name="cp_sendemail" id="cp_sendemail" value="' . stripslashes($this->options["sendemail"]) . '" style="width:400px;" /></p>
				
				<p>Заголовок виджета подписки</p>
				<p><input name="cp_sbcr_widg_title" id="cp_sbcr_widg_title" value="' . stripslashes($this->options["sbcr_widg_title"]) . '" style="width:400px;" /></p>

				<p>Текст виджета подписки</p>
				<p><textarea name="cp_sbcr_widg_txt" id="cp_sbcr_widg_txt" style="width:400px; height:75px;">' . stripslashes($this->options["sbcr_widg_txt"]) . '</textarea></p>

				<p>Заголовок формы подписки в подвале</p>
				<p><input name="cp_sbcr_foot_title" id="cp_sbcr_foot_title" value="' . stripslashes($this->options["sbcr_foot_title"]) . '" style="width:400px;" /></p>

				<p>Текст формы подписки в подвале</p>
				<p><textarea name="cp_sbcr_foot_txt" id="cp_sbcr_foot_txt" style="width:400px; height:75px;">' . stripslashes($this->options["sbcr_foot_txt"]) . '</textarea></p>

				<p></p>
				<p></p>
				<p></p>
				<p></p>

 
 
			</div>
			';
		
		echo '<input type="submit" value="Сохранить" name="cp_save" class="dochanges" />';
		echo '</form>';
	}
}

$cpanel  = new ControlPanel();
$mytheme = get_option('themadmin');


/**
 * Версия файла, чтобы не сильно кэшировалось
 *
 * @param string $file путь от папки темв
 * @param string $type типа файла [css/js]
 *
 * @return string
 */
function version_file($file = '', $type = 'css')
{
	$ver  = 'ver=' . date('d.m.Y.H.i', filemtime(TEMPLATEPATH . $file));
	$file = get_template_directory_uri() . $file . '?' . $ver;
	
	switch ($type) {
		case 'css':
			$res = '<link rel="stylesheet" type="text/css" href="' . $file . '" />';
			break;
		
		case 'js':
			$res = '<script type="text/javascript" src="' . $file . '"></script>';
			break;
	}
	
	return $res;
}