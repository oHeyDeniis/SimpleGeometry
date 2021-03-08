<?php

namespace geometry;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public $savedClick = [];

    public function onEnable()
    {
        $this->saveResource("Como_usar.txt");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onDamage(EntityDamageEvent $e){
        if($e instanceof EntityDamageByEntityEvent){
            $en = $e->getEntity();
            $p = $e->getDamager();
            if($p instanceof Player and isset($this->savedClick[$p->getName()])){
                if($en instanceof Human){
                    $s = $this->savedClick[$p->getName()];
                    if($this->applyGeometry($en, $s[0], $s[1], $s[2])) {
                        $p->sendMessage("Geometria alterada");
                    }else $p->sendMessage("Erro ao aplicar");
                }else $p->sendMessage("Nao e possivel alterar geometria dessa entidade");
            }
        }
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if(in_array($command->getName(), ["ag", "addgeometry"])){
                $folder = $this->getDataFolder();
                if(isset($args[0]) and $args[0] == "cancel") {
                    if (isset($this->savedClick[$sender->getName()])) {
                        unset($this->savedClick[$sender->getName()]);
                        $sender->sendMessage("Geometria cancelada");
                    }
                }
                if(!isset($args[2])){
                    $sender->sendMessage("use /ag [eu|entity] [geometryId] [geometryName] ou /ag ids");
                    return true;
                }
                if($args[0] == "entity"){
                    $this->savedClick[$sender->getName()] = [$args[1], $folder.$args[2].".json", $folder.$args[2].".png"];
                    $sender->sendMessage("Clique em um player ou estatua de player para alterar");
                }
                if($args[0] == "eu"){
                    /**
                     * @var $sender Player
                     */
                    if($this->applyGeometry($sender, $args[1], $folder.$args[2].".json", $folder.$args[2].".png")){
                        $sender->sendMessage("Geometria aplicada");
                    }else $sender->sendMessage("Erro ao aplicar");
                }
                if($args[0] == "ids"){
                    $folder = $this->getDataFolder();
                    $sender->sendMessage("Exibindo ids");
                    foreach (scandir($folder) as $item){
                        if(in_array($item, [".", ".."])) continue;
                        if(is_file($folder.$item)){
                           $ex = explode(".", $item);
                           $extension = $ex[1];
                           if($extension == "json"){
                               if(is_file($folder.$ex[0].".png")){
                                   $sender->sendMessage("GeomtryName: {$ex[0]}");
                               }
                           }
                        }
                    }
                }
        }
        return parent::onCommand($sender, $command, $label, $args); // TODO: Change the autogenerated stub
    }
    public function getImageBytes(string $imgPath) : string{
        $skinBytes = "";
        $img = @imagecreatefrompng($imgPath);
        $s = (int) getimagesize($imgPath)[1];
        for($y = 0; $y < $s; $y++){
            for($x = 0; $x < 64; $x++){
                $cat = @imagecolorat($img, $x, $y);
                $a = ((~((int) ($cat >> 24))) << 1) & 0xff;
                $r = ($cat >> 16) & 0xff;
                $g = ($cat >> 8) & 0xff;
                $b = $cat & 0xff;
                $skinBytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $skinBytes;
    }
    public function applyGeometry(Human $entity, $geometryName, $geometryFile, $geometrySkin) : bool{
        try {
        $lastSkin = $entity->getSkin();
        $sk = $this->getImageBytes($geometrySkin);
        $geometryData = file_get_contents($geometryFile);
        $newSkin = new Skin($lastSkin->getSkinId(), $sk, "", $geometryName, $geometryData);
        $entity->setSkin($newSkin);
        $entity->sendSkin();
        return true;
    }catch (\Exception $e){
            echo $e->getMessage().PHP_EOL;
            return false;
        }
    }
    public function isValid(string $geomtryName){
        $folder = $this->getDataFolder();
        return is_file($folder.$geomtryName.".json") and is_file($folder.$geomtryName.".png");
    }
}