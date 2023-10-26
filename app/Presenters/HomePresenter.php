<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\UserModel;
use App\Models\TeamPositionModel;
use App\Models\TeamModel;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    const POSITION_FORM_PREFIX = "position_";

    private $allPositions;

    public function __construct(
        private UserModel $memberModel,
        private TeamPositionModel $teamPositionModel,
        private TeamModel $teamModel
    ) {

        // Add all possible positions into the form
        $this->allPositions = $this->teamPositionModel->fetchAllPositions();
        
    }


    public function renderDefault()
    {
        $members = $this->memberModel->fetchAllMembers();
        $positions = $this->teamPositionModel->fetchAllPositions();
        $teamsWithMembers = $this->teamModel->fetchAllTeamsWithMembers();


        $this->template->members = $members;
        $this->template->positions = $positions;
        $this->template->teams = $teamsWithMembers;

    }

    public function createComponentMemberAddForm(): Form
    {
        $form = new Form;
        $form->addText('first_name')->setRequired('Please fill in your first name.');
        $form->addText('last_name')->setRequired('Please fill in your last name.');
        
        $positionNames = $this->teamPositionModel->fetchAllPositionsAsIdNamePairs();
        $form->addSelect('team_position', 'Pozice', $positionNames);

        $form->addSubmit('add_user');
        $form->onSuccess[] = [$this, 'memberAddFormSucceeded'];
        return $form;
    }

    public function memberAddFormSucceeded(Form $form, $data)
    {
        $this->memberModel->insertNewMember(
            $data['first_name'],
            $data['last_name'],
            $data['team_position']
        );

        $this->redirectPermanent("Home:");
    }

    public function createComponentTeamAddForm(): Form
    {
        $form = new Form;
        $form->addText('team_name')->setRequired('Prosím vyplňte jméno týmu.');
        

        foreach ($this->allPositions as $position) {
            $position_id = $position->id;
            $position_name = $position->name;

            $allElligibleMembers = $this->memberModel->fetchAllMembersByPositionIdNamePairs($position_id);
            
            $form->addMultiSelect(
                self::POSITION_FORM_PREFIX . $position_id,
                $position_name,
                $allElligibleMembers
            );
        }

        $form->addSubmit('add_team');
        $form->onSuccess[] = [$this, 'teamAddFormSucceeded'];
        return $form;
    }

    public function teamAddFormSucceeded(Form $form, $data)
    {
        // Prepare all the positions with a counter
        // to see of what positions the team will be
        // made up of.
        // index by id's from the DB
        $positionCounterArray = $this->teamPositionModel->fetchAllPositionByMinMaxArray();
        
        // We also start transforming the $data
        // structure to collect all the members
        // into a list.
        $collectedMemberIDs = [];

        // Team name
        $team_name = $data['team_name'];
        
        // iterate over all existing positions and their data
        foreach ($this->allPositions as $position) {
            $position_id = $position->id;

            // create a key to index the data from the form
            $data_key = self::POSITION_FORM_PREFIX . $position_id;
            
            // Get the IDs of all members that were
            // assigned to this position.
            $memberIdArray = $data[$data_key];

            // Get the reference for counter for the position
            // we are iterating.
            $positionCounter = &$positionCounterArray[$position_id];
            
            // Take all the members we
            // want to assign to this job.
            foreach ($memberIdArray as $memberId)
            {                
                // Add another member
                $positionCounter['memberCounter'] += 1;
                $collectedMemberIDs[] = $memberId;
            }


        }

        $areValid = TeamPositionModel::arePositionCountersAllValid($positionCounterArray);

        // Complete validation succeess
        if($areValid['validationResult'])
        {
            $this->onTeamFormValidationSuccess(
                $team_name,
                $collectedMemberIDs,
            );
            return;
        }

        // Validation failure, print messages
        $validationErrorMessages = $areValid['validationResultCompleteMessages'];

        foreach ($validationErrorMessages as $validationErrorMessage) {
            $this->flashMessage($validationErrorMessage);
        }

    }

    public function onTeamFormValidationSuccess(
        $team_name,
        $collectedMemberIDs,
        )
    {
        // For all the positions we have
        // we should now assign every member
        
        $this->flashMessage("Tým úspěšně vytvořen.");
        $this->teamModel->createTeamWithMembers($team_name, $collectedMemberIDs);
        $this->redirectPermanent("Home:");
    }

    

}
