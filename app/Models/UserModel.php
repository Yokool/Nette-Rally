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
        $last_name
        )
    {
        $this->databaseExplorer
            ->table('team_member')
            ->insert([
                'first_name' => $first_name,
                'last_name' => $last_name,
            ]);
    }
    
}
