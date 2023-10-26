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
    
}
