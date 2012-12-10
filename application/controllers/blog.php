<?php

/**
 * Blog Controller.
 */
class Blog_Controller extends Base_Controller
{
	/**
	 * @return Laravel\View
	 */
	public function action_index()
	{
		$redis = Redis::db();
		
		$blog_ids = $redis->run('LRANGE', array(
			'global:timeline',
			Input::get('start', 0),
			Input::get('count', 100),
		));
		
		$blogs = [];
		foreach ($blog_ids as $id) {
			$blogs[$id] = json_decode($redis->run('get', array("blog:{$id}")), true);
		}
		
		return View::make('blog.index', array(
			'blogs' => $blogs,
		));
	}
	
	/**
	 * @return Laravel\Redirect|Laravel\View
	 */
	public function action_new()
	{
		if (!$this->isLoggedIn()) {
			return Redirect::to_action('auth/login');
		}
		
		if (Request::method() != 'POST') {
			return View::make('blog.new', array());
		}
		
		$user = $this->getUser();
		
		$owner_id = $user['id'];
		$title = Input::get('title');
		$body  = Input::get('body');
		$ts = new \DateTime('now');
		$ts = $ts->format('Y-m-d H:i:s');
		
		$redis = Redis::db();
		
		$blog_id = $redis->run('incr', array('global:next_blog_id'));
		$redis->run('set', array("blog:{$blog_id}", json_encode(array(
			'owner_id' => $owner_id,
			'date_created' => $ts,
			'title' => $title,
			'body' => $body,
		))));
		
		$redis->run('lpush', array('global:timeline', $blog_id));
		$redis->run('ltrim', array('global:timeline', 0, 1000));
		
		return Redirect::to_action('blog/view', array(
			'id' => $blog_id,
		));
	}
	
	/**
	 * @return Laravel\View
	 */
	public function action_view($id = null)
	{
		$redis = Redis::db();
		
		$blog_id = (int) Input::get('id', $id);
		$blog = $redis->run('get', array("blog:{$blog_id}"));
		$blog = $blog ? json_decode($blog, true) : false;
		
		if (!$blog) {
			return Redirect::to_action('blog/index');
		}
		
		return View::make('blog.view', array(
			'blog' => $blog,
		));
	}
}
