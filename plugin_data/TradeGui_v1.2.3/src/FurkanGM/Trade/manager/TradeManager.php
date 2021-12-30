<?php

namespace FurkanGM\Trade\manager;

use FurkanGM\Trade\form\TradeRequestForm;
use FurkanGM\Trade\gui\TradeGui;
use FurkanGM\Trade\Main;
use pocketmine\Player;

class TradeManager
{

	/** @var \FurkanGM\Trade\Main  */
	private $main;

	public function __construct(Main $main)
	{
		$this->main = $main;
	}

	public function sendTrade(Player $sender, Player $player)
	{
		$cfg = $this->main->getConfig()->get("type");
		$this->main->tradeRequests[$player->getName()] = $sender->getName();
		$sender->sendMessage($this->main->translateText("command.requestsend", [$player->getName()]));
		if ($cfg == "ui")
			$player->sendForm(new TradeRequestForm($sender));
		else {
			$player->sendMessage($this->main->translateText("form.title") . "\n");
			$player->sendMessage($this->main->translateText("command.use"));
		}
	}

	public function acceptTrade(Player $player)
	{
		if ($this->checkTrade($player))
		{
			$sender = Main::getInstance()->tradeRequests[$player->getName()];
			$sender = $player->getServer()->getPlayer($sender);
			unset(Main::getInstance()->tradeRequests[$player->getName()]);
			if ($sender){
				$gui = new TradeGui($sender,$player);
				$gui->openTrade();
			}
			return;
		}
		else{
			$player->sendMessage(Main::getInstance()->translateText("form.no_offer"));
		}
	}

	public function rejectTrade(Player $player)
	{
		if ($this->checkTrade($player))
		{
			$sender = Main::getInstance()->tradeRequests[$player->getName()];
			$sender = $player->getServer()->getPlayer($sender);
			unset(Main::getInstance()->tradeRequests[$player->getName()]);
			if ($sender){
				$sender->sendMessage(Main::getInstance()->translateText("form.refused", [$player->getName()]));
				$player->sendMessage(Main::getInstance()->translateText("command.reject"));
			}
		}
		else{
			$player->sendMessage(Main::getInstance()->translateText("form.no_offer"));
		}
		return;
	}

	public function checkTrade(Player $player)
	{
		return isset(Main::getInstance()->tradeRequests[$player->getName()]);
	}

}