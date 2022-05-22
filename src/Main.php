<?php

declare(strict_types=1);

namespace NhanAZ\AntiServerStop;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\server\CommandEvent;

class Main extends PluginBase implements Listener {

	protected Config $config;

	private function generatorPassword(): string {
		$password = substr(base64_encode(random_bytes(20)), 3, 8);
		return $password;
	}

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
			"password" => $this->generatorPassword(),
			"usage" => "Usage: /stop <password>",
			"wrongPassword" => "Wrong password!"
		]);
	}

	public function onCommandEvent(CommandEvent $event) {
		$args = explode(" ", $event->getCommand());
		$sender = $event->getSender();
		$stopCmd = "stop";
		if (in_array($args[0], ["{$stopCmd}", "/{$stopCmd}", "./{$stopCmd}", "./pocketmine:{$stopCmd}"])) {
			if (!isset($args[1])) {
				$event->cancel();
				$sender->sendMessage($this->config->get("usage", "Usage: /stop <password>"));
				return;
			}
			if ($args[1] !== $this->config->get("password")) {
				$event->cancel();
				$sender->sendMessage($this->config->get("wrongPassword", "Wrong password!"));
				return;
			}
		}
	}
}
