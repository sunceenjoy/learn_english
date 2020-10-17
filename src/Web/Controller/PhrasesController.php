<?php

namespace Eng\Web\Controller;

use Eng\Core\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Eng\Core\Exception\EngNotFoundException;
use Eng\Core\Exception\EngRuntimeException;
use \Eng\Core\Repository\Entity\PhrasesEntity;

/**
 * Description of words
 *
 * @author grantsun
 */
class PhrasesController extends BaseController
{
    private function getStatusString()
    {
        return array(
            PhrasesEntity::DIFFICULT => $this->t('difficult'),
            PhrasesEntity::NEWONE => $this->t('new'),
            PhrasesEntity::EASY => $this->t('easy'),
            PhrasesEntity::MEDIUM => $this->t('medium'),
            PhrasesEntity::RARE => $this->t('rare'),
        );
    }

    public function listAction()
    {
        $pageSize = 30;
        $currentPage = $this->request->query->get('page', 1);
        $status      = $this->request->query->get('status', PhrasesEntity::NEWONE);
        if ($status == PhrasesEntity::ALL) {
            $query = $this->em->createQuery("SELECT w FROM Eng:PhrasesEntity w");
        } else {
            $query = $this->em->createQuery("SELECT w FROM Eng:PhrasesEntity w WHERE w.status = :status");
            $query->setParameter('status', $status);
        }

        $paginator = new Paginator($query, $currentPage, $pageSize);

        return $this->render('web/phrases/list.html.twig', array('paginator' => $paginator, 'status' => $status, 'statusString' => $this->getStatusString()));
    }

    public function changeStatusAction()
    {
        $status = $this->request->query->get('status', PhrasesEntity::NEWONE);
        $phraseId = $this->request->query->get('phrase_id');

        $phrase = $this->em->getRepository('Eng:PhrasesEntity')->find($phraseId);

        if (!$phrase) {
            throw new EngNotFoundException("Can not find this phrase!");
        }

        $phrase->setStatus($status);
        $this->em->flush();

        $json = array('status' => 0);
        return new JsonResponse($json);
    }

    public function savePhraseAction()
    {
        $phraseId = $this->request->query->get('phrase_id');
        $key = $this->request->query->get('key');
        $value = $this->request->query->get('value');

        if ($key !== 'means' && $key !== 'name') {
            throw new EngRuntimeException("Invalid key type!");
        }

        $phrase = $this->em->getRepository('Eng:PhrasesEntity')->find($phraseId);

        if (!$phrase) {
            throw new EngNotFoundException("can not find this phrase");
        }

        $json = array('status' => 0);
        switch ($key) {
            case 'means':
                $phrase->setMeans($value);
                break;
            case 'name':
                $phrase->setName($value);
                try {
                    $voiceName = $this->container['phraseVoiceDownloader']->download($phrase->getName());
                    $phrase->setVoice($voiceName);
                } catch (\Exception $e) {
                    $json['status'] = 1;
                    $this->log->addWarning(sprintf("Can not find voice:%s, info:%s", $entity->getName(), $e->getMessage()));
                }
                break;
        }
        $this->em->flush();

        $json['data'] = $phrase->toArray($this->c);
        return new JsonResponse($json);
    }

    public function newPhraseAction()
    {
        return $this->render('web/phrases/newPhrase.html.twig');
    }

    public function addNewAction()
    {
        $json = array('status' => 0, 'errorPhrases' => '');
        $key = trim($this->request->request->get('key'));

        $phrases = preg_split("/[\r\n]+/", $key);
        if (empty($phrases)) {
            return new JsonResponse($json);
        }
        $errorPhrases = array();
        $successPhrases = array();
        $updatePhrases = array();
        foreach ($phrases as $phrase_line) {
            if (empty($phrase_line)) {
                continue;
            }
            $all = array();
            preg_match_all('|^([a-z\.\t,\'\\\?!\-’ /]+)(.*)$|i', $phrase_line, $all);
            if (!isset($all[1][0])) {
                continue;
            }
            $phrase = trim(str_replace('’', '\'', $all[1][0]));
            $phrase = str_replace('，', ',', $all[1][0]);
            $mean = isset($all[2][0]) ? $all[2][0] : '';

            $entity =$this->em->getRepository('Eng:PhrasesEntity')->findOneBy(array('name' => $phrase));
            if ($entity === null) {
                $entity = new PhrasesEntity();
                $entity->setName($phrase);
                $entity->setCreateTime(new \Datetime('now'));
            }
            try {
                $entity->setMeans($mean);
                $voiceName = $this->container['phraseVoiceDownloader']->download($entity->getName());
                $entity->setVoice($voiceName);
                $entity->setStatus(PhrasesEntity::NEWONE);
            } catch (\Exception $e) {
                $this->log->addWarning(sprintf("Can not find this phrase's voice:%s, info:%s", $entity->getName(), $e->getMessage()));
                $errorPhrases[] = $entity->getName();
                continue;
            }
            if ($entity->getId() !== null) {
                $updatePhrases[] = $entity->getName();
            } else {
                $successPhrases[] = $entity->getName();
            }
            $this->em->persist($entity);
            $this->em->flush();
        }

        $message = array();
        if (!empty($successPhrases)) {
            $message[] = 'new phrases: '.count($successPhrases);
        }
        if (!empty($updatePhrases)) {
            $message[] = 'update phrases: '.count($updatePhrases);
        }
        if (!empty($errorPhrases)) {
            if (empty($successPhrases) && empty($updatePhrases)) {
                $json['status'] = 1;
            }
            $message[] = 'error phrases: '.count($errorPhrases);
            $json['errorPhrases'] = implode("\n", $errorPhrases);
        }
        $json['message'] = implode(', ', $message);
        return new JsonResponse($json);
    }

    public function searchAction()
    {
        $key = $this->request->request->get('key');
        $pageSize = 30;
        $currentPage = $this->request->query->get('page', 1);
        $query = $this->em->createQuery("SELECT w FROM Eng:PhrasesEntity w WHERE w.name LIKE :name");
        $query->setParameter('name', '%'.$key.'%');
        $paginator = new Paginator($query, $currentPage, $pageSize);

        return $this->render('web/phrases/list.html.twig', array('paginator' => $paginator, 'status' => PhrasesEntity::ALL, 'statusString' => $this->getStatusString()));
    }

    public function voiceAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository('Eng:PhrasesEntity')->findOneBy(array('id' => $id));
        if ($entity === null) {
            throw new EngNotFoundException("can not find this phrase");
        }

        $downloader = $this->c['phraseVoiceDownloader'];
        $voiceArray = explode('/', $entity->getVoice());
        $startVendorName = isset($voiceArray[1]) ? $voiceArray[1] : '';
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
