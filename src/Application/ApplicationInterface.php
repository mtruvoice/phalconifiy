<?php

namespace Phalconify\Application;

interface ApplicationInterface
{
    public function initialise();

    public function setNotFoundHandler();
}
