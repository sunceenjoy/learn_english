<?php

namespace Eng\Web\Controller;

use Eng\Core\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Eng\Core\Exception\EngNotFoundException;
use \Eng\Core\Repository\Entity\PronEntity;

/**
 * Description of words
 *
 * @author grantsun
 */
class PronsController extends BaseController
{
    private function getStatusString()
    {
        return array(
            PronEntity::DIFFICULT => $this->t('difficult'),
            PronEntity::NEWONE => $this->t('new'),
            PronEntity::EASY => $this->t('easy'),
            PronEntity::MEDIUM => $this->t('medium'),
            PronEntity::RARE => $this->t('rare'),
        );
    }

    public function listAction()
    {
        $pageSize = 30;
        $currentPage = $this->request->query->get('page', 1);
        $status      = $this->request->query->get('status', PronEntity::NEWONE);
        if ($status == PronEntity::ALL) {
            $query = $this->em->createQuery("SELECT w FROM Eng:PronEntity w");
        } else {
            $query = $this->em->createQuery("SELECT w FROM Eng:PronEntity w WHERE w.status = :status");
            $query->setParameter('status', $status);
        }

        $paginator = new Paginator($query, $currentPage, $pageSize);

        return $this->render('web/prons/list.html.twig', array('paginator' => $paginator, 'status' => $status, 'statusString' => $this->getStatusString()));
    }

    public function changeStatusAction()
    {
        $status = $this->request->query->get('status', PronEntity::NEWONE);
        $wordId = $this->request->query->get('pron_id');

        $word = $this->em->getRepository('Eng:PronEntity')->find($wordId);

        if (!$word) {
            throw new EngNotFoundException("can not find this word");
        }

        $word->setStatus($status);
        $this->em->flush();

        $json = array('status' => 0);
        return new JsonResponse($json);
    }

    public function savePronAction()
    {
        $pronId = $this->request->query->get('pron_id');
        $key = $this->request->query->get('key');
        $value = $this->request->query->get('value');

        if ($key !== 'means' && $key !== 'name') {
            throw new EngRuntimeException("Invalid key type!");
        }

        $entity = $this->em->getRepository('Eng:PronEntity')->find($pronId);

        if (!$entity) {
            throw new EngNotFoundException("can not find this pronunciation!");
        }

        $json = array('status' => 0);
        switch ($key) {
            case 'means':
                $entity->setMeans($value);
                break;
            case 'name':
                $entity->setName($value);
                try {
                    if (str_word_count($entity->getName(), 0) == 1) {
                        $mean = $this->c['wordMeanDownloader']->download($entity->getName());
                        $entity->setMeans(implode("\n", $mean->getMeaning()));
                        $entity->setPronunciation($mean->getPhonetic());
                        $voiceName = $this->c['pronsWordVoiceDownloader']->download($entity->getName());
                        $entity->setVoice($voiceName);
                    } else {
                        $entity->setMeans('NONE');
                        $entity->setPronunciation('NONE');
                        $voiceName = $this->c['pronsPhraseVoiceDownloader']->download($entity->getName());
                        $entity->setVoice($voiceName);
                    }
                } catch (\Exception $e) {
                    $json['status'] = 1;
                    $this->log->addWarning(sprintf("Can not save this pron properly, name:%s, info:%s", $entity->getName(), $e->getMessage()));
                }
                break;
        }
        $this->em->flush();

        $json['data'] = $entity->toArray($this->c);
        return new JsonResponse($json);
    }

    public function newPronAction()
    {
        return $this->render('web/prons/newPron.html.twig');
    }

    public function addNewAction()
    {
        $json = array('status' => 0, 'errorWords' => '');
        $key = trim($this->request->request->get('key'));

        $words = preg_split("|[\r\n]+|i", $key);
        if (empty($words)) {
            return new JsonResponse($json);
        }
        $errorProns = array();
        $successProns = array();
        $updateProns = array();
        foreach ($words as $word) {
            $entity =$this->em->getRepository('Eng:PronEntity')->findOneBy(array('name' => $word));
            if ($entity === null) {
                $entity = new PronEntity();
                $entity->setName($word);
                $entity->setCreateTime(new \Datetime('now'));
            }
            try {
                if (str_word_count($entity->getName(), 0) == 1) {
                    $mean = $this->c['wordMeanDownloader']->download($entity->getName());
                    $entity->setMeans(implode("\n", $mean->getMeaning()));
                    $entity->setPronunciation($mean->getPhonetic());
                    $voiceName = $this->c['pronsWordVoiceDownloader']->download($entity->getName());
                } else {
                    $entity->setMeans('NONE');
                    $entity->setPronunciation('NONE');
                    $voiceName = $this->c['pronsPhraseVoiceDownloader']->download($entity->getName());
                }
                $entity->setVoice($voiceName);
                $entity->setStatus(PronEntity::NEWONE);
            } catch (\Exception $e) {
                $this->log->addWarning(sprintf("Can not find this sentence:%s, info:%s", $entity->getName(), $e->getMessage()));
                $errorProns[] = $entity->getName();
                continue;
            }
            if ($entity->getId() !== null) {
                $updateProns[] = $entity->getName();
            } else {
                $successProns[] = $entity->getName();
            }
            $this->em->persist($entity);
            $this->em->flush();
        }

        $message = array();
        if (!empty($successProns)) {
            $message[] = 'new prons: '.count($successProns);
        }
        if (!empty($updateProns)) {
            $message[] = 'update prons: '.count($updateProns);
        }
        if (!empty($errorProns)) {
            if (empty($successProns) && empty($updateProns)) {
                $json['status'] = 1;
            }
            $message[] = 'error prons: '.count($errorProns);
            $json['errorWords'] = implode("\n", $errorProns);
        }
        $json['message'] = implode(', ', $message);
        return new JsonResponse($json);
    }

    public function searchAction()
    {
        $key = $this->request->request->get('key');
        $pageSize = 30;
        $currentPage = $this->request->query->get('page', 1);
        $query = $this->em->createQuery("SELECT w FROM Eng:PronEntity w WHERE w.name LIKE :name");
        $query->setParameter('name', '%'.$key.'%');
        $paginator = new Paginator($query, $currentPage, $pageSize);

        return $this->render('web/prons/list.html.twig', array('paginator' => $paginator, 'status' => PronEntity::ALL, 'statusString' => $this->getStatusString()));
    }

    public function voiceAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository('Eng:PronEntity')->findOneBy(array('id' => $id));
        if ($entity === null) {
            throw new EngNotFoundException("can not find this pronciations");
        }

        if (str_word_count($entity->getName(), 0) == 1) {
            $downloader = $this->c['pronsWordVoiceDownloader'];
            $startVendorName = substr($entity->getVoice(), 0, strpos($entity->getVoice(), '/'));
        } else {
            $downloader = $this->c['pronsPhraseVoiceDownloader'];
            $voiceArray = explode('/', $entity->getVoice());
            $startVendorName = isset($voiceArray[1]) ? $voiceArray[1] : '';
        }
        $voiceName = false;
        $vendorName = $startVendorName;
        do {
            $vendor = $downloader->searchNextVendor($vendorName);
            $vendorName = $vendor->getVendor();
            if ($vendor->isMe($startVendorName)) {
                break;
            }
            $voiceName = $downloader->downloadByVendor($vendor, $entity->getName(), true);
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
}
