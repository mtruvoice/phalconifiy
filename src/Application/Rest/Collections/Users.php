<?php

namespace Phalconify\Application\Rest\Collections;

use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceValidator;

class Users extends UserBase
{
    /**
     * First name.
     *
     * @var string
     */
    public $firstName;

    /**
     * Last name.
     *
     * @var string
     */
    public $lastName;

    /*
    ** Model Validation
    */
    public function validation()
    {
        $parentValid = parent::validation();
        if (!$parentValid) {
            return false;
        }

        // Ensure firstname is present
        $this->validate(
            new PresenceValidator([
                    'field' => 'firstName',
                    'message' => 'First name is required.',
                ]
            )
        );

        // Ensure lastname is present
        $this->validate(
            new PresenceValidator([
                    'field' => 'lastName',
                    'message' => 'Last name is required.',
                ]
            )
        );

        return $this->validationHasFailed() != true;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
}
