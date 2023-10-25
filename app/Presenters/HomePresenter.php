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
            $data['last_name']
        );

        $this->redirect("Home:");
    }

}
