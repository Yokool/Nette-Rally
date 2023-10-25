<?php

declare(strict_types=1);

namespace App\Models;

use Nette;


final class TeamPositionModel
{

    public function __construct(
        private Nette\Database\Explorer $databaseExplorer
    ) {
        
    }

    public function fetchAllPositionsAsIdNamePairs()
    {
        $fetchedPositions = $this->databaseExplorer
            ->table('team_position')
            ->fetchAll();

        $idNamePairs = [];

        foreach ($fetchedPositions as $fetchedPosition) {
            $id = $fetchedPosition['id'];
            $positionName = $fetchedPosition['name'];
            
            $idNamePairs[$id] = $positionName;
        }

        
        return $idNamePairs;
    }
    
}
