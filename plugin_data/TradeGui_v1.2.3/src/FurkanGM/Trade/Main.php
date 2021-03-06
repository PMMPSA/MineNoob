<?php

namespace FurkanGM\Trade;

use FurkanGM\Trade\command\Trade;
use FurkanGM\Trade\manager\TradeManager;
use FurkanGM\Trade\libs\muqsit\invmenu\InvMenu;
use FurkanGM\Trade\libs\muqsit\invmenu\InvMenuHandler;
use FurkanGM\Trade\libs\dktapps\pmforms\BaseForm;
use pocketmine\lang\BaseLang;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{

	/** @var array  */
	public $tradeRequests = [];
	/** @var Main */
	public static $instance;
	/** @var BaseLang */
	public $baseLang;


	/**
	 *
	 */
	public function onLoad()
	{
		self::$instance = $this;
	}

	/**
	 *
	 */
	public function onEnable()
	{
		$lang = $this->getConfig()->get("language", "tur");
		$baseFolder = $this->getFile() . "/resources";
		if ($this->getConfig()->get("type") == "ui")
		{
			if (!class_exists(BaseForm::class))
			{
				$this->getLogger()->critical("Please install pmforms virion library");
				$this->setEnabled(false);
				return;
			}
		}
		if (!class_exists(InvMenu::class))
		{
			$this->getLogger()->critical("Please install InvMenu virion library");
			$this->setEnabled(false);
			return;
		}
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		if (file_exists($baseFolder . "/lang/" . $lang . ".ini"))
		{
			$this->baseLang = new BaseLang($lang, $baseFolder . "/lang/");
		}else{
			$this->baseLang = new BaseLang($lang, $baseFolder . "/lang/");
		}
		$this->getServer()->getCommandMap()->register("tradegui",new Trade());
	}

	/**
	 * @return Main
	 */
	public static function getInstance(): Main{
		return self::$instance;
	}

	/**
	 * @return \pocketmine\lang\BaseLang
	 */
	public function getLanguage(): BaseLang
	{
		return $this->baseLang;
	}

	/**
	 * @param string $text
	 * @param array $params
	 * @return string
	 */
	public function translateText(string $text, $params = []): string
	{
		return $this->getLanguage()->translateString($text, $params);
	}

	/**
	 * @return \FurkanGM\Trade\manager\TradeManager
	 */
	public function getManager(): TradeManager{
		return new TradeManager($this);
	}

}
