<?php
/*
  __  __           _         __        ___                     
 |  \/  |_   _ ___| |_ __ _ / _| __ _ / _ \ _______ __ _ _ __  
 | |\/| | | | / __| __/ _` | |_ / _` | | | |_  / __/ _` | '_ \ 
 | |  | | |_| \__ \ || (_| |  _| (_| | |_| |/ / (_| (_| | | | |
 |_|  |_|\__,_|___/\__\__,_|_|  \__,_|\___//___\___\__,_|_| |_|
                                                               
*/
namespace JetonMarket;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\{Command, CommandSender};
use pocketmine\Player;
use jojoe77777\FormAPI\CustomForm;
use onebone\economyapi\EconomyAPI;
class Base extends PluginBase implements Listener
{
	
	public function onEnable()
	{
                                                           
  @mkdir($this->getDataFolder());
  $this->cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);      
  $this->getServer()->getPluginManager()->registerEvents($this, $this);                                          
	}
	
	public function onJoin(PlayerJoinEvent $e)
	{
		$g = $e->getPlayer();
		if($this->cfg->get($g->getName()) == null){
			
			$this->cfg->set($g->getName(), 0);
			$this->cfg->save();
		}
	}
	
	public function onCommand(CommandSender $g, Command $kmt, string $lbl, array $args): bool
	{
		switch($kmt->getName()){
			case "jetonum":
			$g->sendMessage("§7» §eJetonum§7: §6".$this->cfg->get($g->getName()));
			break;
			case "jetonver":
			if($g->hasPermission("jeton.ver")){
				if(!(empty($args[0]) || empty($args[1]))){
					if(is_numeric($args[1])){
						$this->addJeton($this->getServer()->getPlayer($args[0]), (int)$args[1]);
						$g->sendMessage("§7» §aBaşarıyla §e".$args[1]." §ajeton verildi");
					}else{
						$g->sendMessage("§7» §cLütfen sayısal değerler giriniz");
					}
				}else{
					$g->sendMessage("§7» §eKullanım§7: §6/jetonver <oyuncu> <miktar>");
				}
			}else{
				$g->sendMessage("§cBu komutu kullanmak için yetkilendirilmedin!");
			}
			break;
			case "jetonkes":
			if($g->hasPermission("jeton.kes")){
				if(!(empty($args[0]) || empty($args[1]))){
					if(is_numeric($args[1])){
						if(!$this->cfg->get($args[0]) == null){
						if($this->cfg->get($args[0]) >= $args[1]){
						$this->removeJeton($this->getServer()->getPlayer($args[0]), (int)$args[1]);
						$g->sendMessage("§7» §aBaşarıyla §e".$args[1]." §ajeton¬ kesildi.");
						}else{
							$g->sendMessage("§7» §cBu oyuncunun girdiğiniz kadar jetonu yok");
						}
						}else{
							$g->sendMessage("§7» §cBu oyuncu veritabanında kayıtlı değil.");
						}
					}else{
						$g->sendMessage("§7» §cLütfen sayısal değerler giriniz");
					}
				}else{
					$g->sendMessage("§7» §eKullanım§7: §6/jetonkes <oyuncu> <miktar>");
				}
			}else{
				$g->sendMessage("§cBu komutu kullanmak için yetkilendirilmedin!");
			}
			break;
			case "jetonmarket":
			$form = new CustomForm(function(Player $g, $args){
				if($args[0] == 0){
					if($this->cfg->get($g->getName()) >= 2){
						$this->removeJeton($g, 2);
						EconomyAPI::getInstance()->addMoney($g, 100000);
						$g->sendMessage("§aBaşarıyla hesabınıza §e100000§fTL §aaktarıldı");
					}else{
					$g->sendMessage("§cYeteri kadar jetonunuz yok!");
					}
				}
				if($args[0] == 1){
					if($this->cfg->get($g->getName()) >= 5){
						$this->removeJeton($g, 5);
						EconomyAPI::getInstance()->addMoney($g, 250000);
						$g->sendMessage("§aBaşarıyla hesabınıza §e250000§fTL §aaktarıldı");
					}else{
					$g->sendMessage("§cYeteri kadar jetonunuz yok!");
					}
				}
				if($args[0] == 2){
					if($this->cfg->get($g->getName()) >= 10){
						$this->removeJeton($g, 10);
						EconomyAPI::getInstance()->addMoney($g, 500000);
						$g->sendMessage("§aBaşarıyla hesabınıza §e500000§fTL §aaktarıldı");
					}else{
					$g->sendMessage("§cYeteri kadar jetonunuz yok!");
					}
				}
			});
			$form->setTitle("§7» §cJeton Market");
			$form->addDropdown("§7» §aAlmak istediğiniz ürünü seçiniz", [
			"§e100.000§aTL §7» §e2 §aJeton", 
		 "§e250.000§aTL §7» §e5 §aJeton",
			"§e500.000§aTL §7» §e10 §aJeton",
			]);
			$form->sendToPlayer($g);
			break;
		}
		return true;
	}
	
	public function addJeton(Player $g, int $miktar){
		 $jetonu = $this->cfg->get($g->getName());
	  $this->cfg->set($g->getName(), $jetonu+$miktar);
	  $this->cfg->save();
	  $g->sendMessage("§7» §aHesabınıza §e".$miktar."§a jeton aktarıldı.");
	}
	
	public function removeJeton(Player $g, int $miktar){
		 $jetonu = $this->cfg->get($g->getName());
	  $this->cfg->set($g->getName(), $jetonu-$miktar);
	  $this->cfg->save();
	  $g->sendMessage("§7» §aHesabınızdan §e".$miktar."§a jeton kesildi.");
	}
}