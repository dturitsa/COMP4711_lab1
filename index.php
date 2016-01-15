<?php

$squares = $_GET['board'];
if (!isset($_GET['board'])){
    echo "board not set";
}


$game = new Game($squares);


if ($game->winner('o'))
    echo 'You win. Lucky guesses!';
elseif ($game->winner('x'))
    echo 'I already won';
else{
    $game->compMove();
    if ($game->winner('x'))
        echo 'I win. Muahahahaha';
}
$game->display();

echo  "<a href=\"?board=---------\">RESET</a>";

class Game {
    var $position;
    var $newposition;
    function __construct($squares) {
        $this->position = str_split($squares);
    }
/*
 * Checks if there is a line of 3 with the specified token;
 * Param: the token to check
 * return: true if completed line is found
 */
    function winner($token) {
        //check for horizontal winning lines
        for($row=0; $row<3; $row++) {
            if($this->position[3*$row+0] == $token &&
                $this->position[3*$row+1] == $token &&
                $this->position[3*$row+2] == $token){
                return true;
            }
        }
        //check for vertical winning lines
        for($col=0; $col<3; $col++) {
            if($this->position[$col] == $token &&
                $this->position[3 + $col] == $token &&
                $this->position[6 + $col] == $token){
                return true;
            }
        }
        //check for diagonal winning lines
        if($this->position[0] == $token &&
            $this->position[4] == $token &&
            $this->position[8] == $token){
            return true;
        } elseif($this->position[2] == $token &&
            $this->position[4] == $token &&
            $this->position[6] == $token){
            return true;
        }
        return false;
    }
/*
 * The AI for the game. Tries to make the following moves:
 *  1)find a line of 2 x's and complete it (win)
 *  2)find a line of 2 o's and block it (prevent player from winning)
 *  3)claim the central spot
 *  4)fill the first empty spot it finds
 */
    function compMove(){
        if($this->fillLine('x', 2)){
            return;
        } elseif($this->fillLine('o', 2)){
            return;
        } elseif($this->position[4] == '-' && implode($this->position) != '---------'){
            $this->position[4] = 'x';
            return;
        } else{
            $this->fillLine('o', 1);
        }

    }


/*
 * Fills an empty spot in a line that is partially filled by specified tokens
 *   params:
 *       $token: the tokens to search for
 *       $tolerance: the minimum number of tokens in the line
 *   return:
 *       whether a valid line was found and filled
 */
    function fillLine($token, $tolerance){
        //check for partially filled rows
        for($row=0; $row<3; $row++) {
            $oCount = 0;
            if($this->position[3*$row+0] == $token) $oCount++;
            if($this->position[3*$row+1] == $token) $oCount++;
            if($this->position[3*$row+2] == $token) $oCount++;
            if($oCount >= $tolerance) {
                if($this->putInSlot(3*$row+0, 3*$row+1, 3*$row+2))
                     return true;
            }
        }
        //check for partially filled columns
        for($col=0; $col<3; $col++) {
            $oCount = 0;
            if($this->position[$col] == $token) $oCount++;
            if($this->position[3 + $col] == $token) $oCount++;
            if($this->position[6 + $col] == $token) $oCount++;
            if($oCount >= $tolerance) {
                if($this->putInSlot($col, 3 + $col, 6 + $col))
                return true;
            }
        }
        //check for partially filled diagonal lines
        $oCount = 0;
        if($this->position[0] == $token) $oCount++;
        if($this->position[4] == $token) $oCount++;
        if($this->position[8] == $token) $oCount++;
        if($oCount >= $tolerance) {
            if($this->putInSlot(0, 4, 8))
                return true;
        }
        $oCount = 0;
        if($this->position[2] == $token) $oCount++;
        if($this->position[4] == $token) $oCount++;
        if($this->position[6] == $token) $oCount++;
        if($oCount >= $tolerance) {
            if($this->putInSlot(2, 4, 6))
                return true;
        }
        return false;
    }
/*
 * fills the first of the 3 spots that it finds
 * params: the positions to be filled
 * return: true if successful, false if none of the positions are empty
 */
    function putInSlot($a, $b, $c){
       if($this->position[$a] != 'o' && $this->position[$a] != 'x'){
           $this->position[$a] = 'x';
           return true;
       }

       elseif($this->position[$b] != 'o' && $this->position[$b] != 'x'){
           $this->position[$b] = 'x';
           return true;
       }

       elseif($this->position[$c] != 'o' && $this->position[$c] != 'x'){
           $this->position[$c] = 'x';
           return true;
       } else
           return false;

    }

/*
 * Displays the gameboard as a table
 */
    function display() {
        echo '<table cols="3"  width="100" style="font­size:large; font­weight:bold;">';
        echo '<tr>'; // open the first row
        for ($pos=0; $pos<9;$pos++) {
            echo $this->show_cell($pos);
            if ($pos %3 == 2) echo '</tr><tr>';
         }
        echo '</tr>'; // close the last row
        echo '</table>';
    }
/*
 * returns a cell of the gameboard table as a string.
 */
    function show_cell($which) {
        $token = $this->position[$which];
        if ($token != '-') return '<td> '.$token.' </td>';
        $this->newposition = $this->position;
        $this->newposition[$which] = 'o';
        $move = implode($this->newposition);
        $link = 'index.php?board='.$move;

        return '<td><a href="'.$link.'"> - </a></td>';
    }

}

?>