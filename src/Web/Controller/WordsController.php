<?php

namespace Eng\Web\Controller;

use Eng\Core\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Eng\Core\Exception\EngNotFoundException;
use \Eng\Core\Repository\Entity\WordsEntity;

/**
 * Description of words
 *
 * @author grantsun
 */
class WordsController extends BaseController
{
    private function getStatusString()
    {
        return array(
            WordsEntity::DIFFICULT => $this->t('difficult'),
            WordsEntity::NEWONE => $this->t('new'),
            WordsEntity::EASY => $this->t('easy'),
            WordsEntity::MEDIUM => $this->t('medium'),
            WordsEntity::RARE => $this->t('rare'),
        );
    }

    public function listAction()
    {
        $pageSize = 30;
        $currentPage = $this->request->query->get('page', 1);
        $status      = $this->request->query->get('status', WordsEntity::NEWONE);
        if ($status == WordsEntity::ALL) {
            $query = $this->em->createQuery("SELECT w FROM Eng:WordsEntity w");
        } else {
            $query = $this->em->createQuery("SELECT w FROM Eng:WordsEntity w WHERE w.status = :status");
            $query->setParameter('status', $status);
        }

        $paginator = new Paginator($query, $currentPage, $pageSize);

        return $this->render('web/words/list.html.twig', array('paginator' => $paginator, 'status' => $status, 'statusString' => $this->getStatusString()));
    }

    public function changeStatusAction()
    {
        $status = $this->request->query->get('status', WordsEntity::NEWONE);
        $wordId = $this->request->query->get('word_id');

        $word = $this->em->getRepository('Eng:WordsEntity')->find($wordId);

        if (!$word) {
            throw new EngNotFoundException("can not find this word");
        }

        $word->setStatus($status);
        $this->em->flush();

        $json = array('status' => 0);
        return new JsonResponse($json);
    }

    public function saveWordAction()
    {
        $wordId = $this->request->query->get('word_id');
        $means = $this->request->query->get('means');
        $word = $this->em->getRepository('Eng:WordsEntity')->find($wordId);

        if (!$word) {
            throw new EngNotFoundException("can not find this word");
        }

        $word->setMeans($means);
        $this->em->flush();

        $json = array('status' => 0);
        $json['data'] = $word->toArray($this->c);
        return new JsonResponse($json);
    }

    public function newWordAction()
    {
        return $this->render('web/words/newWord.html.twig');
    }

    public function addNewAction()
    {
        $json = array('status' => 0, 'errorWords' => '');
        $key = trim($this->request->request->get('key'));

        $words = preg_split("|[\r\n ]+|i", $key);
        if (empty($words)) {
            return new JsonResponse($json);
        }
        $errorWords = array();
        $successWords = array();
        $updateWords = array();
        foreach ($words as $word) {
            $entity = $this->em->getRepository('Eng:WordsEntity')->findOneBy(array('name' => $word));
            if ($entity === null) {
                $entity = new WordsEntity();
                $entity->setCreateTime(new \Datetime('now'));
                $entity->setName($word);
            }
            try {
                $mean = $this->container['wordMeanDownloader']->download($entity->getName());
                $entity->setMeans(implode("\n", $mean->getMeaning()));
                $entity->setPronunciation($mean->getPhonetic());
                $voiceName = $this->container['wordVoiceDownloader']->download($entity->getName());
                $entity->setVoice($voiceName);
                $entity->setStatus(WordsEntity::NEWONE);
            } catch (\Exception $e) {
                $this->log->addWarning(sprintf("Can not find this word:%s, info:%s", $entity->getName(), $e->getMessage()));
                $errorWords[] = $entity->getName();
                continue;
            }
            if ($entity->getId() !== null) {
                $updateWords[] = $entity->getName();
            } else {
                $successWords[] = $entity->getName();
            }
            $this->em->persist($entity);
            $this->em->flush();
        }

        $message = array();
        if (!empty($successWords)) {
            $message[] = 'new words: '.count($successWords);
        }
        if (!empty($updateWords)) {
            $message[] = 'update words: '.count($updateWords);
        }
        if (!empty($errorWords)) {
            if (empty($successWords) && empty($updateWords)) {
                $json['status'] = 1;
            }
            $message[] = 'error words: '.count($errorWords);
            $json['errorWords'] = implode("\n", $errorWords);
        }
        $json['message'] = implode(', ', $message);
        return new JsonResponse($json);
    }

    public function searchAction()
    {
        $key = $this->request->request->get('key');
        $pageSize = 30;
        $currentPage = $this->request->query->get('page', 1);
        $query = $this->em->createQuery("SELECT w FROM Eng:WordsEntity w WHERE w.name LIKE :name");
        $query->setParameter('name', '%'.$key.'%');
        $paginator = new Paginator($query, $currentPage, $pageSize);

        return $this->render('web/words/list.html.twig', array('paginator' => $paginator, 'status' => WordsEntity::ALL, 'statusString' => $this->getStatusString()));
    }

    public function voiceAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository('Eng:WordsEntity')->findOneBy(array('id' => $id));
        if ($entity === null) {
            throw new EngNotFoundException("can not find this word");
        }
        $voiceName = false;
        $startVendorName = substr($entity->getVoice(), 0, strpos($entity->getVoice(), '/'));
        $vendorName = $startVendorName;
        $i = 0;
        do {
            $vendor = $this->c['wordVoiceDownloader']->searchNextVendor($vendorName);
            $vendorName = $vendor->getVendor();
            if ($vendor->isMe($startVendorName)) {
                break;
            }
            $voiceName = $this->c['wordVoiceDownloader']->downloadByVendor($vendor, $entity->getName(), true);
        } while ($voiceName === false);
        if ($voiceName === false) {
            $json['status'] = 1;
            return new JsonResponse($json);
        }

        $entity->setVoice($voiceName);
        $this->em->persist($entity);
        $this->em->flush();
        $json['data'] = $entity->toArray($this->c);
        $json['status'] = 0;
        return new JsonResponse($json);
    }

    public function test()
    {
         $words = new \Eng\Core\Repository\Entity\TestEntity();
        $words->setName("abc')");
        $this->em->persist($words);
        $this->em->flush();
        $entity =$this->em->getRepository('Eng:TestEntity')->findOneBy(array('id'=>1));
        echo $entity->getName();

        $this->em->getRepository('Eng:TestEntity')->addNewRecord();

        $query = $this->em->createQuery("select u from Eng:TestEntity u order by u.id desc")->setMaxResults(3);
        $users = $query->getResult();

        print_r($users);

        $this->em->find('Eng:TestEntity', 12);

        return $this->render('web/words_list.html.twig');
    }
}
