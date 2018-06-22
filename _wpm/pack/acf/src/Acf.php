<?php
namespace WpmPack\Acf\src;

class Acf {
	protected $selector;
	protected $post_id;
	protected $format_value;

	public function __construct()
	{
		$this->post_id = $this->getPostId(null);
	}

	protected function getPostId($post_id)
	{
		global $post;
		if(!$post_id and !empty($post->ID)) return $post->ID;
		return $post_id;
	}

	public function postId($post_id)
	{
		$this->post_id = $post_id;
		return $this;
	}

	public function options()
	{
		$this->postId('option');
		return $this;
	}

	public function selector($selector, $format_value=true)
	{
		$this->selector = $selector;
		$this->format_value = $format_value;
		return $this;
	}

	public function first()
	{
		return $this->getField();
	}

	public function get()
	{
		if($this->selector) {
			return $this->getField();
		}
		return $this->getFields();
	}

	protected function getField()
	{
		if(!function_exists('get_field')) {
			return null;
		}

		return get_field($this->selector, $this->post_id, $this->format_value);
	}


	protected function getFields()
	{
		if(!function_exists('get_fields')) {
			return null;
		}

		return get_fields($this->post_id);
	}

}