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

    public function fetchAllPositionsAsArray()
    {
        return $this->databaseExplorer
            ->table('team_position')
            ->fetchPairs('id');
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

    // Takes all the positions and transforms
    // them into an array which contains
    // all the information about the position
    // alongside a simple counter.
    public function fetchAllPositionByMinMaxArray()
    {
        $minMaxArray = [];
        $positionsArray = $this->fetchAllPositions();
        
        foreach ($positionsArray as $position) {
            $positionAsArray = $position->toArray();
            $positionAsArray['memberCounter'] = 0;
            $minMaxArray[] = $positionAsArray;
        }

        return $minMaxArray;
    }

    public static function arePositionCountersAllValid($positionCounterArray)
    {
        foreach ($positionCounterArray as $positionCounterElement)
        {
            $isValid = isPositionCounterValid($positionCounterElement);
            if(!$isValid)
            {
                return false;
            }

            return true;
        }
    }
    
    // Checks one of the members of fetchAllPositionByMinMaxArray()
    // to determine whether it adheres to the contraints
    // given by the database.
    public static function isPositionCounterValid($positionCounterElement)
    {
        $minAllowed = $positionCounterElement['minAllowed'];
        $maxAllowed = $positionCounterElement['maxAllowed'];
        $counter = $positionCounterElement['counter'];

        return ($counter >= $minAllowed) && ($counter <= $maxAllowed);

    }
    
}
