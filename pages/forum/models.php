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

###  TABLE_NEWSLETTER
###  TABLE_FORUM_POST
###  TABLE_FORUM_POSTS
###  TABLE_FORUM_THREADS
class ModelsForum
{
	#####################################
	# Récupère les noms des forums
	#####################################
	public function getForum ()
	{
		$return = array();
		$sql = New BDD();
		$sql->table('TABLE_FORUM');
		$sql->orderby(array(array('name' => 'orderby', 'type' => 'ASC')));
		$where = array(
			'name' => 'activate',
			'value' => 1
		);
		$sql->where($where);
		$sql->queryAll();
		$return = $sql->data;

		foreach ($return as $k => $v) {
			$access = false;
			$groups = explode('|', $v->access_groups);
			foreach ($groups as $v_access) {
				if ($v_access == 0) {
					$access = true;
					break;
				} else {
					if (Users::getInfosUser($_SESSION['USER']['HASH_KEY']) !== false) {
						$v_access = explode('|', $v_access);
						foreach ($v_access as $key_access => $value_access) {
							if (in_array($value_access, Users::getGroups($_SESSION['USER']['HASH_KEY']))) {
								$access = true;
								break;
							}
						}
					}
				}
			}

			if ($access === false) {
				unset($return[$k]);
			}
		}


		return $return;
	}
	#####################################
	# Récupère les noms des forums
	#####################################
	public function getAccessForum ($data)
	{
		$data = (int) $data;
		$return = array();
		$sql = New BDD();
		$sql->table('TABLE_FORUM');
		$sql->orderby(array(array('name' => 'orderby', 'type' => 'ASC')));
		$where = array(
			'name' => 'id',
			'value' => $data
		);
		$sql->where($where);
		$sql->queryOne();
		$return = $sql->data;
		if ($return) {
			$return = explode('|', $return->access_admin);
		}
		return $return;
	}
	#####################################
	# Récupère les catégories du forum
	#####################################
	public function getCatForum ($id)
	{
		$id = (int) $id;
		$return = array();
		$sql = New BDD();
		$sql->table('TABLE_FORUM_THREADS');
		$sql->orderby(array(array('name' => 'orderby', 'type' => 'ASC')));
		$where = array(
			'name' => 'id_forum',
			'value' => $id
		);
		$sql->where($where);
		$sql->queryAll();
		$return = $sql->data;
		return $return;
	}
	#####################################
	# Récupère le dernier post
	#####################################
	public function getLastPostForum ($id)
	{
		$id = (int) $id;
		$return = array();
		$sql = New BDD();
		$sql->table('TABLE_FORUM_POST');
		$sql->orderby(array(array('name' => 'id', 'type' => 'DESC')));
		$where = array(
			'name' => 'id_threads',
			'value' => $id
		);
		$sql->where($where);
		$sql->limit(1);
		$sql->queryOne();
		$return = $sql->data;
		return $return;
	}
	#####################################
	# Récupère le nombre de post
	#####################################
	public function CountSjForum ($id)
	{
		$id = (int) $id;
		$sql = New BDD();
		$sql->table('TABLE_FORUM_POST');
		$where = array(
			'name' => 'id_threads',
			'value' => $id
		);
		$sql->where($where);
		$sql->count();
		$return = $sql->data;
		return $return;
	}
	#####################################
	# Récupère les posts
	#####################################
	public function GetThreadsPost ($id)
	{
		$id = (int) $id;
		$sql = New BDD();
		$sql->table('TABLE_FORUM_POST');
		$sql->orderby(array(array('name' => 'id', 'type' => 'DESC')));
		$where = array(
			'name' => 'id_threads',
			'value' => $id
		);
		$sql->where($where);
		$sql->queryAll();
		$return = $sql->data;
		return $return;
	}
	public function GetThreadName ($id = null)
	{
			$sql = New BDD();
			$sql->table('TABLE_FORUM_THREADS');
			$whereThreads = array(
				'name'  => 'id',
				'value' => (int) $id
			);
			$sql->where($whereThreads);
			$sql->fields('title');
			$sql->queryOne();
			$return = $sql->data;
			return $return;		
	}
	
