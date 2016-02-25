<?php
/****************************************************************************/
/*
	* Виджет контакта
	* Виджет facebook
	* Виджет подписки
*/
/****************************************************************************/


/****************************************************************************/
/****************************** Виджет контакта *****************************/
/****************************************************************************/
function get_widget_vk($id_block)
{
	$res = '
	<!-- VK Widget -->
	<div class="soc_widget" id="' . $id_block . '"></div>
	<script type="text/javascript">
	VK.Widgets.Group("' . $id_block . '", {mode: 0, width: "240", height: "400", color1: \'FFFFFF\', color2: \'2B587A\', color3: \'5B7FA6\'}, 71613785);
	</script>';

	return $res;
}


/****************************************************************************/
/****************************** Виджет facebook *****************************/
/****************************************************************************/
function get_widget_fb()
{

	$res = '<div class="soc_widget fb-like-box" data-href="https://www.facebook.com/cluboblako" data-width="240" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="false"></div>';

	return $res;
}


/****************************************************************************/
/****************************** Виджет подписки *****************************/
/****************************************************************************/
function get_widget_subscribe()
{
	$mytheme = get_option('themadmin');
	$res     = '<div class="b_subscribe">
				<p class="subscribe_title">' . $mytheme['sbcr_widg_title'] . '<br /><img src="' . get_bloginfo('template_url') . '/images/logo_225.png" alt=""></p>
				<div class="subscribe_dsc">' . $mytheme['sbcr_widg_txt'] . '</div>
				<div class="subscribe_dsc_err"></div>
				<div class="subscribe_dsc_good"></div>
				<form action="/" class="f_subscribe" id="subscribe_widget" method="post">
					<input type="email" name="email" class="subscribe_input" placeholder="Введите email" required="required" />
					<input type="submit" class="subscribe_btn" value="Подписаться" />
				</form>
			</div>';

	return $res;
}

?>