<?php

namespace FurkanGM\Trade\task;

use FurkanGM\Trade\gui\TradeGui;
use pocketmine\scheduler\Task;

/**
 * @property TradeGui trade
 */
class TradeTask extends Task {

	/**
	 * TradeTask constructor.
	 * @param TradeGui $trade
	 */
	public function __construct(TradeGui $trade)
	{
		$this->trade = $trade;
	}

	/**
	 * @param int $currentTick
	 */
	public function onRun(int $currentTick)
	{
		$this->trade->startTrade($this->getTaskId());
	}

}