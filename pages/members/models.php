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

class ModelsMembers
{
	public function GetUsers ($where = false)
	{
		$nbpp = (int) 10;

		$page = (Dispatcher::RequestPages() * $nbpp) - $nbpp;

		$return = array();

		$sql = New BDD();
		$sql->table('TABLE_USERS');
		$sql->orderby(array(array('name' => 'username', 'type' => 'ASC')));
		$sql->limit(array(0 => $page, 1 => $nbpp), true);
		$sql->queryAll();
		$return = $sql->data;
		unset($sql);

		foreach ($return as $k => $v) {
			$sql = New BDD();
			$sql->table('TABLE_USERS_PROFILS');
			$where = 	array(
							'name'  => 'hash_key',
							'value' => $v->hash_key
						);
			$sql->where($where);
			$sql->queryOne();
			if (Secure::isUrl($sql->data->websites) === false) {
				$sql->data->websites = null;
			}
			$return[$k]->profils = $sql->data;
			unset($sql);
		}

		return $return;
	}

	public function GetLastPost ($hash_key)
	{
		$return = array();

		$sql = New BDD();
		$sql->table('TABLE_FORUM_POST');
		$sql->where(array('name' => 'author', 'value' => $hash_key));
		$sql->orderby(array(array('name' => 'id', 'type' => 'DESC')));
		$sql->limit(3);
		$sql->queryAll();
		$return = $sql->data;
		unset($sql);

		return $return;
	}

	public function addFriendSQL ($hash_key = false)
	{
		if ($hash_key !== false && ctype_alnum($hash_key)) {
			$sql = New BDD;
			$sql->table(TABLE_USERS_PROFILS);
			$where = array('name' => 'hash_key', 'value' => $_SESSION['USER']['hash_key']);
			$sql->where($where);
			$sql->queryOne();
			$count  = $sql->rowCount;
			$data   = $sql->data;
			unset($sql);
			if ($count == 0) {
				return null;
			} else {
				$friends = explode('|', $data->friends);
				if (!in_array($hash_key, $friends)) {
					$friends[] = $hash_key;
					$implode   = implode('|', $friends);
					$sql = New BDD;
					$sql->table(TABLE_USERS_PROFILS);
					$where = array('name' => 'hash_key', 'value' => $_SESSION['USER']['hash_key']);
					$sql->where($where);
					$update['friends'] = $implode;
					$sql->sqlData($update);
					$sql->update();
					return $sql->rowCount;
				}
			}
		} else {
			return null;
		}
	}

	public function getJson ()
	{
		$user   = array();
		$return = array();

		$sql = New BDD();
		$sql->table('TABLE_USERS');
		$sql->orderby(array(array('name' => 'username', 'type' => 'DESC')));
		$sql->fields(array('hash_key', 'username', 'last_visit'));
		$sql->queryAll();

		foreach ($sql->data as $k => $v) {
			$sql = New BDD();
			$sql->table('TABLE_USERS_PROFILS');
			$where = 	array(
							'name'  => 'hash_key',
							'value' => $v->hash_key
						);
			$sql->where($where);
			$sql->fields(array('websites', 'country'));
			$sql->queryOne();
			$user[$k]->username = $v->username;
			$user[$k]->last_visit = $v->last_visit;
			$user[$k]->websites = $sql->data->websites;
			$user[$k]->country = $sql->data->country;
			$return['data'] = $user;
		}

		return $return;
	}

	public function getViewUser ($name)
	{
		$return = array();

		if (!empty($name)) {
			$name = trim($name);

			$sql = New BDD;
			$sql->table('TABLE_USERS');
			$sql->where(array('name' => 'username', 'value' => $name));
			$sql->queryOne();
			$data = $sql->data;

			if (!empty($data)) {
				$data->main_groups = explode('|', $data->main_groups);
				if (empty($data->avatar) OR !is_file($data->avatar)) {
					$data->avatar = 'assets/images/default_avatar.jpg';
				}
				$return = $data;
			}
		}

		return $return;
	}
}

