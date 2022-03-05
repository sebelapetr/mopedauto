<?php

namespace App\BackModule\Presenters;

use App\Model\Episode;
use App\Model\Orm;
use App\Model\Serial;
use App\Model\Serie;
use Tracy\Debugger;

class ExportPresenter extends BasePresenter{


    public function __construct(Orm $orm)
    {
        parent::__construct($orm);
    }

    public function actionDefault()
    {
    }

    public function actionSerials($limit, $offset){

        $serials = $this->orm->serials->findAll()->limitBy($limit, $offset);
        /** @var Serial $serial */
        $i = 0;
        foreach ($serials as $serial){
            $array["serial"][$i]["id"] = $serial->id;
            $array["serial"][$i]["name"] = $serial->name;
            $array["serial"][$i]["description"] = $serial->description;

            $series = $serial->serie;
            $y = 0;
            /** @var Serie $serie */
            foreach($series as $serie){
                $array["serial"][$i]["serie"][$y]["id"] = $serie->id;
                $array["serial"][$i]["serie"][$y]["name"] = $serie->name;
                $y++;

                $episodes = $serie->episodes;
                $z = 0;
                /** @var Episode $episode */
                foreach ($episodes as $episode){

                    $array["serial"][$i]["serie"][$y]["episode"][$z]["id"] = $episode->id;
                    $array["serial"][$i]["serie"][$y]["episode"][$z]["name"] = $episode->name;
                    $array["serial"][$i]["serie"][$y]["episode"][$z]["description"] = $episode->description;
                    $z++;
                }

            }
        $i++;
        }
        $json = json_encode($array);
        $this->sendJson($json);
    }
}



