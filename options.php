<?
global $MESS;
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("wahrheit.socnetsposting");
//COption::RemoveOption("wahrheit.socnetsposting", "iblock");

if ($REQUEST_METHOD == "POST" && strlen($save) > 0 && check_bitrix_sessid())
{
	ModuleSettings::write($_POST['fb'], 'fb');
	ModuleSettings::write($_POST['tw'], 'tw');
	ModuleSettings::write(array('iblock_ids'=>$_POST['iblock']), 'iblock');

	LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG."&mid=".urlencode($mid)."&".bitrix_sessid_get()."&mid_menu=1&tabControl_active_tab=".$_REQUEST['tabControl_active_tab']);
}


$arFB = ModuleSettings::read('fb');
$arTw = ModuleSettings::read('tw');
$arIblockParams = ModuleSettings::read('iblock');


CModule::IncludeModule("iblock");
$dbIblocks = CIBlock::GetList();
while($arIblock = $dbIblocks->Fetch())
	$arIblocks[] = $arIblock;


$aTabs = array(
	array("DIV" => "settings_iblock", "TAB" => GetMessage("MAIN_TAB_IBLOCK"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_IBLOCK")),
	array("DIV" => "settings_fb", "TAB" => GetMessage("MAIN_TAB_FB"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_FB")),
	array("DIV" => "settings_tw", "TAB" => GetMessage("MAIN_TAB_TW"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_TW")),
);
$tabControl = new CAdmintabControl("tabControl", $aTabs);


$tabControl->Begin();
?>

<style>
	.adm-input {width: 300px}
</style>

<form method="POST" enctype="multipart/form-data" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>&mid_menu=1">
	<?=bitrix_sessid_post()?>
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<?$tabControl->BeginNextTab();?>
	<tr>
		<td align="center">

			<?=GetMessage('iblock_ids')?><br>
			<?=GetMessage('iblock_mess')?>
			<br>
			<br>
			<select size="10" name="iblock[]" multiple="multiple">
				<?foreach ($arIblocks as $arIblock):?>
					<option value="<?=$arIblock['ID']?>" <?if(in_array($arIblock['ID'], $arIblockParams['iblock_ids'])):?>selected="selected"<?endif;?>>[<?=$arIblock['ID']?>] <?=$arIblock['NAME']?></option>
				<?endforeach;?>
			</select>
		</td>
	</tr>
	<?$tabControl->BeginNextTab();?>
	<tr>
		<td align="center">
			<table>
				<?foreach($arFB as $code => $val):?>
					<tr>
						<td><?=GetMessage($code)?></td>
						<td><input type="text" class="adm-input" name="fb[<?=$code?>]" value="<?=$val?>" <?/*if($code=="access_page_token"):?>disabled="disabled"<?endif;*/?>></td>
					</tr>
				<?endforeach;?>
			</table>
		</td>
	</tr>
	<?$tabControl->BeginNextTab();?>
	<tr>
		<td align="center">
			<table>
				<?foreach($arTw as $code => $val):?>
					<tr>
						<td><?=GetMessage($code)?></td>
						<td><input type="text" class="adm-input" name="tw[<?=$code?>]" value="<?=$val?>"></td>
					</tr>
				<?endforeach;?>
			</table>
		</td>
	</tr>


	<?$tabControl->Buttons();?>
	<input type="submit" name="save" value="<?=GetMessage('BUTTON_SAVE')?>" class="adm-btn-save">
	<input type="hidden" name="save" value="Y">

	<?$tabControl->End();?>
</form>