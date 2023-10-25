<?php

declare(strict_types=1);

namespace App\Models;

use Nette;


final class UserModel
{

    public function __construct(
        private Nette\Database\Explorer $databaseExplorer
    ) {
        
    }

    public function insertNewMember(
        $first_name,
        $last_name,
        $team_positon_key
        )
    {
        $this->databaseExplorer
            ->table('team_member')
            ->insert([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'team_position_fk' => $team_positon_key,
            ]);
    }

    public function fetchAllMembers()
    {
        $fetchedMembers = $this->databaseExplorer
            ->table('team_member')
            ->fetchAll();

        return $this->transformMembersToOuputForm($fetchedMembers);

        
    }

    /**
     * REWRITE THIS USING SQL
     */
    public function transformMembersToOuputForm($fetchedMembers)
    {
        $transformedMembers = [];
        foreach ($fetchedMembers as $fetchedMember) {
            
            $position = $fetchedMember
                ->ref('team_position', 'team_position_fk');
            
                $memberArray = $fetchedMember->toArray();
                $memberArray['team_position_name'] = $position['name'];
                $transformedMembers[] = $memberArray;   
        }

        return $transformedMembers;

    }
    
}
