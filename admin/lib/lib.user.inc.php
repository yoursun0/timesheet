<?php
class CS_User{
	private $info;
	private $acl_ids = array();
	private $acl_keys = array();

	public function __construct(&$data = false) {
		$this->load($data);
	}
		
	public function load(&$data) {
		$this->info 	= $data['info'];
		if (isset($data['acl']) && is_array($data['acl'])) {
		$this->acl_ids 	= array_keys($data['acl']);
		$this->acl_keys = array_values($data['acl']);				
		}
		return true;
	}
	
	public function isAllowed($value) {
		$value = trim($value);
		return is_numeric($value) ? $this->isAllowedId($value) : $this->isAllowedKey($value);
	}
	public function isAllowedId($id) {
		return in_array(trim($id),$this->acl_ids);
	}
	public function isAllowedKey($key) {
		return in_array(strtoupper(trim($key)),$this->acl_keys);		
	}
	
	public function getId() {
		return $this->getInfo('id');
	}
	public function getName() {
		return $this->getInfo('name');
	}
	public function getEmail() {
		return $this->getInfo('email');
	}

	private function getInfo ($key) {
		return isset($this->info[$key]) ? $this->info[$key] : false;
	}
}

class CS_Auth {
	/**
	 * @var CS_Database
	 */
	private $dbo;
	/**
	 * @var CS_User
	 */
	private $user;
	private $errorMessage = array();

	public function __construct($dbo = false) {
		$this->setDB($dbo);
		$this->user = new CS_User();
	}

	public function setDB($dbo) {
		if (isset($dbo) && !empty($dbo)) {
			$this->dbo = $dbo;
			return true;
		}
		return false;
	}
	public function errorInfo() {
		switch (count($this->errorMessage)) {
			case 0:return '';
			case 1:return $this->errorMessage[0];
			default:return var_export($this->errorMessage,true);
		}
	}

	public function timesheetLogin() {

		
		if (!isset($_SESSION)) {
			session_start();				
		}	
		$room_id = $_SESSION['DefaultRoomID'];
		
		$user_id = $this->dbo->getOne("SELECT `id` FROM `{$GLOBALS['db_table']['user']}` WHERE `room_id`=$room_id");

		$login_mode = LOGIN_MODE::LOCAL;
		
		$_SESSION[CONFIG::LOGIN_FLAG] = $login_mode;
		$_SESSION['user']['info'] = array();
		$_SESSION['user']['acl'] = $this->getAcl($user_id);
		return true;
	}
	public function login($login,$password=""){		
		$login = trim($login);
		$password =trim($password);
		
		if (empty($login)) {
			$this->errorMessage[] = 'login name cannot be empty';
			return false;
		}

		$sth = $this->dbo->db->prepare("SELECT `id`,`auth_type` FROM `{$GLOBALS['db_table']['user']}` WHERE `login`=? AND `status`='A'");
		$sth->bindParam(1,$login, PDO::PARAM_STR, 30);

		if ($sth->execute()) {	//user found

			//get user_id and auth_type
			list($user_id,$auth_type) = $sth->fetch(PDO::FETCH_NUM);
			
			//auth by type
			switch (strtoupper($auth_type)) {
				case 'LDAP':
					$user_info = $this->ldapAuth($user_id,$password);
					$login_mode = LOGIN_MODE::LOCAL;
					break;
				case 'SSO':
					$user_info = $this->ssoAuth($user_id,$password);
					$login_mode = LOGIN_MODE::SSO;
					break;
				default:
					$user_info = $this->localAuth($user_id,$password);
					$login_mode = LOGIN_MODE::LOCAL;
					break;
			}
			
			//if auth failure, return false
			if (false === $user_info) {
				$this->errorMessage[] = 'login fail';
				return false;
			}
			
			//login success, save data to session
			if (!isset($_SESSION)) {
				session_start();				
			}			
			$_SESSION[CONFIG::LOGIN_FLAG] = $login_mode;
			$_SESSION['user']['info'] = $user_info;
			$_SESSION['user']['acl'] = $this->getAcl($user_id);
			return true;

		} else { 
			//user not found, return false
			$this->errorMessage[] = 'login fail';
			return false;
		}
	}
	public function logout() {
		//clear session
		if (!isset($_SESSION)) {
			session_start();
		}
		session_destroy();
	}

	public function getAcl($uid) {
		//get function id
		$sql = "
SELECT `f`.`id`, `f`.`key` FROM
  `{$GLOBALS['db_table']['user']}` `u`,
  `{$GLOBALS['db_table']['user_role_rel']}` `u_r`,
  `{$GLOBALS['db_table']['role_func_rel']}` `r_f`,
  `{$GLOBALS['db_table']['func']}` `f`,
  `{$GLOBALS['db_table']['role']}` `r`
WHERE `u`.`id` = $uid
  AND `u`.`id` = `u_r`.`user_id`
  AND `u_r`.`role_id`=`r_f`.`role_id`
  AND `u_r`.`role_id`=`r`.`id` AND `r`.`status`=1
  AND `r_f`.`func_id`=`f`.`id` AND `f`.`status`=1";
		return $this->dbo->getArray($sql,2);
	}
	
	private function localAuth($user_id,$password) {
		$password = md5($password);
		$sql = "SELECT * FROM `{$GLOBALS['db_table']['user']}` WHERE `id`=$user_id AND `password`='$password' LIMIT 1";
		return $this->dbo->getRow($sql);
	}
	private function ssoAuth($user_id,$password) {
		//todo
		return false;
	}
	private function ldapAuth($user_id,$password) {
		//filter empty password
		if (''==$password) {
			return false;
		}

		//get ldap param.
		if (false === ($row = $this->dbo->getRow("SELECT `auth_login`,`auth_domain` FROM `{$GLOBALS['db_table']['user']}` WHERE `id`=$user_id"))) {
			return false;
		}
		
		$ldap_user = "{$row['auth_domain']}\\{$row['auth_login']}";

		//$dn = "CN=Schema,CN=Configuration,DC=cshk,DC=com";
		$ldapconn = ldap_connect(CONFIG::LDAP_SERVER);
		if(!$ldapconn) {
			return false;
		}
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		$ldapbind = @ldap_bind($ldapconn,$ldap_user,$password);
		if(!$ldapbind) {
			return false;
		}
		
		ldap_unbind($ldapconn);
		return $this->dbo->getRow("SELECT * FROM `{$GLOBALS['db_table']['user']}` WHERE `id`=$user_id");

	}
}
?>