<?php

/**
 * @package Minecraft Online Players Widget
 * @version 3.0
 */
/*
Plugin Name: Minecraft Online Players Widget
Plugin URI: 
Description: Plugin Widget permettant d'afficher les joueurs en ligne d'un serveur dans le menu du blog.
Author: pirmax
Version: 3.0
Author URI: http://pirmax.fr/
*/

require_once(dirname(__FILE__) . '/lib/MinecraftQuery.class.php');

function widget_mou()
{
	register_widget("widget_mou");
}

add_action('widgets_init', 'widget_mou');

function widget_mou_link($links, $file)
{
    if($file == plugin_basename(__FILE__))
	{
        $widget_mou_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DPPT5J9GXXFJY" target="_blank">Faire un don</a>';
        $links[] = $widget_mou_link;
        $widget_mou_link = '<a href="https://wordpress.org/support/view/plugin-reviews/minecraft-onlineusers-widget" target="_blank">Voter pour ce plugin</a>';
        $links[] = $widget_mou_link;
    }
    return $links;
}

add_filter('plugin_row_meta', 'widget_mou_link', 10, 2);

function widget_mou_action_links($links, $file)
{

	static $this_plugin;

	if(!$this_plugin)
	{
		$this_plugin = plugin_basename(__FILE__);
	}

	if($file == $this_plugin)
	{
		$settings_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DPPT5J9GXXFJY" target="_blank">Faire un don</a>';
		array_unshift($links, $settings_link);
		// $settings_link = '<a href="https://wordpress.org/support/view/plugin-reviews/minecraft-onlineusers-widget" target="_blank">Voter pour ce plugin</a>';
		// array_unshift($links, $settings_link);
	}

	return $links;

}

add_filter('plugin_action_links', 'widget_mou_action_links', 10, 2 );

class widget_mou extends WP_widget
{

	function widget_mou(){
		$options = array(
			"classname" => "widget-mou",
			"description" => "Afficher les joueurs en ligne sur votre serveur Minecraft."
		);

		$this->WP_widget("widget-mou", "Minecraft Online Players", $options);
	}

	function widget($args, $instance)
	{

		$styleCSS = "#mouw {\n}\n\n#mouw li {\n\tfont-size: 15px;\n\tfont-weight: bold;\n}\n\n#mouw li img.avatar {\n\tvertical-align: middle;\n\tmargin-right: 10px;\n}\n\n.widget-title span.title {\n}\n\n.widget-title span.number {\n\tmargin-left: 20px;\n\tfont-weight: bold;\n\tfloat: right;\n}";

		$defaut = array(
			"title" => "Joueurs en ligne",
			"ifNoPlayer" => "Aucun joueur en ligne",
			"serverip" => "ip.mon-serveur.fr",
			"serverport" => "25565",
			"displayAvatar" => "on",
			"displayCount" => "on",
			"nbSlot" => 30,
			"avatarSize" => 25,
			"styleCSS" => $styleCSS
		);
		$instance = wp_parse_args($instance, $defaut);

		$GetPlayers = array();

		if(!empty($instance['serverip']) AND !empty($instance['serverport']))
		{

			$Query = new MinecraftQuery( );

			try
			{
				$Query->Connect( $instance['serverip'], $instance['serverport'], 1 );
				$GetPlayers = (array) $Query->GetPlayers();
			}
			catch( MinecraftQueryException $e )
			{
				// echo $e->getMessage();
			}

			extract($args);

			$displayWidget = '';
			$displayWidget .= '<ul id="mouw">';

			if($GetPlayers !== false)
			{
				foreach ($GetPlayers as $i => $value)
				{
					if($instance['displayAvatar'] !== 1)
					{
						$displayWidget .= '<li><img src="https://minotar.net/helm/' . $value . '/' . $instance['avatarSize'] . '.png" width="' . $instance['avatarSize'] . '" height="' . $instance['avatarSize'] . '" border="0" title="' . $value . '" alt="avatar_' . $value . '" class="avatar" />' . $value . '</li>';
					}
					else
					{
						$displayWidget .= '<li>' . $value . '</li>';
					}
				}
				$resnbPlayer = count($GetPlayers);
			}
			else
			{
				$displayWidget .= '<li>' . $instance['ifNoPlayer'] . '</li>';
				$resnbPlayer = 0;
			}

			$displayWidget .= '</ul>';

			echo '<style>' . $instance['styleCSS'] . '</style>';
			echo $before_widget;

			if($instance['displayCount'] !== 1)
			{
				echo $before_title . '<span class="title">' . $instance['title'] . '</span><span class="number">' . $resnbPlayer . '/' . $instance['nbSlot'] . '</span>' . $after_title;
			}
			else
			{
				echo $before_title . $instance['title'] . $after_title;
			}

			echo $displayWidget;
			echo $after_widget;

		}

	}

	function update($new, $old)
	{
		return $new;
	}

