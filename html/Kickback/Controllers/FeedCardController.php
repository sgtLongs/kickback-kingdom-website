<?php
declare(strict_types=1);

namespace Kickback\Controllers;

use Kickback\Services\Database;
use Kickback\Views\vActivity;
use Kickback\Views\vFeedCard;
use Kickback\Views\vNews;
use Kickback\Views\vQuote;
use Kickback\Views\vDateTime;
use Kickback\Views\vQuest;
use Kickback\Views\vQuestLine;

use DateTime;

class FeedCardController
{
    public static function vQuote_to_vFeedCard(vQuote $quote) : vFeedCard {
        $feedCard = new vFeedCard();

        $feedCard->type = "QUOTE";
        $feedCard->typeText = "QUOTE";
        $feedCard->description = $quote->text;
        $feedCard->icon = $quote->icon;
        $feedCard->dateTime = new vDateTime();
        $feedCard->dateTime->formattedBasic = $quote->date;
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

    public static function vQuest_to_vFeedCard(vQuest $quest) : vFeedCard {
        $feedCard = new vFeedCard();
        $feedCard->type = "QUEST";
        $feedCard->typeText = "QUEST";
        
        $feedCard->icon = $quest->icon;
        $feedCard->title = $quest->title;
        $feedCard->description = $quest->summary;
        $feedCard->quest = $quest;
        $feedCard->hasRewards = true;
        $feedCard->hasTags = true;
        $feedCard->cssClassCard = "quest-card";
        $feedCard->cssClassRight = "quest-card-right";
        $feedCard->dateTime = $quest->endDate;

        $feedCard->createdByPrefix = "Hosted";
        $feedCard->cta = "View Quest";
        $feedCard->url = $quest->getURL();
        

        $feedCard->reviewStatus = $quest->reviewStatus;

        return $feedCard;
    }

    public static function vQuestLine_to_vFeedCard(vQuestLine $questLine) : vFeedCard {
        $feedCard = new vFeedCard();
        $feedCard->type = "QUEST-LINE";
        $feedCard->typeText = "QUEST LINE";
        $feedCard->cta = "View Quest Line";
        $feedCard->createdByPrefix = "Created";
        $feedCard->url = $questLine->getURL();
        $feedCard->dateTime = $questLine->dateCreated;
        $feedCard->icon = $questLine->icon;
        $feedCard->title = $questLine->title;
        $feedCard->description = $questLine->summary;
        $feedCard->reviewStatus = $questLine->reviewStatus;
        $feedCard->questLine = $questLine;



        return $feedCard;
    }

    public static function vNews_to_vFeedCard(vNews $news) : vFeedCard {
        

        if ($news->quest != null)
        {
            return self::vQuest_to_vFeedCard($news->quest);
        }

        $feedCard = new vFeedCard();
        $feedCard->type = $news->type;
        $feedCard->typeText = $news->type;
        if ($news->blogPost != null)
        {
            $feedCard->icon = $news->blogPost->icon;
            $feedCard->title = $news->blogPost->title;
            $feedCard->description = $news->blogPost->summary;
            $feedCard->blogPost = $news->blogPost;
            $feedCard->dateTime = $news->blogPost->publishedDateTime;
            $feedCard->createdByPrefix = "Written";

            $feedCard->url = $news->blogPost->getURL();
            $feedCard->reviewStatus = $news->blogPost->reviewStatus;
        }

        if ($feedCard->reviewStatus->published == false) {
            $feedCard->title = "[DRAFT] ".$feedCard->title." [DRAFT]"; 
        }

        return $feedCard;
    }

    public static function vActivity_to_vFeedCard(vActivity $activity) : vFeedCard
    {
        $feedCard = new vFeedCard();
        $feedCard->activity = $activity;
        $feedCard->type = $activity->type;
        $feedCard->typeText = $activity->type;
        $feedCard->dateTime = $activity->dateTime;
        $feedCard->icon = $activity->getMedia();
        $feedCard->url = '/'.$activity->url;
        switch ($feedCard->type) {
            case 'QUEST-PARTICIPANT':
                $feedCard->cta = "View Quest";
                $feedCard->typeText = "PARTICIPATION";
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username.' '.$activity->verb.' '.$activity->name;

                if ($activity->verb == "BAILED ON")
                {

                    $feedCard->description = GetBailedFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTime->valueString);
                }
                else{
                    $feedCard->description = GetParticipationFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTime->valueString);

                }


                break;
            case 'GAME-RECORD':
                $feedCard->hideCTA = true;
                $feedCard->typeText = "RANKED MATCH";
                $feedCard->createdByShowOnlyDate = true;

                $feedCard->title = $activity->account->username.' '.$activity->verb." a ranked match of ".$activity->name;


                if ($activity->verb == "WON")
                {
                    $feedCard->description = GetWonMatchFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTime->valueString);
                }
                else{
                    //GetLostMatchFlavorText
                    $feedCard->description = GetLostMatchFlavorText($activity->account->username, $activity->name, $activity->account->username.$activity->name.$activity->dateTime->valueString);
                }

