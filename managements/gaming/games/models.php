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

class ModelsGames
{
	#####################################
	# Infos tables
	#####################################
	# TABLE_PAGES_GAMES
	#####################################
	# récupère les jeux
	#####################################
	public function getGames ($id = null)
	{
		$sql = New BDD();
		$sql->table('TABLE_PAGES_GAMES');

		if ($id !== null && is_numeric($id)) {
			$id = (int) $id;
			$where = array(
				'name' => 'id',
				'value' => $id
			);
			$sql->where($where);
			$sql->queryOne();
			return $sql->data;
		} else {
			$sql->queryAll();
			return $sql->data;
		}
	}
	#####################################
	# Ajoute un jeu
	#####################################
	public function addGame ($data = false)
	{
		$error  = 0;
		$error1 = 0;
		$dir = 'uploads/games/';
		if (!file_exists($dir)) {
			if (!mkdir($dir, 0777, true)) {
				throw new Exception('Failed to create directory');
			} else {
				$fopen  = fopen($dir.'/index.html', 'a+');
				$fclose = fclose($fopen);
			}
		}

		$extensions = array('.png', '.gif', '.jpg', '.jpeg');

		if ($_FILES['banner']['size'] != 0) {
			$extension = strrchr($_FILES['banner']['name'], '.');
			if (!in_array($extension, $extensions)) {
				$return['msg']  = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg'; ++$error;
				$return['type'] = 'alert';
			}
			if (move_uploaded_file($_FILES['banner']['tmp_name'], $dir.$_FILES['banner']['name'])) {
				$send['banner'] = $dir.$_FILES['banner']['name'];
			} else {
				$return['msg']  = 'Echec de l\'upload !';;
				$return['type'] = 'warning';
			}
		} else {
			$send['banner'] = null;
		}
		if ($_FILES['ico']['size'] != 0) {
			$extension = strrchr($_FILES['ico']['name'], '.');
			if (!in_array($extension, $extensions)) {
				$return['msg']  = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg'; ++$error1;
				$return['type'] = 'alert';
			}
			if ($error1 == 0) {
				if (move_uploaded_file($_FILES['ico']['tmp_name'], $dir.$_FILES['ico']['name'])) {
					$send['ico'] = $dir.$_FILES['ico']['name'];
				} else {
					$return['msg']  = 'Echec de l\'upload !'; ++$error1;
					$return['type'] = 'warning';
				}
			}
		} else {
			$send['ico'] = null;
		}

		/* Secure data before insert BDD */
		$send['name'] = Common::VarSecure($data['name'], '');

		$sql = New BDD();
		$sql->table('TABLE_PAGES_GAMES');
		$sql->sqlData($send);
		$sql->insert();
		$countRowUpdate = $sql->rowCount;

		if ($countRowUpdate != 0) {
			$return['msg']  = 'Vos informations ont été sauvegardées avec succès';
			$return['type'] = 'success';
		} else {
			$return['msg']  = 'Vos informations n\'ont pas été sauvegardées ou partiellement';
			$return['type'] = 'warning';
		}

		return $return;
	}

	public function editGame ($data)
	{
		$error  = 0;
		$error1 = 0;
		$dir = 'uploads/games/';
		if (!file_exists($dir)) {
			if (!mkdir($dir, 0777, true)) {
				throw new Exception('Failed to create directory');
			} else {
				$fopen  = fopen($dir.'/index.html', 'a+');
				$fclose = fclose($fopen);
			}
		}
		$extensions = array('.png', '.gif', '.jpg', '.jpeg');

		if ($_FILES['banner']['size'] != 0) {
			$extension = strrchr($_FILES['banner']['name'], '.');
			if (!in_array($extension, $extensions)) {
				$return['msg']  = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg'; ++$error;
				$return['type'] = 'alert';
			}
			if ($error == 0) {
				if (move_uploaded_file($_FILES['banner']['tmp_name'], $dir.$_FILES['banner']['name'])) {
					$edit['banner'] = $dir.$_FILES['banner']['name'];
				} else {
					$return['msg']  = 'Echec de l\'upload !'; ++$error;
					$return['type'] = 'warning';
				}
			}
		} else {
			$edit['banner'] = $data['banner2'];
		}
		if ($_FILES['ico']['size'] != 0) {
			$extension = strrchr($_FILES['ico']['name'], '.');
			if (!in_array($extension, $extensions)) {
				$return['msg']  = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg'; ++$error1;
				$return['type'] = 'alert';
			}
			if ($error1 == 0) {
				if (move_uploaded_file($_FILES['ico']['tmp_name'], $dir.$_FILES['ico']['name'])) {
					$edit['ico'] = $dir.$_FILES['ico']['name'];
				} else {
					$return['msg']  = 'Echec de l\'upload !'; ++$error1;
					$return['type'] = 'warning';
				}
			}
		} else {
			$edit['ico'] = $data['ico2'];
		}

		/* Secure data before insert BDD */
		$id                  = (int) $data['id'];
		$edit['name']        = Common::VarSecure($data['name'], '');

		$sql = New BDD();
		$sql->table('TABLE_PAGES_GAMES');
		$sql->where(array('name'=>'id','value'=> $id));
		$sql->sqlData($edit);
		$sql->update();
		$countRowUpdate = $sql->rowCount;

		if ($countRowUpdate != 0) {
			$return['msg']  = 'Vos informations ont été sauvegardées avec succès';
			$return['type'] = 'success';
		} else {
			$return['msg']  = 'Vos informations n\'ont pas été sauvegardées ou partiellement';
			$return['type'] = 'warning';
		}

		return $return;
	}

	public function delGame ($id = null)
	{
		if ($id && is_numeric($id)) {
			$game = self::getGames ($id);
			// delete file
			if (!empty($game->banner)) {
				if (is_file($game->banner)) {
					@unlink($game->banner);
				}
			}
			// delete file
			if (!empty($game->ico)) {
				if (is_file($game->ico)) {
					@unlink($game->ico);
				}
			}
			// SQL DELETE
			$sql = New BDD();
			$sql->table('TABLE_PAGES_GAMES');
			$sql->where(array('name'=>'id','value' => $id));
			$sql->delete();
			// SQL RETURN NB DELETE
			if ($sql->rowCount == 1) {
				$return = array(
					'type' => 'success',
					'text' => DEL_GAME_SUCCESS
				);
			} else {
				$return = array(
					'type' => 'warning',
					'text' => DEL_GAME_ERROR
				);
			}
			return $return;
		}
	}
}