	#####################################
	# Récupère le dernier posts
	#####################################
	public function getLastPostsForum ($id)
	{
		$id = (int) $id;
		$return = array();
		$sql = New BDD();
		$sql->table('TABLE_FORUM_POSTS');
		$sql->orderby(array(array('name' => 'id', 'type' => 'DESC')));
		$where = array(
			'name' => 'id_post',
			'value' => $id
		);
		$sql->where($where);
		$sql->queryOne();
		$return = $sql->data;
		return $return;
	}
	#####################################
	# Récupère le post origine
	#####################################
	public function getLastPostsOriginForum ($id, $id_threads)
	{
		$id = Common::SecureRequest($id);
		$return = array();
		$sql = New BDD();
		$sql->table('TABLE_FORUM_POST');
		$where[] = array(
			'name' => 'id',
			'value' => $id
		);
		$where[] = array(
			'name' => 'id_threads',
			'value' => $id_threads
		);
		$sql->where($where);
		$sql->queryOne();
		$return = $sql->data;
		return $return;
	}
	public function editpost ($id = null)
	{
		$return = null;
		if ($id != null) {
			$id = Common::SecureRequest($id);
			$sql = New BDD();
			$sql->table('TABLE_FORUM_POSTS');
			$sql->where(array('name' => 'id', 'value' => $id));
			$sql->queryOne();
			$return = $sql->data;
		}
		return $return;
	}
	public function sendEditPost ($d)
	{
		$data['info_text'] = Common::VarSecure($d['info_text']);
		$update = New BDD();
		$update->table('TABLE_FORUM_POSTS');
		$where[] = array(
			'name'  => 'id',
			'value' => Common::SecureRequest($d['id'])
		);
		$where[] = array(
			'name'  => 'id_post',
			'value' => Common::SecureRequest($d['id_post'])
		);
		$update->where($where);
		$options = $data['info_text'];
		$update->sqlData(array('content' => $options));
		$update->update();
		if ($update->rowCount == 1) {
			$return['msg']  = EDIT_SUCCESS;
			$return['type'] = 'success';
		} else {
			$return['msg']  = EDIT_FALSE;
			$return['type'] = 'error';
		}
		return $return;	
	}
	#####################################
	# Récupère les posts
	#####################################
	public function GetPosts($id = false, $id_supp = null)
	{
		$return = false;
		if ($id && $id_supp) {
			$id_supp = (int) $id_supp;
			// Récupère le 1er message du post //
			$this->sql = New BDD();
			$this->sql->table('TABLE_FORUM_POST');
			$this->sql->where(array('name' => 'id', 'value' => $id_supp));
			$this->sql->limit(1);
			$this->sql->queryAll();
			$firstPost = $this->sql->data;
			unset($this->sql);
			// Récupère les reponses du post //
			$this->sql = New BDD();
			$this->sql->table('TABLE_FORUM_POSTS');
			$this->sql->where(array('name' => 'id_post', 'value' => $id_supp));
			$this->sql->orderby(array(array('name' => 'date_post', 'type' => 'ASC')));
			$this->sql->queryAll();
			$posts = $this->sql->data;
			// Assemble les deux tableaux
			$return = array_merge($firstPost, $posts);
			foreach ($return as $k => $v) {
				$authorId = $v->author;
				$author = Users::getInfosUser($authorId);
				// Fait corrépondre leurs ID avec leur username
				$return[$k]->author = Users::hashkeyToUsernameAvatar($authorId);
				// Fait corrépondre leurs ID avec leur avatar
				$return[$k]->avatar = Users::hashkeyToUsernameAvatar($authorId, 'avatar');
				// Fait corrépondre leurs ID avec leur date d'inscription
				$return[$k]->registration = (isset($author->date_registration)) ? Common::TransformDate($author->date_registration) : '';
				// Récupère les options et les transformer en Booleen
				// Les like sont transoformer en (int)
				$options = explode('|', $v->options);
				foreach ($options as $k_opt => $v_opt) {
					$tmp_opt = explode('=', $v_opt);
					$options[$tmp_opt[0]] = $tmp_opt[1] == 1 ? true : false;
					if (isset($options['like'])) {
						$options['like'] = $options['like'] == false ? (int) 0 : $options['like'];
					}
					unset($options[$k_opt], $tmp_opt);
				}
				$return[$k]->options = $options;
			}
		}
		return $return;
	}
	#####################################
	# Ajoute une vue supplèmentaire au post
	#####################################
	public function addView ($id = false) {
		if ($id && is_int($id)) {
			$get = New BDD();
			$get->table('TABLE_FORUM_POST');
			$where = array(
				'name'  => 'id',
				'value' => (int) $id
			);
			$get->where($where);
			$get->queryOne();
			$data = $get->data;

			if ($get->rowCount != 0) {
				$options = Common::transformOpt($data->options);
				$options['view'] = (int) $options['view'] + 1;
				$options = Common::transformOpt($options, true);

				$update = New BDD();
				$update->table('TABLE_FORUM_POST');
				$update->where($where);
				$update->sqlData(array('options' => $options));
				$update->update();
			}
		}
	}
	#####################################
	# Lock le post
	#####################################
	public function lock ($id = false)
	{
		if ($id) {
			$id = Common::SecureRequest($id);
			$where = array('name' => 'id', 'value' => $id);
			# recupere le post
			$get = New BDD();
			$get->table('TABLE_FORUM_POST');
			$get->where($where);
			$get->queryOne();
			$data = $get->data;

			$options = Common::transformOpt($data->options);
			$options['lock'] = (int) 1;
			$options = Common::transformOpt($options, true);

			# update le post
			$update = New BDD;
			$update->table('TABLE_FORUM_POST');
			$update->where($where);
			$update->sqlData(array('options' => $options));
			$update->update();
			# verifie si c'est bien inserer
			if ($update->rowCount == 1) {
				$return['msg']  = LOCK_SUCCESS;
				$return['type'] = 'success';
			} else {
				$return['msg']  = ERROR_LOCK_BDD;
				$return['type'] = 'error';
			}
			# return le resulat
			return $return;
		}
	}
	#####################################
	# Delock le post
	#####################################
	public function unlock ($id = false)
	{
		if ($id) {
			$id = (int) $id;
			$where = array('name' => 'id', 'value' => $id);
			# recupere le post
			$get = New BDD();
			$get->table('TABLE_FORUM_POST');
			$get->where($where);
			$get->queryOne();
			$data = $get->data;

			$options = Common::transformOpt($data->options);
			$options['lock'] = (int) 0;
			$options = Common::transformOpt($options, true);

			# update le post
			$update = New BDD;
			$update->table('TABLE_FORUM_POST');
			$update->where($where);
			$update->sqlData(array('options' => $options));
			$update->update();
			# verifie si c'est bien inserer
			if ($update->rowCount == 1) {
				$return['msg']  = UNLOCK_SUCCESS;
				$return['type'] = 'success';
			} else {
				$return['msg']  = ERROR_UNLOCK_BDD;
				$return['type'] = 'error';
			}
			# return le resulat
			return $return;
		}
	}
	#####################################
	# Supprime le(s) post(s)
	#####################################
	public function delpost ($id = false)
	{
		if ($id) {
			$id = (int) $id;
			$where = array('name' => 'id', 'value' => $id);
			$del = New BDD();
			$del->table('TABLE_FORUM_POST');
			$del->where($where);
			$del->delete();
			$true = $del->rowCount;
			unset($del);
			$where = array('name' => 'id_post', 'value' => $id);
			$del = New BDD();
			$del->table('TABLE_FORUM_POSTS');
			$del->where($where);
			$del->delete();
			# verifie si c'est bien supprimer
			if ($true == 1) {
				$return['msg']  = DEL_POST_SUCCESS;
				$return['type'] = 'success';
			} else {
				$return['msg']  = DEL_POST_ERROR;
				$return['type'] = 'error';
			}
			# return le resulat
			return $return;
		}
	}
	#####################################
	# Réponse au post
	#####################################
	public function SubmitPost($data)
	{
		if (Users::getInfosUser($_SESSION['USER']['HASH_KEY']) === false) {
			$return['msg']  = ERROR_LOGIN;
			$return['type'] = 'warning';
			return $return;
		}

		if (!isset($_SESSION['REPLYPOST'])) {
			$return['msg']  = ERROR_ID;
			$return['type'] = 'warning';
			return $return;
		}

		if ($_SESSION['REPLYPOST'] != $data['id']) {
			$return['msg']  = ERROR_ID;
			$return['type'] = 'warning';
			return $return;
		} else {
			unset($_SESSION['REPLYPOST']);
		}

		$upload = Common::Upload('file', 'forum');
		if ($upload == UPLOAD_FILE_SUCCESS) {
			$insert['attachment'] = 'uploads/forum/'.Common::FormatName($_FILES['file']['name']);
			$upload = '<br>'.$upload;
		} else if ($upload == UPLOAD_NONE) {
			$insert['attachment'] = '';
			$upload = '';
		} else {
			$insert['attachment'] = '';
			$upload = '';
		}

		$insert['content'] = Common::VarSecure($data['info_text']);
		$insert['id_post'] = (int) $data['id'];
		$insert['author']  = $_SESSION['USER']['HASH_KEY'];
		$insert['options'] = 'like=0|report=0';

		$BDD = New BDD();
		$BDD->table('TABLE_FORUM_POSTS');
		$BDD->sqlData($insert);
		$BDD->insert();

		if ($BDD->rowCount == 1) {
			self::addPlusPost($BDD->sqlData['id_post']);
			$return['msg']  = 'Enregistrement de la réponse en cours...'.$upload;
			$return['type'] = 'success';
		} else {
			$return['msg']  = ERROR_BDD;
			$return['type'] = 'danger';
		}

		return $return;
	}
	#####################################
	# Crée un nouveau post
	#####################################
	public function SubmitThread($id, $data)
	{
		# teste si utilisateur est connecté
		if (Users::getInfosUser($_SESSION['USER']['HASH_KEY']) === false) {
			$return['msg']  = ERROR_LOGIN;
			$return['type'] = 'info';
			return $return;
		}
		# check ID du forum
		if ($_SESSION['NEWTHREADS'] != $id) {
			$return['msg']  = ERROR_ID;
			$return['type'] = 'warning';
			return $return;
		} else {
			unset($_SESSION['NEWTHREADS']);
		}
		# les données à inserer
		$insert['id']         = NULL;
		$insert['id_threads'] = (int) $id;
		$insert['title']      = strip_tags(fixUrl($data['title']));
		$insert['author']     = $_SESSION['USER']['HASH_KEY'];
		$insert['options']    = 'lock=0|like=0|report=0|pin=0|view=0|post=0';
		$insert['date_post']  = date("Y-m-d H:i:s");
		$insert['attachment'] = '';
		$insert['content']    = Common::VarSecure(trim($data['content']));
		if ($insert['content'] == '') {
			$insert['content'] = 'null';
		}
		# insert en BDD
		$sql = New BDD();
		$sql->table('TABLE_FORUM_POST');
		$sql->sqlData($insert);
		$sql->insert();
		# verifie si c'est bien inserer
		if ($sql->rowCount == 1) {
			$return['msg']  = 'Enregistrement du nouveau post en cours...';
			$return['type'] = 'success';
		} else {
			$return['msg']  = ERROR_BDD;
			$return['type'] = 'error';
		}
		# return le resulat
		return $return;
	}
	#####################################
	# Ajoute un +1 au post 
	#####################################
	public function addPlusPost ($id = false) {
		if ($id && is_int($id)) {
			$get = New BDD();
			$get->table('TABLE_FORUM_POST');
			$where = array(
				'name'  => 'id',
				'value' => (int) $id
			);
			$get->where($where);
			$get->queryOne();
			$data = $get->data;

			$options = Common::transformOpt($data->options);
			$options['post'] = (int) $options['post'] + 1;
			$options = Common::transformOpt($options, true);

			$update = New BDD();
			$update->table('TABLE_FORUM_POST');
			$update->where($where);
			$update->sqlData(array('options' => $options));
			$update->update();

		}
	}
	#####################################
	# Secutity level +1
	#####################################
	public function securityPost ($id)
	{
		$return = false;

		if ($id && is_int($id)) {
			$sqlThreads = New BDD();
			$sqlThreads->table('TABLE_FORUM_THREADS');
			$whereThreads = array(
				'name'  => 'id',
				'value' => (int) $id
			);
			$sqlThreads->where($whereThreads);
			$sqlThreads->queryOne();
			$dataThreads = $sqlThreads->data;
			if (!empty($dataThreads)) {
				$idForum  = $dataThreads->id_forum;
				$sqlForum = New BDD();
				$sqlForum->table('TABLE_FORUM');
				$whereForum = array(
					'name'  => 'id',
					'value' => (int) $idForum
				);
				$sqlForum->where($whereForum);
				$sqlForum->queryOne();
				$dataForum = $sqlForum->data;
				if (!empty($dataForum)) {
					if ($dataForum->access_groups == 0) {
						$return = true;
					} else {
						$return = explode('|', $dataForum->access_groups);
					}
				}
			}
		}

		return $return;
	} 
}