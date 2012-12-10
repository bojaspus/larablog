<?php

/**
 * Auth Controller.
 */
class Auth_Controller extends Base_Controller
{
	/**
	 * @return Laravel\View
	 */
	public function action_register()
	{
		if ($this->isLoggedIn()) {
			return Redirect::to_action('blog/index');
		}
		
		if (Request::method() != 'POST') {
			return View::make('auth.register', array());
		}
		
		$redis = Redis::db();
		
		$email = Input::get('email');
		$password = Input::get('password');
		$ts = new \DateTime('now');
		$ts = $ts->format('Y-m-d H:i:s');
		
		$uid = $redis->run('incr', array('global:next_user_id'));
		$redis->run('set', array("email:{$email}:user", $uid));
		$redis->run('set', array("user:{$uid}:email", $email));
		$redis->run('set', array("user:{$uid}:password", sha1($password)));
		$redis->run('set', array("user:{$uid}", json_encode(array(
			'email' => $email,
			'date_created' => $ts,
		))));
		
		$auth = md5('secretsalt'.md5($email).time());
		$redis->run('set', array("user:{$uid}:auth", $auth));
		$redis->run('set', array("auth:{$auth}", $uid));
		
		setcookie("auth", $auth, time() + 3600 * 24 * 365, '/');
		
		return Redirect::to_action('blog/index');
	}
	
	/**
	 * @return Laravel\Redirect|Laravel\View
	 */
	public function action_login()
	{
		if ($this->isLoggedIn()) {
			return Redirect::to_action('blog/index');
		}
		
		if (Request::method() != 'POST') {
			return View::make('auth.login', array());
		}
		
		$redis = Redis::db();
		
		$email = Input::get('email');
		$password = Input::get('password');
		
		$uid = $redis->run('get', array("email:{$email}:user"));
		if (!$uid) {
			return Redirect::to_action('auth/login');
		}
		
		$enc_pass = $redis->run('get', array("user:{$uid}:password"));
		if ($enc_pass != sha1($password)) {
			return Redirect::to_action('auth/login');
		}
		
		setcookie("auth", $redis->run('get', array("user:{$uid}:auth")), time() + 3600 * 24 * 365, '/');
		
		return Redirect::to_action('blog/index');
	}
}
