<?php
/**
 * Bel-CMS [Content management system]
 * @version 1.0.0
 * @link https://bel-cms.be
 * @link https://determe.be
 * @license http://opensource.org/licenses/GPL-3.-copyleft
 * @copyright 2014-2019 Bel-CMS
 * @author as Stive - stive@determe.be
 */

if (!defined('CHECK_INDEX')) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit(ERROR_INDEX);
}

Common::constant(array(
	#####################################
	# Fichier lang en français - Pages game
	#####################################
	'ADMIN_NAMEPAGE'   => 'Administration de la page jeux',
	'DEL_GAME_SUCCESS' => 'Effacement du jeu avec succès',
	'DEL_GAME_ERROR'   => 'Erreur lors de l\'éfacement du jeu',
));