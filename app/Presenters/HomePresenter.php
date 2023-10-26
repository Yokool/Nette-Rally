<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Models\UserModel;
use App\Models\TeamPositionModel;

final class HomePresenter extends Nette\Application\UI\Presenter
{

    public function __construct(
        private UserModel $memberModel,
        private TeamPositionModel $teamPositionModel
    ) {

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
        

        // Add all possible positions into the form
        $allPositions = $this->teamPositionModel->fetchAllPositions();
        
        foreach ($allPositions as $position) {
            $position_id = $position->id;
            $position_name = $position->name;

            $allElligibleMembers = $this->memberModel->fetchAllMembersByPositionIdNamePairs($position_id);
            
            $form->addMultiSelect(
                "position_" . $position_id,
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
        $this->redirectPermanent("Home:");
    }

    

}
