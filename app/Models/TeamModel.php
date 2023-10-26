<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use App\Models\UserModel;

final class TeamModel
{

    public function __construct(
        private UserModel $userModel,
        private Nette\Database\Explorer $databaseExplorer
    ) {
        
    }

    public function createTeamWithMembers($teamName, $memberIDList)
    {
        $createdTeam = $this->databaseExplorer->table('team')
            ->insert([
                'name' => $teamName,
            ]);
        
        $createdTeamID = $createdTeam['id'];
        
        $this->linkMembersToTeam($createdTeamID, $memberIDList);
    }

    public function linkMembersToTeam($teamID, $memberIDList)
    {
        $decompositionTable = $this->databaseExplorer->table('team_member__team');
        foreach ($memberIDList as $memberID)
        {
            $decompositionTable->insert([
                'team_member_fk' => $memberID,
                'team_fk' => $teamID,
            ]);
        }
    }

    public function fetchAllTeams() {
        return $this->databaseExplorer->table('team')
            ->fetchAll();
    }

    public function fetchAllTeamsWithMembers() {
        $allTeams = $this->fetchAllTeams();
        
        $transformedTeams = [];
        // Go through all teams
        foreach ($allTeams as $team)
        {
            $teamArray = $team->toArray();
            $teamArray['members'] = [];
            // Get the decomposition table for all team member
            foreach ($team->related('team_member__team') as $decompositionTable)
            {
                // Get reference for team member
                $teamMember = $decompositionTable->ref('team_member', 'team_member_fk');
                
                // Get the position of the member associated with this team   
                $memberPosition = $teamMember->ref('team_position', 'team_position_fk')->toArray()['name'];

                // Add the member under into the team under
                // his position.
                $teamArray['members'][$memberPosition][] = $teamMember->toArray();
            }    

            $transformedTeams[] = $teamArray;
        }

        bdump($transformedTeams);

        return $transformedTeams;
    }
    
}
