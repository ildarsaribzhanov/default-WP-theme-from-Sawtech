<?php
/****************************************************************************/
/*
	* Генерация csv пользователей
*/
/****************************************************************************/
function generate_csv()
{
	global $wpdb;
	$table_users = $wpdb->prefix . 'users';
	
	$userlist = $wpdb->get_results("SELECT ID FROM $table_users", OBJECT);
	
	$filecontent = "<b>Имя</b>#<b>Email</b>#<b>Телефон</b>#<b>СМИ, которое представляет</b>\n";
	
	foreach ($userlist as $userone) {
		$filecontent .= $userone->display_name;                    // имя
		$filecontent .= '#' . $userone->user_email;                // email
		$phone       = get_user_meta($userone->ID, 'phone', 1);        // телефон
		$filecontent .= '#' . $phone;
		$company     = get_user_meta($userone->ID, 'company', 1);    // СМИ
		$filecontent .= '#' . $company;
		$filecontent .= "\n";
	};
	
	$fp   = fopen(WP_CONTENT_DIR . '/uploads/users.csv', 'w+'); //открываем файл
	$test = fwrite($fp, $filecontent); // Запись в файл
	if ($test) {
		echo '<p>Данные в файл успешно занесены.</p>';
	} else {
		echo '<p style="color:#f00;">Ошибка при записи в файл.</p>';
	}
	fclose($fp);
}