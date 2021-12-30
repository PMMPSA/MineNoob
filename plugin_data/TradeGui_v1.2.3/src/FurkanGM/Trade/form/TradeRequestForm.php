<?php

namespace FurkanGM\Trade\form;

use FurkanGM\Trade\gui\TradeGui;
use FurkanGM\Trade\Main;
use FurkanGM\Trade\libs\dktapps\pmforms\ModalForm;
use pocketmine\Player;

class TradeRequestForm extends ModalForm
{

	/** @var \pocketmine\Player  */
	private $sender;

	/**
	 * TradeRequestForm constructor.
	 * @param Player $sender
	 */
	public function __construct(Player $sender)
	{
		$this->sender = $sender;
		parent::__construct(
			Main::getInstance()->translateText("form.title"),
			Main::getInstance()->translateText("form.text", [$sender->getName()]),
			function (Player $player,bool $choice): void {
				if ($choice)
					Main::getInstance()->getManager()->acceptTrade($player);
				else
					Main::getInstance()->getManager()->rejectTrade($player);
			},
			Main::getInstance()->translateText("form.button.accept"),
			Main::getInstance()->translateText("form.button.reject")
		);
	}

}
