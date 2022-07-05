<?php

declare(strict_types=1);

namespace NhanAZ\AntiServerStop;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationFactory;
/* use pocketmine\permission\PermissionManager; */
/* use pocketmine\permission\DefaultPermissionNames; */


class Main extends PluginBase implements Listener {

	protected Config $config;

	private function generatorPassword(): string {
		$password = substr(base64_encode(random_bytes(20)), 3, 8);
		return $password;
	}

	protected function onLoad(): void {
		$commandMap = $this->getServer()->getCommandMap();
		$stopCommand = $commandMap->getCommand("stop");
		$commandMap->unregister($stopCommand);
	}

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, ["password" => $this->generatorPassword(), "wrongPassword" => "Wrong password!"]);
		$command = $this->getServer()->getCommandMap()->getCommand("stop");
		$command->setDescription(KnownTranslationFactory::pocketmine_command_stop_description());
		/**
		 * $permission = PermissionManager::getInstance()->getPermission($command->getPermission());
		 * $permission->setDescription(?);
		 * */
	}

	public function onCommand(CommandSender $sender, Command $command, string $commandLabel, array $args): bool {
		if ($command->getName() === "stop") {
			/** DefaultPermissionNames::COMMAND_STOP */
			if (!isset($args[0])) {
				return false;
			}
			if ($args[0] !== $this->config->get("password")) {
				$sender->sendMessage($this->config->get("wrongPassword", "Wrong password!"));
				return true;
			}
			Command::broadcastCommandMessage($sender, KnownTranslationFactory::commands_stop_start());
			$sender->getServer()->shutdown();
			return true;
		}
		return false;
	}
}
