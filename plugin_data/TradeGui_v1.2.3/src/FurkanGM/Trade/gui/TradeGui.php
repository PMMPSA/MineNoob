<?php

namespace FurkanGM\Trade\gui;

use FurkanGM\Trade\Main;
use FurkanGM\Trade\task\TradeTask;
use FurkanGM\Trade\libs\muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;

class TradeGui
{

	/** @var InvMenu */
	public $menu;
	/** @var \FurkanGM\Trade\Main  */
	private $plugin;
	/** @var Player */
	private $player;
	/** @var Player */
	private $sender;
	/** @var int */
	private $starting = 0;
	/** @var int */
	private $taskId;

	/** @var array */
	private $border = [4, 13, 22, 31, 40, 49];
	/** @var array */
	private $player1area = [0, 1, 2, 3, 9, 10, 11, 12, 18, 19, 20, 21, 27, 28, 29, 30, 36, 37, 38, 39, 46, 47, 48];
	/** @var array */
	private $player2area = [5, 6, 7, 8, 14, 15, 16, 17, 23, 24, 25, 26, 32, 33, 34, 35, 41, 42, 43, 44, 50, 51, 52];


	/**
	 * TradeGui constructor.
	 * @param Player $sender
	 * @param Player $player
	 */
	public function __construct(Player $sender, Player $player)
	{
		$this->player = $player;
		$this->sender = $sender;
        $this->plugin = Main::getInstance();
        $sname = strlen($this->sender->getName()) > 8 ? substr($this->sender->getName(),0,8) . "..." : $this->sender->getName();
        $pname = strlen($this->player->getName()) > 8 ? substr($this->player->getName(),0,8) . "..." : $this->player->getName();
    	$this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)->setName($sname . str_repeat(" ",11) . $pname)->setListener([$this, "inventoryEvent"])->setInventoryCloseListener([$this,"inventoryCloseEvent"]);
    }


	public function openTrade(): void
	{
        $this->menu->send($this->sender);
        $this->menu->send($this->player);
        $this->menu->getInventory()->setItem(45,Item::get(236,14,1)->setCustomName($this->plugin->translateText("gui.button.accept")));
        $this->menu->getInventory()->setItem(53,Item::get(236,14,1)->setCustomName($this->plugin->translateText("gui.button.accept")));
        $this->menu->getInventory()->setItem(4,Item::get(20,0,1)->setCustomName(" "));
        $this->menu->getInventory()->setItem(13,Item::get(20,0,1)->setCustomName(" "));
        $this->menu->getInventory()->setItem(22,Item::get(20,0,1)->setCustomName(" "));
        $this->menu->getInventory()->setItem(31,Item::get(20,0,1)->setCustomName(" "));
        $this->menu->getInventory()->setItem(40,Item::get(20,0,1)->setCustomName(" "));
        $this->menu->getInventory()->setItem(49,Item::get(20,0,1)->setCustomName(" "));
	}

	/**
	 * @param Player $player
	 * @param Item $item
	 * @param Item $width
	 * @param SlotChangeAction $action
	 * @return bool
	 */
	public function inventoryEvent(Player $player, Item $item, Item $width, SlotChangeAction $action): bool
	{
		if ($action->getSlot() == 45 && $player->getName() == $this->sender->getName()) {
			$this->clickButton($action->getSlot(), $item);
			return false;
		} elseif ($action->getSlot() == 53 && $player->getName() == $this->player->getName()) {
			$this->clickButton($action->getSlot(), $item);
			return false;
		} elseif ($this->isBorder($action->getSlot())) {
			return false;
		} elseif ($this->checkArea($player, $action->getSlot())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param \pocketmine\Player $player
	 * @param $slotNumber
	 * @return bool
	 */
	public function checkArea(Player $player, $slotNumber): bool
	{
		if (($player->getName() == $this->sender->getName() && in_array($slotNumber, $this->player1area)) || ($player->getName() == $this->player->getName() && in_array($slotNumber, $this->player2area))) {
			if ($this->starting == 1)
				$this->cancelTrade();
			return true;
		}
		return false;
	}

	/**
	 * @param int $slotNumber
	 * @return bool
	 */
	public function isBorder(int $slotNumber): bool
	{
		return in_array($slotNumber, $this->border);
	}

	/**
	 * @param int $slotNumber
	 * @param \pocketmine\item\Item $item
	 */
	public function clickButton(int $slotNumber, Item $item): void
	{
		$slot = $slotNumber == 45 ? 53 : 45;
		if ($item->getDamage() == 14) {
			$this->menu->getInventory()->setItem($slotNumber, Item::get(236, 5, 1)->setCustomName($this->plugin->translateText("gui.button.cancel")));
			if ($this->menu->getInventory()->getContents()[$slot]->getDamage() == 5) {
				if ($this->starting == 0) {
					$this->plugin->getScheduler()->scheduleRepeatingTask(new TradeTask($this), 20);
				}
			}
		} else {
			$this->menu->getInventory()->setItem($slotNumber, Item::get(236, 14, 1)->setCustomName($this->plugin->translateText("gui.button.accept")));
			if ($this->starting == 1) {
				$this->cancelTrade();
			}
		}
	}

	/**
	 * @var int
	 */
	private $a = 5;

	/**
	 * @param int $taskId
	 */
	public function startTrade(int $taskId)
	{
		$this->taskId = $taskId;
		$this->starting = 1;
		$this->menu->getInventory()->setItem(4, Item::get(241, 5, $this->a)->setCustomName(" "));
		$this->menu->getInventory()->setItem(13, Item::get(241, 5, $this->a)->setCustomName(" "));
		$this->menu->getInventory()->setItem(22, Item::get(241, 5, $this->a)->setCustomName(" "));
		$this->menu->getInventory()->setItem(31, Item::get(241, 5, $this->a)->setCustomName(" "));
		$this->menu->getInventory()->setItem(40, Item::get(241, 5, $this->a)->setCustomName(" "));
		$this->menu->getInventory()->setItem(49, Item::get(241, 5, $this->a)->setCustomName(" "));
		$this->a--;
		if ($this->a < 0) {
			$this->successfulTrade();
		}
	}

	/**
	 * @var int
	 */
	public $finish = 1;

	/**
	 *
	 */
	public function successfulTrade()
	{
		$this->plugin->getScheduler()->cancelTask($this->taskId);
		$items = $this->menu->getInventory()->getContents();
		foreach ($items as $index => $item) {
			if (in_array($index, $this->player1area)) {
				$this->player->getInventory()->addItem($item);
			}
			if (in_array($index, $this->player2area)) {
				$this->sender->getInventory()->addItem($item);
			}
		}
		$this->sender->addTitle($this->plugin->translateText("trade.successful"));
		$this->player->addTitle($this->plugin->translateText("trade.successful"));
            $this->finish = 0;
            $this->sender->removeWindow($this->menu->getInventory());
            $this->player->removeWindow($this->menu->getInventory());
    }

	/**
	 *
	 */
	public function cancelTrade()
	{
		$this->plugin->getScheduler()->cancelTask($this->taskId);
		$this->menu->getInventory()->setItem(45, Item::get(236, 14, 1)->setCustomName($this->plugin->translateText("gui.button.accept")));
		$this->menu->getInventory()->setItem(53, Item::get(236, 14, 1)->setCustomName($this->plugin->translateText("gui.button.accept")));
		$this->menu->getInventory()->setItem(4, Item::get(20, 0, 1)->setCustomName(" "));
		$this->menu->getInventory()->setItem(13, Item::get(20, 0, 1)->setCustomName(" "));
		$this->menu->getInventory()->setItem(22, Item::get(20, 0, 1)->setCustomName(" "));
		$this->menu->getInventory()->setItem(31, Item::get(20, 0, 1)->setCustomName(" "));
		$this->menu->getInventory()->setItem(40, Item::get(20, 0, 1)->setCustomName(" "));
		$this->menu->getInventory()->setItem(49, Item::get(20, 0, 1)->setCustomName(" "));
		$this->starting = 0;
		$this->a = 5;
	}

	/**
	 * @param Player $player
	 * @param $env
	 * @return bool
	 */
	public function inventoryCloseEvent(Player $player, Inventory $env): bool
	{
		$items = $env->getContents();
		if (!is_null($this->taskId))
			$this->plugin->getScheduler()->cancelTask($this->taskId);
		if ($this->finish == 1) {
			foreach ($items as $index => $item) {
				if ($player->getName() == $this->sender->getName()) {
					if (in_array($index, $this->player1area)) {
						$player->getInventory()->addItem($item);
					}
				} elseif ($player->getName() == $this->player->getName()) {
					if (in_array($index, $this->player2area)) {
						$player->getInventory()->addItem($item);
					}
				}
			}
			if ($player->getName() == $this->sender->getName()) {
				$this->player->removeWindow($this->menu->getInventory());
				return true;
			} else {
				$this->sender->removeWindow($this->menu->getInventory());
				return true;
			}
		}
    return true;
    }

}