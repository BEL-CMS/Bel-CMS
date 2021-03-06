<?php
/**
 * Bel-CMS [Content management system]
 * @version 0.0.1
 * @link http://www.bel-cms.be
 * @link http://www.stive.eu
 * @license http://opensource.org/licenses/GPL-3.0 copyleft
 * @copyright 2014-2019 Bel-CMS
 * @author Stive - mail@stive.eu
 */

if (!defined('CHECK_INDEX')) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit(ERROR_INDEX);
}
?>
<section id="belcms_forum">
	<?php
	foreach ($forum as $k => $v):
	?>
	<div class="forum">
		<h1 class="belcms_forum_h1"><?=$v->title?></h1>
		<div class="belcms_forum_category_description"><?=$v->subtitle?></div>
		<table class="belcms_forum_table table table-bordered">
			<?php
			$count = (count($v->category));
			foreach ($v->category as $cat_k => $cat_v):
				?>
			<tr>
				<td class="belcms_f_td">
					<i class="<?=$cat_v->icon?>"></i>
				</td>
				<td>
					<a class="belcms_f_a"><a href="Forum/Threads/<?=$cat_v->title?>/<?=$cat_v->id?>"><?=$cat_v->title?></a></a>
				</td>
				<td>
					Dernier post en date : <?php if (empty($cat_v->last->date_post)) { echo 'Aucun post'; } else { echo Common::TransformDate($cat_v->last->date_post, 'MEDIUM', 'NONE'); ?>par <span style="color: <?php echo Users::colorUsername($cat_v->last->author)?>"><?php echo Users::hashkeyToUsernameAvatar($cat_v->last->author); ?></span><?php } ?>
				</td>
				<td style="text-align: center;">
					<?=$cat_v->count?> post
				</td>
			</tr>
			<?php
			if ($count <= count($v->category)):
			?>
				<tr class="espace"></tr>
			<?php
			endif;
			endforeach;
			?>
		</table>
	</div>	
	<?php
	endforeach;
	?>
</section>