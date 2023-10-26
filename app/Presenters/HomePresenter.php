<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\UserModel;
use App\Models\TeamPositionModel;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    const POSITION_FORM_PREFIX = "position_";

    private $allPositions;

    public function __construct(
        private UserModel $memberModel,
        private TeamPositionModel $teamPositionModel
    ) {

        // Add all possible positions into the form
        $this->allPositions = $this->teamPositionModel->fetchAllPositions();
        
    }


    public function renderDefault()
    {
        $members = $this->memberModel->fetchAllMembers();
        $positions = $this->teamPositionModel->fetchAllPositions();

        $this->template->members = $members;
        $this->template->positions = $positions;
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
        $positionCounterArray = $this->teamPositionModel->fetchAllPositionByMinMaxArray();

        $team_name = $data['team_name'];
        
        // iterate over all positions and their data
        foreach ($this->allPositions as $position) {
            $position_id = $position->id;
            $key = self::POSITION_FORM_PREFIX . $position_id;
            
            // Get the IDs of all members that were
            // assigned to this position.
            $memberIdArray = $data[$key];

            $positionCounter = $positionCounterArray[$position_id];

            // Add another member
            $positionCounter['counter'] += 1;

        }

        $areValid = TeamPositionModel::arePositionCountersAllValid($positionCounterArray);
        
        // Perform validation of the counters

        $this->redirectPermanent("Home:");
    }

    

}
