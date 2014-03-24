<?php

namespace Poker\PokerTableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Poker\PokerTableBundle\Entity\PokerUser;

class DefaultController extends Controller {

    public function indexAction($id_table) {
        $securityContext = $this->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $table = $this->get('poker_table_repository')->getElements(array('by_id' => $id_table, 'action' => 'one'));
            $pokerUser = $this->get('poker_user_repository')->getElements(array('by_table_id' => $id_table, 'by_user_id' => $user->getId(), 'action' => 'one'));
            /* exemple process for developpement */
            return $this->render('PokerPokerTableBundle:Default:index.html.twig', array('table' => $table, 'player' => $pokerUser));
        } else {
            return $this->redirect($this->generateUrl('fo_login'));
        }
    }

    public function createPlayerAction() {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.context')->getToken()->getUser();
        if (!$request->get('place') || !$request->get('table_id') || !$user) {
            exit;
        }

        $pokerTable = $this->get('poker_table_repository')->getElements(array('by_id' => $request->get('table_id'), 'action' => 'one'));

        $cards = array();
        $player = new PokerUser();
        $player->setPokerTable($pokerTable);
        $player->setMoney(10);
        $player->setPlace($request->get('place'));
        $player->setUser($user);

        for ($i = 0; $i < 2; $i++) {
            $cards[] = $this->setCards($pokerTable);
        }
        $player->setCards($cards);
        $em->persist($player);
        $em->flush();

        echo 'ok';
        exit;
    }

    public function timeOutAction() {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.context')->getToken()->getUser();
        if (!$request->get('player_place') || !$request->get('table_id') || !$user) {
            exit;
        }
        $pokerTable = $this->get('poker_table_repository')->getElements(array('by_id' => $request->get('table_id'), 'action' => 'one'));
        $pokerUser = $this->get('poker_user_repository')->getElements(array('by_table_id' => $request->get('table_id'), 'by_place' => $request->get('player_place'), 'action' => 'one'));
        $mise = 0;
        if ($pokerTable->getRound() == 1) {
            if ($pokerUser->getSmallBlind()) {
                //mise
                $mise = $pokerTable->getSmallBlind();
                $pokerUser->setMoney($pokerUser->getMoney() - $mise);
                $pokerUser->setMoneyUsed($pokerUser->getMoneyUsed() + $mise);
            } elseif ($pokerUser->getBigBlind()) {
                //mise
                $mise = $pokerTable->getBigBlind();
                $pokerUser->setMoney($pokerUser->getMoney() - $mise);
                $pokerUser->setMoneyUsed($pokerUser->getMoneyUsed() + $mise);
            } else {
                //couché / absent / expulser
                $pokerUser->setStatus($pokerUser->getStatus() + 1);
            }
        } else {
            //pas de mise check
            //sinon se couche
            $pokerUser->setStatus($pokerUser->getStatus() + 1);
        }

        $em->persist($pokerUser);
        $em->flush();
        $returnArray = array('status' => $pokerUser->getStatus(),
            'money' => $pokerUser->getMoney(),
            'mise' => $mise);
        echo json_encode($returnArray);
        exit;
    }

    public function misePlayerAction() {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->get('security.context')->getToken()->getUser();
        if (!$request->get('player_mise') || !$request->get('table_id') || !$user) {
            exit;
        }

        $pokerTable = $this->get('poker_table_repository')->getElements(array('by_id' => $request->get('table_id'), 'action' => 'one'));
        $pokerUser = $this->get('poker_user_repository')->getElements(array('by_table_id' => $request->get('table_id'), 'by_user_id' => $user->getId(), 'action' => 'one'));

        $pokerUser->setMoney($pokerUser->getMoney() - $request->get('player_mise'));
        $pokerUser->setMoneyUsed($pokerUser->getMoneyUsed() + $request->get('player_mise'));

        $em->persist($pokerUser);
        $em->flush();

        echo $pokerUser->getMoney();
        exit;
    }

    private function setCards($pokerTable, $t = null) {
        $em = $this->getDoctrine()->getEntityManager();
        $cardsUsed = $pokerTable->getCardsUsed();
        $cards = $pokerTable->getCards();
        $rand = rand(0, 12) . '-' . rand(0, 3);
        if (is_array($cardsUsed)) {
            if (!in_array($rand, $cardsUsed)) {
                array_push($cardsUsed, $rand);
                if($t == 1){
                    array_push($cards, $rand);
                    $pokerTable->setCards($cards);
                }    
                $pokerTable->setCardsUsed($cardsUsed);
                $em->persist($pokerTable);
                $em->flush(); 
            } else {
                $this->setCards($pokerTable);
            }
        }  
    }

    public function setCardsTableAction() {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $cards = array();
        $ucards = array();
        $pokerTable = $this->get('poker_table_repository')->getElements(array('by_id' => $request->get('table_id'), 'action' => 'one'));
        $pokerUser = $this->get('poker_user_repository')->getElements(array('by_table_id' => $request->get('table_id'), 'by_user_id' => $user->getId(), 'action' => 'one'));
        $round = $pokerTable->getRound();
        if ($round >= 3) {
            $round = 0;
        }
        if ($round === 0) {
            foreach($pokerTable->getPokerUser() as $player){
                foreach($player->getCards() as $ca):
                    $ucards[] = $ca;
                endforeach;
            };
            $pokerTable->setCardsUsed($ucards);
            $pokerTable->setCards(array());
            $em->persist($pokerTable);
            $em->flush();
            for($i = 0; $i < 3; $i++){
                $this->setCards($pokerTable, 1);
            }
        } else {
            $this->setCards($pokerTable, 1);
        }
        $round++;
        $pokerTable->setRound($round);
        $em->persist($pokerTable);
        $em->flush();

        $combinedArray = array();
        $combinedArray['comb'] = $this->verifCombinaison($pokerTable, $pokerUser);
        $combinedArray['table'] = $pokerTable->getCards();
        echo json_encode($combinedArray);
        exit;
    }

    private function verifCombinaison($pokerTable, $player) {
        /*combinaisons
         * 
         * Paire
         * Double paire
         * Brelan
         * Quinte
         * couleur
         * Full
         * Carré
         * Quinte flush
         * Quinte flush ryal
        */
        $arrayCards = array();
        $tableCards = $pokerTable->getCards();
        $playerCards = $player->getCards();

        //GETTING CARDS//
        $playerCard1 = $playerCards[0];
        $playerCard2 = $playerCards[1];

        $playerCardExploded1 = explode("-", $playerCard1);
        $playerCardExploded2 = explode("-", $playerCard2);

        $combin = array();
        //PAIRES//
        $paires = array();
        $brelan = array();

        $nbPlayerCard1 = $playerCardExploded1[0];
        $nbPlayerCard2 = $playerCardExploded2[0];
        $combin[] = $playerCard1;
        $combin[] = $playerCard2;
        if ($nbPlayerCard1 === $nbPlayerCard2) {
            $paires[] = $playerCard1;
            $paires[] = $playerCard2;
        }
        $nbArray = array();
        $c = array();
        foreach ($tableCards as $tableCard) {
            $tableCardExploded = explode("-", $tableCard);
            $nbTableCard = $tableCardExploded[0];
            $combin[] = $tableCard;
            
            //regroupe carte identique
            if (array_key_exists($nbTableCard, $nbArray)) {
                //check carte identite sur table
                $paires[] = $tableCard;
                $paires[] = $nbArray[$nbTableCard];
            } elseif ($nbTableCard == $nbPlayerCard1) {
                $paires[] = $playerCard1;
                $paires[] = $tableCard;
            } elseif ($nbTableCard == $nbPlayerCard2) {
                $paires[] = $playerCard2;
                $paires[] = $tableCard;
            }
            $paires = $this->reOrder($paires);
            $nbArray[$nbTableCard] = $tableCard;
            
        }
        $combName = 'Carte la pus forte';
        $combin = $this->reOrder($combin);
        $quinte = false;
        $couleur = false;
        //check quinte / couleur
        if(count($combin) >= 5){
            $nbQ = 0;
            $nbColor = 0;
            foreach($combin as $k => $co):
                $cardExploded = explode("-", $co);
                $nbCard = $cardExploded[0];
                $color = $cardExploded[1];
                $key = ($k < count($combin)-1) ? $k+1 : $k;
                $cardExploded2 = explode("-", $combin[$key]);
                $color1 = $cardExploded2[1];
                $nbCard2 = $cardExploded2[0];
                if($nbCard-1 == $nbCard2)
                    $nbQ++;
                if($color == $color1)
                    $nbColor++;
            endforeach;
            if($nbColor >= 5)
                $couleur = true;
            if($nbQ >= 5)
                $quinte = true;      
        }
        if($quinte == false){
            if (count($paires) >= 2) {
                $combName = 'Paire';
                //echo count($c);
                if(count($paires) === 3)
                    $combName = 'Brelan';
                if(count($paires) === 4)
                    $combName = 'Doubles paires';
                if(count($paires) === 5)
                    $combName = '5 Full';
                if(count($paires) === 6)
                    $combName = 'Full';
                foreach ($paires as $k => $paire):
                    array_unshift($combin, $paire);
                endforeach;
                $combin = $this->reOrder($combin, 1);
            }
        }elseif($couleur == false){
            $combName = 'Quinte';
        }
        if($couleur == true){
            $combName = 'Couleur';
        }
        //removed cards unused
        if (count($combin) > 5) {
            $i = 0;
            foreach ($combin as $k => $c):
                if ($i > 4) {
                    unset($combin[$k]);
                }
                $i++;
            endforeach;
        }
        
        
        return array('cards' => $combin, 'name' => $combName);
    }

    private function reOrder($combin, $s = null) {
        $new = array();
        foreach ($combin as $k => $c):
            $e = explode('-', $c);
            if (!in_array($c, $new)) {
                $new[] = $c;
            }
        endforeach;
        if($s == null)
            rsort($new, 1);
        
        return $new;
    }

}
