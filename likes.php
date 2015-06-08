<div class="post_likes">
	<?php $like_url = urlencode( get_permalink() ); ?>

	<div class="like_itm">
		<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $like_url; ?>" data-text="<?php the_title(); ?> <?php the_permalink(); ?> ">Tweet</a>
		<script type="text/javascript">
			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
			// для ajax
			twttr.widgets.load();
		</script>
	</div>



	
	<div class="like_itm">
		<div class="fb-like" data-href="<?php get_permalink(); ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
	</div>

	<div class="like_itm">
		<div class="g-plusone" data-size="medium" data-href="<?php echo $like_url; ?>"></div>
		<script type="text/javascript">gapi.plusone.go();</script>
	</div>
	
			
	<div class="like_itm">
		<div id="vk_like-<?php the_ID(); ?>"></div>
		<script type="text/javascript">
			VK.Widgets.Like("vk_like-<?php the_ID(); ?>", {type: "button", pageUrl: '<?php echo $like_url; ?>'}, <?php the_ID(); ?> );
		</script>
	</div>

	<div class="like_itm">
		<div id="ok_shareWidget-<?php the_ID(); ?>"></div>
		<script type="text/javascript">
		!function (d, id, did, st) {
			var js = d.createElement("script");
			js.src = "http://connect.ok.ru/connect.js";
			js.onload = js.onreadystatechange = function () {
				if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
				if (!this.executed) {
					this.executed = true;
					setTimeout(function () {
						OK.CONNECT.insertShareWidget(id,did,st);
					}, 0);
				}
			}};
			d.documentElement.appendChild(js);
		}(document,"ok_shareWidget-<?php the_ID(); ?>","<?php echo $like_url; ?>","{width:100,height:30,st:'oval',sz:20,nt:1}");
		</script>
	</div>
</div>