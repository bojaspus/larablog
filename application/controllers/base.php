<?php

/**
 * Base Controller.
 */
class Base_Controller extends Controller
{
	/**
	 * @var array
	 */
	protected $user = false;
	
	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}
	
	/**
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		if ($this->user) {
			return $this->user;
		}
		
		if (!isset($_COOKIE['auth'])) {
			return false;
		}
		
		$redis = Redis::db();
		
		$auth = $_COOKIE['auth'];
		$uid = $redis->run('get', array("auth:{$auth}"));
		if (!$uid) {
			return false;
		}
		
		$user_info = json_decode($redis->run('get', array("user:{$uid}")), true);
		
		$user = array(
			'id' => $uid,
			'email' => $user_info['email'],
			'date_created' => $user_info['date_created'],
		);
		$this->user = $user;
		
		return true;
	}
	
	/**
	 * @return array
	 */
	public function getUser()
	{
		return $this->user;
	}
}
