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
        
        foreach ($memberIDList as $memberID)
        {
            $this->userModel->assignMemberToTeam($memberID, $createdTeamID);
        }
    }
    
}