                break;
            case 'SPENT-PRESTIGE-TOKEN':
                $feedCard->hideCTA = true;
                $feedCard->typeText = $activity->verb;
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " " . $activity->name;
                
                if ($activity->verb == "COMMENDED") {
                    $feedCard->description = GetCommendedSomeoneFlavorText($activity->account->username, $activity->name, $activity->account->username . $activity->name . $activity->dateTime->valueString);
                } else {
                    $feedCard->description = GetDenouncedSomeoneFlavorText($activity->account->username, $activity->name, $activity->account->username . $activity->name . $activity->dateTime->valueString);
                }
                break;

            case 'RECEIVED-PRESTIGE':
                $feedCard->hideCTA = true;
                $feedCard->typeText = $activity->verb;
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->name . ' ' . $activity->verb . " " . $activity->account->username;
                
                if ($activity->verb == "COMMENDED") {
                    $feedCard->description = GetCommendedSomeoneFlavorText($activity->name, $activity->account->username, $activity->name . $activity->account->username . $activity->dateTime->valueString);
                } else {
                    $feedCard->description = GetDenouncedSomeoneFlavorText($activity->name, $activity->account->username, $activity->name . $activity->account->username . $activity->dateTime->valueString);
                }
                break;

            case 'QUEST-HOSTED':
                $feedCard->cta = "View Quest";
                $feedCard->typeText = $activity->verb;
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " " . $activity->name;
                $feedCard->description = GetHostedQuestFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTime->valueString);
                break;

            case 'BADGE':
                $feedCard->hideCTA = true;
                $feedCard->typeText = "NEW BADGE";
                $feedCard->createdByShowOnlyDate = true;
                if ($activity->verb == "NOMINATED") {
                    $activity->verb = "was NOMINATED for";
                }
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " the " . $activity->name . " badge!";
                $feedCard->description = GetEarnedBadgeFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTime->valueString);
                break;

            case 'TOURNAMENT':
                $feedCard->cta = "View Tournament";
                $feedCard->typeText = $activity->verb . " TOURNAMENT";
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' ' . $activity->verb . " in the " . $activity->name . " quest!";
                if ($activity->verb == "WON") {
                    $feedCard->description = GetWinTournamentFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTime->valueString);
                } else {
                    $feedCard->description = GetLostTournamentFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTime->valueString);
                }
                break;

            case 'WROTE-BLOG-POST':
                $feedCard->hideCTA = true;
                $feedCard->typeText = "NEW POST";
                $feedCard->createdByShowOnlyDate = true;
                $feedCard->title = $activity->account->username . ' just ' . $activity->verb . " \"" . $activity->name . "\"";
                $feedCard->description = GetWroteBlogPostFlavorText($activity->account->username, $activity->name, $activity->name . $activity->account->username . $activity->dateTime->valueString);
                break;
        }

        return $feedCard;
    }
}
?>
