<?php
declare(strict_types=1);

namespace Kickback\Views;

use Kickback\Views\vAccount;
use Kickback\Views\vDateTime;
use Kickback\Views\vTournament;
use Kickback\Views\vContent;
use Kickback\Services\Session;
use Kickback\Views\vReviewStatus;
use Kickback\Views\vQuestLine;
use Kickback\Models\PlayStyle;
use Kickback\Controllers\QuestLineController;

class vQuest extends vRecordId
{
    public string $title;
    public string $locator;
    public string $summary;
    public ?vDateTime $endDate;
    public vAccount $host1;
    public ?vAccount $host2 = null;
    public vReviewStatus $reviewStatus;
    public PlayStyle $playStyle; 

    public bool $requiresApplication;

    public ?vTournament $tournament = null;
    public vContent $content;
    public ?vRaffle $raffle = null;
    public ?vQuestLine $questLine = null;

    public ?vMedia $icon;
    public ?vMedia $banner;
    public ?vMedia $bannerMobile;

    public ?array $rewards = null;

    function __construct(string $ctime = '', int $crand = -1)
    {
        parent::__construct($ctime, $crand);
    }

    public function getURL() : string {
        return '/q/'.$this->locator;
    }

    public function hasEndDate() : bool {
        return ($this->endDate != null);
    }

    public function hasExpired() : bool {
        return ($this->hasEndDate() && $this->endDate->isExpired());
    }

    public function isTournament() : bool {
        return ($this->tournament != null);
    }

    public function isRaffle() : bool {
        return ($this->raffle != null);
    }

    public function isBracketTournament() : bool {
        return ($this->isTournament() && $this->tournament->hasBracket);
    }

    public function hasQuestLine() : bool {
        return ($this->questLine != null);
    }

    public function canEditQuest() : bool {
        return $this->isQuestHost() || Session::isAdmin();
    }

    public function isQuestHost() : bool {
        if (Session::isLoggedIn())
        {
            return (Session::getCurrentAccount()->crand == $this->host1->crand || ($this->host2 != null && Session::getCurrentAccount()->crand == $this->host2->crand));
        }
        else{
            return false;
        }
    }

    public function hasContent() : bool {
        return ($this->content != null);
    }

    public function getHost2Id() : string {
        if ($this->host2 == null)
            return "";
        return $this->host2->crand;
    }

    public function getHost2Username() : string {
        if ($this->host2 == null)
            return "";
        return $this->host2->username;
    }

    public function nameIsValid() : bool {
        $valid = StringIsValid($this->title, 10);
        if ($valid) 
        {
            if (strtolower($this->title) == "new quest")
                $valid = false;
        }
        return $valid;
    }

    public function summaryIsValid() : bool {
        $valid = StringIsValid($this->summary, 200);
        return $valid;
    }
    
    public function pageContentIsValid() : bool {
        return ($this->hasContent() && ($this->content->isValid()));
    }

    public function locatorIsValid() : bool {
        $valid = StringIsValid($this->locator, 5);
        if ($valid) 
        {
            if (strpos(strtolower($this->locator), 'new-quest-') === 0) {
                $valid = false;
            }
        }
        return $valid;
    }

    public function imagesAreValid() : bool {
        return self::imageIsValid($this->icon) && self::imageIsValid($this->banner) && self::imageIsValid($this->bannerMobile);
    }

    private static function imageIsValid($media) : bool {
        return isset($media) && !is_null($media);
    }

    public function rewardsAreValid() : bool {
        return $this->rewards != null && (count($this->rewards) > 0);
    }

    public function isValidForPublish() : bool {
        return $this->nameIsValid() && $this->summaryIsValid() && $this->locatorIsValid() && $this->pageContentIsValid() && $this->imagesAreValid() && $this->rewardsAreValid();
    }

    public function populateQuestLine() : void {
        if ($this->hasQuestLine())
        {
            $resp = QuestLineController::getQuestLineById($this->questLine);
            if ($resp->success)
                $this->questLine = $resp->data;
            else
                throw new \Exception($resp->message);
                
        }
    }


}



?>