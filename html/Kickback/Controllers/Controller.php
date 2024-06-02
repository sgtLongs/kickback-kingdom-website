<?php

namespace Kickback\Controllers;
use \DateTime;
use \InvalidArgumentException;

class Controller
{

    public function __construct()
    {

    }

    protected static function parseId(array $Id)
    {
        $ctimePattern = '/ctime/';
        $crandPattern = '/crand/';

        $ctime = new \DateTime();
        $crand = -1;
        
        $numberOfElements = count($Id);

        if($numberOfElements !== 2)
        {
            throw new \InvalidArgumentException("Id Array Must Contain Exactly Two Elements To Parse Id | Array : ".print_r($Id));
        }


        foreach($Id as $key => $value)
        {
            if(preg_match($ctimePattern, $key))
            {
                if(is_string($value))
                {
                    $value = \DateTime::createFromFormat("Y-m-d H:i:s", $value);
                }

                if(!($value instanceof \DateTime))
                {
                    throw new \InvalidArgumentException("Ctime Value Must Be Of Type DateTime Or String To Parse Id | Key : ".$key." Value : ".$value);
                }

                $ctime = $value;

                continue;
            }

            if(preg_match($crandPattern, $key))
            {
                if(is_string($value))
                {
                    $value = (int)$value;
                }

                if(!is_int($value))
                {
                    throw new \InvalidArgumentException("Crand Value Must Be Of Type INT or String | Key : ".$key." Value : ".$value);
                }

                $crand = $value;

                continue;
            }

            throw New \InvalidArgumentException("Id Keys Must Have Only Keys With 'Ctime' or 'Crand' Included | Key : ".$key." Value : ".$value);
        }

        

        $returnArray = ["ctime"=>$ctime, "crand"=>$crand];

        return $returnArray;
    }

    protected static function CIdToStringId($cid)
    {
        $id = Controller::parseId($cid);

        $ctime = $id["ctime"];
        $crand = $id["crand"];

        $ctimeString = $ctime->format("YhdHisu");
        $crandString = (string)$crand;

        $idString = $ctimeString.$crandString;

        return $idString;
    }


    protected static function parseIdAsString(array $Id, string $dateFormat = "Y-m-d H:i:s.u")
    {
        $IdRaw = Controller::parseId($Id);

        $ctimeRaw = $IdRaw["ctime"];
        $crandRaw = $IdRaw["crand"];

        $ctime = $IdRaw["ctime"];
        $crand = $IdRaw["crand"];

        if(!is_string($ctimeRaw))
        {
            try
            {
                $ctime = $ctimeRaw->format($dateFormat);
            }
            catch(Exception $e)
            {
                throw new Exception("Could Not Format ctime Value From Id Into String : ".$e);
            }
        }
        
        if(!is_string($crandRaw))
        {
            try
            {
                $crand = (string)$crandRaw;
            }
            catch(Exception $e)
            {
                throw new Exception("Could Not Cast Crand To String : ".$e);
            }
        }
        
        $returnArray = ["ctime"=>$ctime, "crand"=>$crand];

        return $returnArray;
    }


}

?>