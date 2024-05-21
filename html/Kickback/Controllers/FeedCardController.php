<?php
declare(strict_types=1);

namespace Kickback\Controllers;

use Kickback\Services\Database;
use Kickback\Views\vActivity;
use Kickback\Views\vFeedCard;
use Kickback\Views\vNews;
use Kickback\Views\vQuote;

class FeedCardController
{

    public static function vQuote_to_vFeedCard(vQuote $quote) : vFeedCard {
        $feedCard = new vFeedCard();

        
        $feedCard->type = "QUOTE";
        $feedCard->typeText = "QUOTE";
        $feedCard->description = $quote->text;
        $feedCard->icon = $quote->icon;
        $feedCard->dateTimeFormattedBasic = $quote->date;
        $feedCard->quoteStyleText = true;
        $feedCard->quote = $quote;
        $feedCard->createdByPrefix = "Said";
        $feedCard->hideCTA = true;
        $feedCard->hideType = true;
        $feedCard->hasCreatedBy = false;
        $feedCard->cssClassImageColSize = "col col-md";
        $feedCard->cssClassTextColSize = "col col-8 col-md-8 col-lg-9";
        $feedCard->title = "";


        return $feedCard;
    }

    public static function vNews_to_vFeedCard(vNews $news) : vFeedCard {
        $feedCard = new vFeedCard();
        $feedCard->type = $news->type;
        $feedCard->typeText = $news->type;


        if ($news->quest != null)
        {
            $feedCard->icon = $news->quest->icon;
            $feedCard->title = $news->quest->title;
            $feedCard->description = $news->quest->summary;
            $feedCard->quest = $news->quest;
            $feedCard->hasRewards = true;
        }

        if ($news->blogPost != null)
        {
            $feedCard->icon = $news->blogPost->icon;
            $feedCard->title = $news->blogPost->title;
            $feedCard->description = $news->blogPost->summary;
        }

        return $feedCard;
    }

    public static function vActivity_to_vFeedCard(vActivity $activity) : vFeedCard
    {
        $feedCard = new vFeedCard();
        $feedCard->activity = $activity;
        $feedCard->type = $activity->type;
        $feedCard->typeText = $activity->type;
        $feedCard->setDateTime($activity->dateTime);
        $feedCard->icon = $activity->getMedia();
        $feedCard->url = $activity->url;
        switch ($feedCard->type) {
            case 'QUEST-PARTICIPANT':
                $feedCard->cta = "View Quest";
                $feedCard->typeText = "PARTICIPATION";
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username.' '.$activity->verb.' '.$activity->name;

                if ($activity->verb == "BAILED ON")
                {

                    $feedCard->description = GetBailedFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTimeString);
                }
                else{
                    $feedCard->description = GetParticipationFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTimeString);

                }


                break;
            case 'GAME-RECORD':
                $feedCard->hideCTA = true;
                $feedCard->typeText = "RANKED MATCH";
                $feedCard->createdByShowOnlyDate = true;

                $feedCard->title = $activity->account->username.' '.$activity->verb." a ranked match of ".$activity->name;


                if ($activity->verb == "WON")
                {
                    $feedCard->description = GetWonMatchFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTimeString);
                }
                else{
                    //GetLostMatchFlavorText
                    $feedCard->description = GetLostMatchFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTimeString);
                }

                break;
            case 'SPENT-PRESTIGE-TOKEN':
                $feedCard->hideCTA = true;
                $feedCard->typeText = $activity->verb;
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " " . $activity->name;
                
                if ($activity->verb == "COMMENDED") {
                    $feedCard->description = GetCommendedSomeoneFlavorText($activity->account->username, $activity->name, $activity->account->username . $activity->name . $activity->dateTimeString);
                } else {
                    $feedCard->description = GetDenouncedSomeoneFlavorText($activity->account->username, $activity->name, $activity->account->username . $activity->name . $activity->dateTimeString);
                }
                break;

            case 'RECEIVED-PRESTIGE':
                $feedCard->hideCTA = true;
                $feedCard->typeText = $activity->verb;
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->name . ' ' . $activity->verb . " " . $activity->account->username;
                
                if ($activity->verb == "COMMENDED") {
                    $feedCard->description = GetCommendedSomeoneFlavorText($activity->name, $activity->account->username, $activity->name . $activity->account->username . $activity->dateTimeString);
                } else {
                    $feedCard->description = GetDenouncedSomeoneFlavorText($activity->name, $activity->account->username, $activity->name . $activity->account->username . $activity->dateTimeString);
                }
                break;

            case 'QUEST-HOSTED':
                $feedCard->cta = "View Quest";
                $feedCard->typeText = $activity->verb;
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " " . $activity->name;
                $feedCard->description = GetHostedQuestFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTimeString);
                break;

            case 'BADGE':
                $feedCard->hideCTA = true;
                $feedCard->typeText = "NEW BADGE";
                $feedCard->createdByShowOnlyDate = true;
                if ($activity->verb == "NOMINATED") {
                    $activity->verb = "was NOMINATED for";
                }
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " the " . $activity->name . " badge!";
                $feedCard->description = GetEarnedBadgeFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTimeString);
                break;

            case 'TOURNAMENT':
                $feedCard->cta = "View Tournament";
                $feedCard->typeText = $activity->verb . " TOURNAMENT";
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " in the " . $activity->name . " quest!";
                if ($activity->verb == "WON") {
                    $feedCard->description = GetWinTournamentFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTimeString);
                } else {
                    $feedCard->description = GetLostTournamentFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTimeString);
                }
                break;

            case 'WROTE-BLOG-POST':
                $feedCard->hideCTA = true;
                $feedCard->typeText = "NEW POST";
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' just ' . $activity->verb . " \"" . $activity->name . "\"";
                $feedCard->description = GetWroteBlogPostFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTimeString);
                break;
        }

        return $feedCard;
    }
}
?>