	function form($d)
	{

		$styleCSS = "#mouw {\n}\n\n#mouw li {\n\tfont-size: 15px;\n\tfont-weight: bold;\n}\n\n#mouw li img.avatar {\n\tvertical-align: middle;\n\tmargin-right: 10px;\n}\n\n.widget-title span.title {\n}\n\n.widget-title span.number {\n\tmargin-left: 20px;\n\tfont-weight: bold;\n\tfloat: right;\n}";

		$defaut = array(
			"title" => "Joueurs en ligne",
			"ifNoPlayer" => "Aucun joueur en ligne",
			"serverip" => "ip.mon-serveur.fr",
			"serverport" => "25565",
			"displayAvatar" => "on",
			"displayCount" => "on",
			"nbSlot" => 30,
			"avatarSize" => 25,
			"styleCSS" => $styleCSS
		);
		$d = wp_parse_args($d, $defaut);

		?>
		<?php if(!function_exists('fwrite')){ echo '<p style="border-bottom: 1px dashed #FF0000; color: #FF0000; padding-bottom: 5px;"><b>Attention!</b> La fonction PHP <code>fwrite()</code> n\'est pas disponible sur votre hébergement. Contactez votre administrateur système.</p>'; } ?>
		<p>
		Pour activer le widget, vous devez activer <code>enable-query</code> (<strong>enable-query=true</strong>) dans le fichier <code>server.properties</code> de votre serveur <strong>Minecraft</strong> puis red&eacute;marrer votre serveur.
		</p>
		<hr style="border-top: 1px dashed #CCCCCC;" />
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Titre du widget :</label><br />
		<input value="<?php echo $d['title']; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" type="text" class="widefat" placeholder="Les joueurs en ligne" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('ifNoPlayer'); ?>">Texte de remplacement :</label><br />
		<input value="<?php echo $d['ifNoPlayer']; ?>" name="<?php echo $this->get_field_name('ifNoPlayer'); ?>" id="<?php echo $this->get_field_id('ifNoPlayer'); ?>" type="text" class="widefat" placeholder="Aucun joueur en ligne" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('serverip'); ?>">Adresse IP du serveur :</label><br />
		<input value="<?php echo $d['serverip']; ?>" name="<?php echo $this->get_field_name('serverip'); ?>" id="<?php echo $this->get_field_id('serverip'); ?>" type="text" class="widefat" placeholder="play.minefight.fr" /><br />
		<label for="<?php echo $this->get_field_id('serverport'); ?>">Port du serveur :</label><br />
		<input value="<?php echo $d['serverport']; ?>" name="<?php echo $this->get_field_name('serverport'); ?>" id="<?php echo $this->get_field_id('serverport'); ?>" type="text" class="widefat" placeholder="25565" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('nbSlot'); ?>">Nombre de slot du serveur (<abbr title="Nombre de slot disponible sur votre serveur">?</abbr>) :</label><br />
		<input value="<?php echo $d['nbSlot']; ?>" name="<?php echo $this->get_field_name('nbSlot'); ?>" id="<?php echo $this->get_field_id('nbSlot'); ?>" type="text" class="widefat" placeholder="30" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('avatarSize'); ?>">Taille des avatars (<abbr title="Nombre de pixel (Longueur x Hauteur) de l'image">?</abbr>) :</label><br />
		<input value="<?php echo $d['avatarSize']; ?>" name="<?php echo $this->get_field_name('avatarSize'); ?>" id="<?php echo $this->get_field_id('avatarSize'); ?>" type="text" class="widefat" placeholder="25" />
		</p>
		<p id="editCSS">
		<label for="<?php echo $this->get_field_id('styleCSS'); ?>">Modifier le style CSS (<a href="https://pastebin.com/u7H7G31e" target="_blank">CSS par défaut</a>) :</label><br />
		<textarea name="<?php echo $this->get_field_name('styleCSS'); ?>" id="<?php echo $this->get_field_id('styleCSS'); ?>" class="widefat" rows="10" style="resize: vertical;"><?php echo $d['styleCSS']; ?></textarea>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('displayAvatar'); ?>"><input name="<?php echo $this->get_field_name('displayAvatar'); ?>" id="<?php echo $this->get_field_id('displayAvatar'); ?>" type="checkbox" <?php if($d['displayAvatar'] == 'on'){ echo 'checked'; } ?> /> Afficher l'avatar des joueurs</label><br />
		<label for="<?php echo $this->get_field_id('displayCount'); ?>"><input name="<?php echo $this->get_field_name('displayCount'); ?>" id="<?php echo $this->get_field_id('displayCount'); ?>" type="checkbox" <?php if($d['displayCount'] == 'on'){ echo 'checked'; } ?> /> Afficher le nombre de joueur en ligne</label>
		</p>
		<hr style="border-top: 1px dashed #CCCCCC;" />
		<style>
			.div-pirmax {
				width: 100%;
				text-align: center;
			}
			.div-pirmax ul.link-pirmax {
				list-style: none;
				width: 100%;
			}
			.div-pirmax ul.link-pirmax li {
				margin: 0;
				padding: 0px;
				float: left;
				display: inline;
				width: 20%;
				text-align: center;
				font-size: 20px;
			}
			.div-pirmax ul.link-pirmax li a {
				text-decoration: none;
			}
		</style>
		<p align="center">
			<div class="div-pirmax">
				<ul class="link-pirmax">
					<li><a href="http://www.youtube.com/user/PirmaxLePoulpeRouge" target="_blank" title="Twitter"><span class="dashicons dashicons-twitter"></span></a></li>
					<li><a href="http://www.youtube.com/user/PirmaxLePoulpeRouge" target="_blank" title="YouTube"><span class="dashicons dashicons-video-alt3"></span></a></li>
					<li><a href="http://www.youtube.com/user/PirmaxLePoulpeRouge" target="_blank" title="Facebook"><span class="dashicons dashicons-facebook"></span></a></li>
					<li><a href="http://www.youtube.com/user/PirmaxLePoulpeRouge" target="_blank" title="Google+"><span class="dashicons dashicons-googleplus"></span></a></li>
					<li><a href="http://www.youtube.com/user/PirmaxLePoulpeRouge" target="_blank" title="Blog"><span class="dashicons dashicons-wordpress"></span></a></li>
				</ul>
				<br><br>
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DPPT5J9GXXFJY" target="_blank"><img src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donate_SM.gif"></a>
			</div>
		</p>
		<hr style="border-top: 1px dashed #CCCCCC;" />
		<?php

	}

}

?>