<?php
/**
 * Created by PhpStorm.
 * Developer: Shagufta Ambreen
 * Email: missshagufta000@gmail.com
 * Date:0/9/2022
 * Time: 2:40 PM
 */

class Dice {

    /** @var int $diceInCup */
    private $topSideVal;

    /**
     * @return int
     */
    public function getTopSideVal()
    {
        return $this->topSideVal;
    }

    /**
     * @return int
     */
    public function roll()
    {
        $this->topSideVal =  rand(1,6);
        return $this;
    }

    /**
     * @param int $topSideVal
     * @return Dice
     */
    public function setTopSideVal($topSideVal)
    {
        $this->topSideVal = $topSideVal;
        return $this;
    }
}

class Player {

    /** @var array $diceInCup */
    private $diceInCup = array();
    /** @var string $name */
    private $name;
    /** @var int $position */
    private $position;

    /**
     * @return array
     */
    public function getDiceInCup()
    {
        return $this->diceInCup;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Player constructor.
     * @param int $numberOfDice
     */
    public function __construct($numberOfDice,$position,$name='')
    {
        //position 0 is the left most
        $this->position = $position;
        //optional name, example Player A
        $this->name = $name;
        //init array of dice
        for($i=0;$i<$numberOfDice;$i++){
            array_push($this->diceInCup,new Dice());
        }
    }

    public function play()
    {
        foreach($this->diceInCup as $dice){
            /** @var Dice $dice */
            $dice->roll();
        }
    }

    /**
     * @param int $key
     */
    public function removeDice($key)
    {
        //echo "<pre>Remove index $key</pre><br>";
        unset($this->diceInCup[$key]);
    }

    /**
     * @param Dice $dice
     */
    public function insertDice($dice)
    {
        array_push($this->diceInCup,$dice);
    }
}

/**
 * The game is where the rules are applied
 * Class Game
 */
class Game{
    /** @var Player[] $players */
    private $players = array();
    private $round;
    const NUMBER_OF_DICE_PER_PLAYER = 6;
    const NUMBER_OF_PLAYER = 4;
    const RULE_REMOVED_WHEN_DICE_TOP = 6;
    const RULE_MOVE_WHEN_DICE_TOP = 1;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        //init round to 0
        $this->round = 0;
        //the game contains players and each
        //player have dices
        for($i=0;$i<self::NUMBER_OF_PLAYER;$i++){
            $this->players[$i] = new Player(self::NUMBER_OF_DICE_PER_PLAYER,$i,chr(65+$i));
        }
    }

    /**
     * @return $this
     */
    public function displayRound()
    {
        echo "<strong>Round ".$this->round."</strong><br><br>\r\n";
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function displayTopSideDice($title='After dice rolled')
    {
        echo '<span style="text-decoration: underline;">'.$title.'</span><br>';
        foreach($this->players as $player){
            /** @var Player $player */
            echo "Player ".$player->getName().": ";
            $diceTopSide = '';
            foreach($player->getDiceInCup() as $dice){
                /** @var Dice $dice */
                $diceTopSide .= $dice->getTopSideVal().", ";
            }
            //remove last comma and echo
            echo rtrim($diceTopSide,",")."<br>\r\n";

        }
        echo "<br><br>\r\n";
        return $this;
    }

    /**
     * @param Player $player
     * @return $this
     */
    public function displayWinner($player)
    {
        echo "<h1>Found Winner</h1>\r\n";
        echo "Player ".$player->getName()."<br>\r\n";
        return $this;
    }

    /**
     * Start the game
     */
    public function start()
    {
        //loop until found the winner(s)
        while(true){
            $this->round++;
            $diceCarryForward = array();
            //simulate the simultaneous player roll the dice
            foreach($this->players as $player){
                /** @var Player $player */
                $player->play();
            }
            //display before moved/removed
            $this->displayRound()->displayTopSideDice();
            //check foreach player the top side
            foreach($this->players as $index=>$player){
                /** @var Player $player */
                $tempDiceArray = array();
                foreach($player->getDiceInCup() as $diceIndex=>$dice){
                    /** @var Dice $dice */
                    //check for any occurrence of 6
                    if($dice->getTopSideVal() == self::RULE_REMOVED_WHEN_DICE_TOP){
                        $player->removeDice($diceIndex);
                    }
                    //check for occurrence of 1
                    if($dice->getTopSideVal() == self::RULE_MOVE_WHEN_DICE_TOP){
                        //determine player position
                        //MAX player is right most side,
                        //so move the dice to left most side
                        if($player->getPosition()==(self::NUMBER_OF_PLAYER-1)){
                            $this->players[0]->insertDice($dice);
                            $player->removeDice($diceIndex);
                        }
                        else{
                            array_push($tempDiceArray,$dice);
                            $player->removeDice($diceIndex);
                        }

                    }
                }
                $diceCarryForward[$index+1] =$tempDiceArray;

                if(array_key_exists($index,$diceCarryForward) && count($diceCarryForward[$index])>0){
                    //insert the dice
                    foreach ($diceCarryForward[$index] as $dice) {
                        $player->insertDice($dice);
                    }
                    //reset
                    $diceCarryForward = array();

                }

            }
            //display after moved/removed
            $this->displayTopSideDice("After Dice moved/Removed");
            //check if any winners?
            $winnerCount=0;
            foreach($this->players as $player){
                /** @var Player $player */
                if(count($player->getDiceInCup())<=0){
                    $this->displayWinner($player);
                    $winnerCount++;
                }
            }
            if($winnerCount>0){
                //exit the loop
                break;
            }


        }
    }
}

$game = new Game();
$game->start();
