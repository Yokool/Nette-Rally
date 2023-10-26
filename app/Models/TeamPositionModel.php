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

    public function fetchAllPositions()
    {
        return $this->databaseExplorer
            ->table('team_position')
            ->fetchAll();
    }

    /**
     * Fetches all the positions in the format
     * of
     * ID => NAME
     * Useful when you want to use the positions
     * in a select list.
     */
    public function fetchAllPositionsAsIdNamePairs()
    {
        $fetchedPositions = $this->fetchAllPositions();

        $idNamePairs = [];

        foreach ($fetchedPositions as $fetchedPosition) {
            $id = $fetchedPosition['id'];
            $positionName = $fetchedPosition['name'];
            
            $idNamePairs[$id] = $positionName;
        }

        
        return $idNamePairs;
    }
    
}
