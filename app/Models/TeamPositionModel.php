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
            $minMaxArray[$position->id] = $positionAsArray;
        }

        return $minMaxArray;
    }

    public static function arePositionCountersAllValid($positionCounterArray)
    {
        $validationResult = true;
        $validationResultCompleteMessages = [];
        foreach ($positionCounterArray as $positionCounterElement)
        {
            $isValidResult = TeamPositionModel::isPositionCounterValid($positionCounterElement);
            
            
            if(!$isValidResult['validationResult'])
            {
                $validationResult = false;
                $validationResultCompleteMessages[] = $isValidResult['errorMessage'];
                continue;
            }

        }

        
        return [
            'validationResult' => $validationResult,
            'validationResultCompleteMessages' => $validationResultCompleteMessages,
        ];
    }
    
    // Checks one of the members of fetchAllPositionByMinMaxArray()
    // to determine whether it adheres to the contraints
    // given by the database.
    public static function isPositionCounterValid($positionCounterElement)
    {
        $positionName = $positionCounterElement['name'];
        $minAllowed = $positionCounterElement['min_allowed'];
        $maxAllowed = $positionCounterElement['max_allowed'];
        $counter = $positionCounterElement['memberCounter'];

        $validationResult = ($counter >= $minAllowed) && ($counter <= $maxAllowed);

        return [
            'validationResult' => $validationResult,
            'errorMessage' => "Počet členů v týmu na pozici $positionName by měl být mezi čísly $minAllowed a $maxAllowed. Nyní máte $counter.\n",
        ];

    }
    
}
