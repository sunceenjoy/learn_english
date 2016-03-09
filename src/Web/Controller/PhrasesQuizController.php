<?php

namespace Eng\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Eng\Core\Exception\EngNotFoundException;
use Eng\Core\Repository\Entity\PhrasesEntity;

/**
 * Description of words
 *
 * @author grantsun
 */
class PhrasesQuizController extends BaseController
{
    public function quiz()
    {
        return $this->render('web/phrases/quiz.html.twig');
    }

    public function startQuiz()
    {
        $status = $this->request->query->get('status', PhrasesEntity::NEWONE);
        $numbers = $this->request->query->get('numbers', 50);
        $phrases = $this->em->getRepository('Eng:PhrasesEntity')->getQuizList($status, $numbers);
        shuffle($phrases);
        $json = array('status' => 0, 'items' => $phrases);
        if (count($phrases) == 0) {
            $json['status'] = 1;
        }
        return new JsonResponse($json);
    }

    public function updateQuizResult()
    {
        $isSuccess = $this->request->query->get('is_success');
        $status = $this->request->query->get('status', PhrasesEntity::NEWONE);
        $wordId = $this->request->query->get('id');

        $query = $this->em->createQuery("SELECT w FROM Eng:PhrasesMarkerEntity w WHERE w.status = :status");
        $query->setParameter('status', $status);
        $markerInfo = $query->getOneOrNullResult();
        if (!$markerInfo) {
            throw new EngNotFoundException("Can not find a proper marker!");
        }

        $query = $this->em->createQuery("SELECT w FROM Eng:PhrasesEntity w WHERE w.id = :id");
        $query->setParameter('id', $wordId);
        $wordInfo = $query->getOneOrNullResult();
        if (!$wordInfo) {
            throw new EngNotFoundException("Can not find a proper word by ID!");
        }

        $quizModule = new \Eng\Web\Module\Phrases\Quiz\Quiz($markerInfo);
        if ($isSuccess) {
            $quizModule->doKnowUpdate($wordInfo);
        } else {
            $quizModule->doUnKnowUpdate($wordInfo);
        }

        $this->em->persist($wordInfo);
        $this->em->flush();

        $learningHistoryRepository = $this->em->getRepository('Eng:LearningHistoryEntity');
        $learningHistoryRepository->incrHistory('phrases', $status, $isSuccess);
        
        $json = array('status' => 0);
        return new JsonResponse($json);
    }
}
