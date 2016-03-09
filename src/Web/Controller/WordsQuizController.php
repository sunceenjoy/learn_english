<?php

namespace Eng\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Eng\Core\Exception\EngNotFoundException;
use Eng\Core\Repository\Entity\WordsEntity;

/**
 * Description of words
 *
 * @author grantsun
 */
class WordsQuizController extends BaseController
{
    public function quiz()
    {
        return $this->render('web/words/quiz.html.twig');
    }

    public function startQuiz()
    {
        $status = $this->request->query->get('status', WordsEntity::NEWONE);
        $numbers = $this->request->query->get('numbers', 50);
        $words = $this->em->getRepository('Eng:WordsEntity')->getQuizList($status, $numbers);
        shuffle($words);
        $json = array('status' => 0, 'items' => $words);
        if (count($words) == 0) {
            $json['status'] = 1;
        }
        return new JsonResponse($json);
    }

    public function updateQuizResult()
    {
        $isSuccess = $this->request->query->get('is_success');
        $status = $this->request->query->get('status', WordsEntity::NEWONE);
        $wordId = $this->request->query->get('id');

        $query = $this->em->createQuery("SELECT w FROM Eng:WordsMarkerEntity w WHERE w.status = :status");
        $query->setParameter('status', $status);
        $markerInfo = $query->getOneOrNullResult();
        if (!$markerInfo) {
            throw new EngNotFoundException("Can not find a proper marker!");
        }

        $query = $this->em->createQuery("SELECT w FROM Eng:WordsEntity w WHERE w.id = :id");
        $query->setParameter('id', $wordId);
        $wordInfo = $query->getOneOrNullResult();
        if (!$wordInfo) {
            throw new EngNotFoundException("Can not find a proper word by ID!");
        }

        $quizModule = new \Eng\Web\Module\Words\Quiz\Quiz($markerInfo);
        if ($isSuccess) {
            $quizModule->doKnowUpdate($wordInfo);
        } else {
            $quizModule->doUnKnowUpdate($wordInfo);
        }

        $this->em->persist($wordInfo);
        $this->em->flush();

        $learningHistoryRepository = $this->em->getRepository('Eng:LearningHistoryEntity');
        $learningHistoryRepository->incrHistory('words', $status, $isSuccess);
        $json = array('status' => 0);
        return new JsonResponse($json);
    }
}
