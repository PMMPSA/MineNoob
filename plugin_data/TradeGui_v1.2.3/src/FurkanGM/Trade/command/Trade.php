<?php

namespace FurkanGM\Trade\command;

use FurkanGM\Trade\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class Trade extends Command implements PluginIdentifiableCommand
{

    /**
     * @var Main
     */
    private $plugin;

    /**
     * Trade constructor.
     */
    public function __construct()
    {
		$this->plugin = Main::getInstance();
		parent::__construct($this->plugin->translateText("command"), $this->plugin->translateText("command.description"), $this->plugin->translateText("command.usage"));
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
			$cfg = Main::getInstance()->getConfig()->get("type");
			if (isset($args[0])){
				if ($cfg == "command")
				{
					if ($args[0] == "accept" || $args[0] == "kabul")
					{
						$this->getPlugin()->getManager()->acceptTrade($sender);
						return false;
					}
					elseif ($args[0] == "reject" || $args[0] == "reddet"){
						$this->getPlugin()->getManager()->rejectTrade($sender);
						return false;
					}
				}
                if (($player = $this->plugin->getServer()->getPlayer($args[0])) instanceof Player){
                    if ($player->getName() == $sender->getName()){
                    	$sender->sendMessage($this->plugin->translateText("command.yourself"));
                    	return false;
                    }else {
                    	$this->getPlugin()->getManager()->sendTrade($sender, $player);
					}
                }else{
                    $sender->sendMessage($this->plugin->translateText("command.playernotfound"));
                }
            }else{
				$sender->sendMessage($this->plugin->translateText("command.usage"));
            }
        }
        return true;
    }

	/**
	 * @return \pocketmine\plugin\Plugin
	 */
	public function getPlugin(): Plugin
	{
		return $this->plugin;
	}

}