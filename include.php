<?
CModule::AddAutoloadClasses('wahrheit.socnetsposting',array(
		'Facebook'=>'lib/facebook.php',
		'Twitter'=>'lib/twitter.php',
		'ModuleSettings'=>'lib/modulesettings.php',
	)
);

/*
 * TODO 1) Вынести этот класс в отдельный файл
 */
class Post
{
	public function post($arFields)
	{
		$arIblockParams = ModuleSettings::read('iblock');
		$arIblockIds = $arIblockParams['iblock_ids'];

		if (in_array($arFields['IBLOCK_ID'], $arIblockIds))
		{
			$host = $_SERVER['HTTP_HOST'];

			$arFilter = array("ID" => $arFields['IBLOCK_ID']);
			$dbIblock = CIBlock::GetList(array(), $arFilter);
			$arIblock = $dbIblock->Fetch();

			$url = str_replace("#SITE_DIR#", "http://".$host, $arIblock['DETAIL_PAGE_URL']);
			$url = str_replace("#ID#", $arFields['ID'], $url);
			$url = str_replace("#CODE#", $arFields['CODE'], $url);

			$pic = "http://".$host.CFile::GetPath($arFields['PREVIEW_PICTURE_ID']);
			$pic = str_replace(" ", "%20", $pic);

			$twitter = new Twitter();
			$mess = $arFields['NAME'].". Подробнее: ".$url;
			$twitter->post($mess);

			$fb = new Facebook();
			$arPostFields = array(
				"message" => strip_tags($arFields['PREVIEW_TEXT']),
				'picture' => $pic,
				"name" => $arFields['NAME'],
				"description" => strip_tags($arFields['PREVIEW_TEXT']),
				"caption" => $host,
				"link" => $url,
			);
			$fb->post($arPostFields);
		}
	}
}