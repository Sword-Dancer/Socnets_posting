<?
define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__.'/facebook-php-sdk-v4-4.0-dev/src/Facebook/');
require_once __DIR__.'/facebook-php-sdk-v4-4.0-dev/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\Entities\AccessToken;
use Facebook\FacebookRedirectLoginHelper;


class Facebook
{
	private $user_id;
	private $page_id;
	private $app_id;
	private $app_secret;

	private $consumer_page_token;

	const MODULE_SETTINGS_CODE = "fb";

	/*
	 * TODO Корректная работа при не заданных настройках (ИД страницы и пр.)
	 */
	public function __construct($user_id = null, $page_id = null, $app_id = null, $app_secret = null, $page_token = null)
	{
		$arSettings = ModuleSettings::read(self::MODULE_SETTINGS_CODE);
		//if (array_search('', $arSettings)) {throw new \Bitrix\Main\DB\Exception;}

		$this->user_id = $user_id ? $user_id : $arSettings['user_id'];
		$this->page_id = $page_id ? $page_id : $arSettings['page_id'];
		$this->app_id = $app_id ? $app_id : $arSettings['app_id'];
		$this->app_secret = $app_secret ? $app_secret : $arSettings['app_secret'];

		$this->consumer_page_token = $page_token ? $page_token : null;

		FacebookSession::setDefaultApplication($this->app_id, $this->app_secret);
	}

	private function setSession()
	{
		$page_token = self::getToken();
		$session = new FacebookSession($page_token);

		try
		{
			$session->validate();
		}
		catch (\Exception $e)
		{
			$longLivedAccessToken = new AccessToken($page_token);
			$code = AccessToken::getCodeFromAccessToken($longLivedAccessToken);
			$newLongLivedAccessToken = AccessToken::getAccessTokenFromCode($code);
			$user_session = new FacebookSession($newLongLivedAccessToken);

			self::loginWithPerms();

			$page_token = false;
			$request = new FacebookRequest($user_session, 'GET', '/'.$this->user_id.'/accounts');
			$pageList = $request->execute()->getGraphObject()->asArray();
			foreach ($pageList['data'] as $arPage)
			{
				if ($arPage->id == $this->page_id)
				{
					$page_token = $arPage->access_token;
				}
			}

			self::saveToken($page_token);
			$session = new FacebookSession($page_token);
		}
		return $session;
	}


	private function getToken()
	{
		$arSettings = ModuleSettings::read(self::MODULE_SETTINGS_CODE);
		$token = $arSettings['access_page_token'];

		return $token;
	}
	private function saveToken($token)
	{
		$arSettings = ModuleSettings::read(self::MODULE_SETTINGS_CODE);
		$arSettings['access_page_token'] = $token;
		ModuleSettings::write($arSettings, self::MODULE_SETTINGS_CODE);
	}


	private function loginWithPerms()
	{
		$helper = new FacebookRedirectLoginHelper("http://".$_SERVER['SERVER_NAME']);
		$url = $helper->getLoginUrl(['manage_pages']);
		$context = stream_context_create(array(
			'http' => array(
				'ignore_errors'=>true,
				'method'=>'GET',
				'header'=>"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:37.0) Gecko/20100101 Firefox/37.0\r\n"
			)
		));
		file_get_contents($url, false, $context);
	}

	public function post($arFields)
	{
		$session = self::setSession();

		return (new FacebookRequest(
			$session, 'POST', '/'.$this->page_id.'/feed', $arFields
		))->execute()->getGraphObject();
	}
} 