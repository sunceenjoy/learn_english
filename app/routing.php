<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'portal',
    new Route(
        '/',
        array('_controller' => 'Eng\Web\Controller\WordsController::listAction',)
    )
);

$collection->add(
    'words-quiz',
    new Route(
        '/words-quiz',
        array('_controller' => 'Eng\Web\Controller\WordsQuizController::quiz',)
    )
);

$collection->add(
    'words-quiz-update',
    new Route(
        '/words-quiz/update',
        array('_controller' => 'Eng\Web\Controller\WordsQuizController::updateQuizResult',)
    )
);

$collection->add(
    'words-quiz-start',
    new Route(
        '/words-quiz/start',
        array('_controller' => 'Eng\Web\Controller\WordsQuizController::startQuiz',)
    )
);

$collection->add(
    'phrases-quiz',
    new Route(
        '/phrases-quiz',
        array('_controller' => 'Eng\Web\Controller\PhrasesQuizController::quiz',)
    )
);

$collection->add(
    'phrases-quiz-update',
    new Route(
        '/phrases-quiz/update',
        array('_controller' => 'Eng\Web\Controller\PhrasesQuizController::updateQuizResult',)
    )
);

$collection->add(
    'phrases-quiz-start',
    new Route(
        '/phrases-quiz/start',
        array('_controller' => 'Eng\Web\Controller\PhrasesQuizController::startQuiz',)
    )
);

$collection->add(
    'chart-words',
    new Route(
        '/chart-words',
        array('_controller' => 'Eng\Web\Controller\ChartController::words',)
    )
);

$collection->add(
    'chart-phrases',
    new Route(
        '/chart-phrases',
        array('_controller' => 'Eng\Web\Controller\ChartController::phrases',)
    )
);

$collection->add(
    'chart-ajax',
    new Route(
        '/chart-ajax',
        array('_controller' => 'Eng\Web\Controller\ChartController::ajax',)
    )
);

$collection->add(
    'login',
    new Route(
        '/login',
        array('_controller' => 'Eng\Web\Controller\AuthController::login',)
    )
);

$collection->add(
    'post-login',
    new Route(
        '/post-login',
        array('_controller' => 'Eng\Web\Controller\AuthController::postLogin',)
    )
);

$collection->add(
    'logout',
    new Route(
        '/logout',
        array('_controller' => 'Eng\Web\Controller\AuthController::logout',)
    )
);

return $collection;
