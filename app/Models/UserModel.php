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

    public function fetchAllMembersByPositionId($position_id)
    {
        return $this->databaseExplorer->table('team_member')
            ->where('team_position_fk', $position_id)
            ->fetchAll();
    }

    public function fetchAllMembersByPositionIdNamePairs($position_id)
    {
        $allMembers = $this->fetchAllMembersByPositionId($position_id);
        $transformedMembers = [];

        foreach ($allMembers as $member) {
            $member_id = $member->id;
            $member_name = $member->first_name . " " . $member->last_name;

            $transformedMembers[$member_id] = $member_name;
        }

        return $transformedMembers;
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

    public function assignMemberToTeam($member_id, $team_id)
    {
        $this->databaseExplorer
            ->table('team_member')
            ->where('id', $member_id)
            ->update([
                'team_fk' => $team_id
            ]);
    }
    
}
