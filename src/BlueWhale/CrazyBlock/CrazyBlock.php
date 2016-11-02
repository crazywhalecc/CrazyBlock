<?php

namespace BlueWhale\CrazyBlock;

//Command
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;

use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class CrazyBlock extends PluginBase implements Listener
{
	public function onEnable()
	{
		@mkdir($this->getDataFolder());//Making a folder
		$this->commandblock = new Config($this->getDataFolder()."CommandBlock.yml", Config::YAML, array(
		"CB" => array(),
		"CBT" => array()
		));
		$this->statusp=new Config($this->getDataFolder()."temp.wtp",Config::YAML,array(
			"pig" => "0"
		));
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onChat(PlayerChatEvent $eventc)
	{
		$msg=$eventc->getMessage();
		$t=$this->statusp->get("pig");
		$playerww=$eventc->getPlayer();
		
		$player=$playerww->getName();
		if($t == "1")
		{
			$lpt=$this->statusp->get("name");
			if($lpt!=$eventc->getPlayer()->getName())
			{
				return true;
			}
			else
			{
				$message = $eventc->getMessage();
				if($message == "*cancel")
				{
					$this->statusp->set("pig",0);
					$this->statusp->save();
					$playerww->sendMessage("§a成功取消操作！");
					$eventc->setCancelled(true);
					return true;
				}
				if($message == "")
				{
					$playerww->sendMessage("§c[WHelp] 请输入需要站在方块上运行的命令！");
					$eventc->setCancelled(true);
					return true;
				}
				$piu=$this->commandblock->get("CB");
				$this->statusp->set("name",$player);
				$this->statusp->save();
				$pick=$this->statusp->get("bname");
				$piu[$pick]["Command"]=$message;
				$tempcbl=1;
				$t=0;
				$this->statusp->set("pig",2);
				$this->statusp->save();
				$this->statusp->set("cmd",$message);
				$this->statusp->save();
				$playerww->sendMessage("§a[WHelp] 已成功保存指令，接下来请点击一个方块来生效！");
				$eventc->setCancelled(true);
				return true;
		    }
		}
		if($t == "4")
		{
			$lpt=$this->statusp->get("name");
			if($eventc->getPlayer()->getName()==$lpt)
			{
				$ltsp=$eventc->getPlayer();
				$message = $eventc->getMessage();
				if($message == "*cancel")
				{
					$this->statusp->set("pig",0);
					$this->statusp->save();
					$ltsp->sendMessage("§a成功取消操作！");
					$eventc->setCancelled(true);
					return true;
				}
				$piu=$this->commandblock->get("CBT");
				$pick=$this->statusp->get("bname");
				$piu[$pick]["Command"]=$message;
				$t=0;
				$this->statusp->set("pig",5);
				$this->statusp->save();
				$this->statusp->set("cmd",$message);
				$this->statusp->save();
				$ltsp->sendMessage("§a[WHelp] 已成功保存指令，接下来请点击一个方块来生效！");
				$eventc->setCancelled(true);
				return true;
			}
		}
	}
	public function playerBlockTouch(PlayerInteractEvent $pig)
	{
		$qw=$this->commandblock->get("CBT");
		$b=$pig->getBlock();
		$p=$pig->getPlayer();
		$pn=$p->getName();
		foreach($qw as $link)  
		{
			if($link["X"]==$b->getX() and $link["Y"]==$b->getY() and $link["Z"]==$b->getZ() and $link["level"]==$b->getLevel()->getFolderName())
			{
				$this->getServer()->dispatchCommand($p,str_replace("%p",$pn,$link["Command"]));
				$pig->setCancelled(true);
			}
		}
		$poi=$this->statusp->get("pig");
		if($poi == 2)
		{
			$manager=$pig->getPlayer();
			$man=$manager->getName();
			$poiu=$this->statusp->get("bname");
			$name=$this->statusp->get("name");
			$cmds=$this->statusp->get("cmd");
			$b=$pig->getBlock();
			if($name == $man)
			{
				$pu=$this->commandblock->get("CB");
				$pu[$poiu]["X"]=$b->x;
				$pu[$poiu]["Y"]=$b->y;
				$pu[$poiu]["Z"]=$b->z;
				$pu[$poiu]["Command"]=$cmds;
				$this->commandblock->set("CB",$pu);
				$this->commandblock->save();
				$manager->sendMessage("§a[WHelp] 已成功添加方块指令！");
				$this->statusp->set("pig","0");
				$this->statusp->save();
				return true;
			}
		}
		if($poi == 5)
		{
			$manager=$pig->getPlayer();
			$man=$manager->getName();
			$poiu=$this->statusp->get("bname");
			$name=$this->statusp->get("name");
			$cmds=$this->statusp->get("cmd");
			$b=$pig->getBlock();
			$levels=$b->getLevel()->getFolderName();
			if($name == $man)
			{
				$pu=$this->commandblock->get("CBT");
				$pu[$poiu]["X"]=$b->x;
				$pu[$poiu]["Y"]=$b->y;
				$pu[$poiu]["Z"]=$b->z;
				$pu[$poiu]["level"]=$levels;
				$pu[$poiu]["Command"]=$cmds;
				$this->commandblock->set("CBT",$pu);
				$this->commandblock->save();
				$manager->sendMessage("§a[WHelp] 已成功添加方块指令！");
				$this->statusp->set("pig","0");
				$this->statusp->save();
				return true;
			}
		}
	}
	public function pigll(PlayerMoveEvent $event)
	{
		$qw=$this->commandblock->get("CB");
		$b = $event->getPlayer()->getLevel()->getBlock($event->getPlayer()->floor()->subtract(0, 1));
		$p=$event->getPlayer();
		$pn=$p->getName();
		foreach($qw as $link)
		{
			if($link["X"]==$b->getX() and $link["Y"]==$b->getY() and $link["Z"]==$b->getZ())
			{
				$this->getServer()->dispatchCommand($p,str_replace("%p",$pn,$link["Command"]));
			}
		}
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args)
	{
		switch($command->getName())
		{
			case "cb":
				if (isset($args[0]))
				{
					switch($args[0])
					{
						case "step":
							if(isset($args[1]))
							{
								$blockname=$args[1];
								$lig=$sender->getName();
								$this->statusp->set("pig",1);
								$this->statusp->save();
								$this->statusp->set("name",$lig);
								$this->statusp->save();
								$this->statusp->set("bname",$args[1]);
								$this->statusp->save();
								$sender->sendMessage("§b[CrazyBlock] 已添加名字为 $blockname 的方块命令，接下来请在聊天框内直接输入想要添加的指令，无需/");
								$sender->sendMessage("§f[CrazyBlock] 提示：如想撤销这次操作请在聊天框内输入*cancel即可");
								return true;
							}
							else
							{
								$sender->sendMessage("§c[CrazyBlock] 用法： /cb step [name]");
								return true;
							}
						case "帮助":
							$sender->sendMessage("§e=====鲸鱼家里的疯狂的方块=====");
							$sender->sendMessage("§a1.站立类型: 先输入/cb step [名字]， 然后根据提示直接在聊天框内输入需要添加的命令即可， 最后点击方块即可完成设置~最后只需站立在方块上即可运行命令");
							$sender->sendMessage("§a2.点击类型: 先输入/cb touch [名字], 然后根据提示直接在聊天框内输入需要添加的命令即可， 最后点击方块即可完成设置~玩家点击方块即可执行命令！");
							$sender->sendMessage("§a3.删除数据: 输入/cb delete [touch/step] [名字]");
							$sender->sendMessage("§a4.查询名称: 输入/cb list, 来查看已设置的所有命令的方块的名称以便删除。 如果设置了很多的方块，建议从后台查询");
							return true;
						case "touch":
							if(isset($args[1]))
							{
								$lig=$sender->getName();
								$this->statusp->set("pig",4);
								$this->statusp->save();
								$this->statusp->set("name",$lig);
								$this->statusp->save();
								$this->statusp->set("bname",$args[1]);
								$this->statusp->save();
								$sender->sendMessage("§b[CrazyBlock] 已添加名字为 $args[1] 的方块命令，接下来请在聊天框内直接输入想要添加的指令，无需/");
								$sender->sendMessage("§f[CrazyBlock] 提示：如想撤销这次操作请在聊天框内输入*cancel即可");
								return true;
							}
							else
							{
								$sender->sendMessage("§b[CrazyBlock] 用法： /cb touch [方块名]");
								return true;
							}
						case "list":
							$listCB=$this->commandblock->get("CB");
							$listCBT=$this->commandblock->get("CBT");
							$sender->sendMessage("§e=疯狂的方块站在方块执行命令名字列表=");
							foreach($listCB as $a=>$b)
							{
								$bl=$b["Command"];
								$sender->sendMessage("§7名字: $a , 命令: $bl ");
							}
							$sender->sendMessage("§e=疯狂的方块点击方块执行命令名字列表=");
							foreach($listCBT as $a=>$b)
							{
								$bl=$b["Command"];
								$sender->sendMessage("§7名字: $a , 命令: $bl ");
							}
							return true;
						case "delete":
							if(isset($args[1]))
							{
								if($args[1]=="touch")
								{
									if(isset($args[2]))
									{
										$pocket=$this->commandblock->get("CBT");
										$po=$args[2];
										unset($pocket[$po]);
										$this->commandblock->set("CBT",$pocket);
										$this->commandblock->save();
										$sender->sendMessage("§a[CrazyBlock] 成功删除方块！");
										return true;
									}
									else
									{
										$sender->sendMessage("用法： /cb delete touch [name]");
										return true;
									}
								}
								if($args[1]=="step")
								{
									if(isset($args[2]))
									{
										$pocket=$this->commandblock->get("CB");
										$po=$args[2];
										unset($pocket[$po]);
										$this->commandblock->set("CB",$pocket);
										$this->commandblock->save();
										$sender->sendMessage("§a[CrazyBlock] 成功删除方块！");
										return true;
									}
									else
									{
										$sender->sendMessage("用法： /cb delete step [name]");
										return true;
									}
								}
								else
								{
									$sender->sendMessage("§c[CrazyBlock] 用法： /cb delete step [name]");
									return true;
								}
							}
							else
							{
								$sender->sendMessage("§c[CrazyBlock] 用法： /cb delete [touch/step] [name]");
								return true;
							}
						default:
							$sender->sendMessage("§c请输入/cb 帮助");
							return true;
					}
				}
				else
				{
					$sender->sendMessage("§c请输入/cb 帮助");
					return true;
				}
				return true;
		}
	}
}
