<?
require_once __DIR__.'/twitteroauth-0.5.3/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
	private $consumer_key;
	private $consumer_secret;
	private $oauth_token;
	private $oauth_secret;

	const MODULE_SETTINGS_CODE = "tw";

	public function __construct()
	{
		$arSettings = ModuleSettings::read(self::MODULE_SETTINGS_CODE);

		$this->consumer_key = $arSettings['consumer_key'];
		$this->consumer_secret = $arSettings['consumer_secret'];
		$this->oauth_token = $arSettings['oauth_token'];
		$this->oauth_secret = $arSettings['oauth_secret'];
	}

	public function post($mess)
	{
		$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_secret);
		$connection->get("account/verify_credentials");
		return $connection->post('statuses/update', array('status' => $mess));
	}
} 