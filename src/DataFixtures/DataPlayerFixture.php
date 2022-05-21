<?php
namespace App\DataFixtures;

use App\Entity\Challenge\Team;
use App\Entity\Challenge\Player;
use Doctrine\Persistence\ObjectManager;
use App\Helpers\MySerializer;
use App\Repository\Challenge\TeamRepository;
use Symfony\Component\Console\Output\ConsoleOutput;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class DataPlayerFixture extends BaseFixture implements FixtureGroupInterface{

    public function loadData(ObjectManager $manager){
       /** @var TeamRepository $teamRepository */
       $teamRepository=$manager->getRepository(Team::class);

        $serializer=(new MySerializer())->getSerializer($manager,$this->translator,$this->router,$this->params,"Y-m-d H:i:s");
        $players=json_decode(file_get_contents("https://raw.githubusercontent.com/RealFevr/challenge/master/data/players.json"),true);

        $teams=(array)@$players["data"]["teams"];

        foreach($teams as $team){
            
            /** @var Team|null $teamEntity */
            $teamEntity=$teamRepository->getTeamByAcronym($team["acronym"]);

            if(!$teamEntity)
                $teamEntity= new Team($team["name"],$team["acronym"]);
                
           
            foreach((array)@$team["players"] as $player){
                if(!is_integer($player["number"]))
                    unset($player["number"]);

                
                $newPlayer=$serializer->deserialize(json_encode($player),Player::class,'json',[]);
                
                // $newPlayer=new Player();
                // $newPlayer->setFromArray($player);
                $teamEntity->addPlayer($newPlayer);
            }

            $manager->persist($teamEntity);
            $manager->flush();
        }


      
    }

    public static function getGroups(): array{
        return ['importplayers'];
    }


    
    
}
