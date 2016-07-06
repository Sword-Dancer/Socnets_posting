<?
class ModuleSettings
{
	public static function read($settings_code)
	{
		$json = \COption::GetOptionString(self::getModuleId(), $settings_code);
		$arSettings = $json ? json_decode($json, true) : self::getDefaultSettings($settings_code);

		return $arSettings;
	}
	public static function write($arSettings, $settings_code)
	{
		\COption::SetOptionString(self::getModuleId(), $settings_code, json_encode($arSettings));
	}

	/*
	 * TODO вычисление ID модуля регулярками
	 */
	public static function getModuleId()
	{
		//preg_match("modules/(.*)/lib", __DIR__, $arMatches);
		//$module_id = $arMatches[0];

		$module_id = "wahrheit.socnetsposting";
		return $module_id;
	}

	private static function getDefaultSettings($settings_code)
	{
		switch ($settings_code)
		{
			case "fb":
				return array(
					"user_id" => "",
					"page_id" => "",
					"app_id" => "",
					"app_secret" => "",
					"access_page_token" => ""
				);
				break;
			case "tw":
				return array(
					'consumer_key' => "",
					'consumer_secret' => "",
					'oauth_token' => "",
					'oauth_secret' => "",
				);
				break;
			case "iblock":
				return array();
				break;
			default:
				return array();
		}
	}
}