<?php

namespace Eng\Core\Command\Util;

use Eng\Core\Command\EngCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Eng\Core\Exception\EngRuntimeException;
use Eng\Core\Repository\Entity\WordsEntity;
use Eng\Core\Repository\Entity\PhrasesEntity;
use Eng\Core\Repository\Entity\PronEntity;

class VoiceManager extends EngCommand
{
    private $output;

    protected function configure()
    {
        $this->setName('util:voice-manager')
            ->setDescription('Manage voices of words,phrases,prons.')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'update | rebuild'
            )
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'all | words | phrases | prons'
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Specify the name you want to deal with, if no name specified, deal with all records'
            )
            ->addArgument(
                'user_id',
                InputArgument::OPTIONAL,
                'Only effect this user_id, if no user_id specifed, all users are effected!'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Begin running util:voice-manager command');
        $type = $input->getArgument('type');
        $name = $input->getArgument('name');

        $this->output = $output;
        $user_id = $input->getArgument('user_id');
        $action = $input->getArgument('action');
        $force = $action == 'rebuild';
        $isRun = false;
        if ($type == 'words' || $type == 'all') {
            $isRun = true;
            $this->downloadWordsVoice($name, $force);
        }
        if ($type == 'phrases' || $type == 'all') {
            $isRun = true;
            $this->downloadPhrasesVoice($name, $force);
        }
        if ($type == 'prons' || $type == 'all') {
            $isRun = true;
            $this->downloadPronsVoice($name, $force);
        }
        if (!$isRun) {
            throw new EngRuntimeException("Invalid arguments!");
        }
        $output->writeln('End running util:voice-manager command');
        return null;
    }

    private function updateWord(WordsEntity $entity)
    {
        $voiceName = $this->c['wordVoiceDownloader']->download($entity->getName());
        $entity->setVoice($voiceName);
        $this->em->merge($entity);
        $this->em->flush();
        $this->output->writeln(sprintf("word [%s] is updated.", $entity->getName()));
    }

    private function downloadWordsVoice($name, $force)
    {
        $params = array();
        if ($force) {
            $sql = "SELECT * FROM words WHERE 1 = 1";
        } else {
            $sql = "SELECT * FROM words WHERE (voice IS NULL or voice = '')";
        }
        if ($name != null) {
            $sql .= ' AND name = :name';
            $params['name'] = $name;
        }
		$sql .= " AND voice not like '%/%'";
        $handle = $this->db->executeQuery($sql, $params);
        while ($row = $handle->fetch()) {
            $entity = new WordsEntity();
            $entity->fromArray($row);
            try {
                $this->updateWord($entity, $force);
            } catch (\Exception $e) {
                $this->output->writeln(sprintf("word [%s] update failed, error info: %s", $entity->getName(), $e->getMessage()));
            }
        }
    }

    private function updatePhrase(PhrasesEntity $entity)
    {
        $voiceName = $this->c['phraseVoiceDownloader']->download($entity->getName());
        $entity->setVoice($voiceName);
        $this->em->merge($entity);
        $this->em->flush();

        $this->output->writeln(sprintf("phrase [%s] is updated.", $entity->getName()));
    }

    private function downloadPhrasesVoice($name, $force)
    {
        $params = array();
        if ($force) {
            $sql = "SELECT * FROM phrases WHERE 1 = 1";
        } else {
            $sql = "SELECT * FROM phrases WHERE (voice IS NULL or voice = '')";
        }
        if ($name != null) {
            $sql .= ' AND name = :name';
            $params['name'] = $name;
        }
		$sql .= " AND voice not like '%/%'";
        $handle = $this->db->executeQuery($sql, $params);
        while ($row = $handle->fetch()) {
            $entity = new PhrasesEntity();
            $entity->fromArray($row);
            try {
                $this->updatePhrase($entity, $force);
            } catch (\Exception $e) {
                $this->output->writeln(sprintf("phrase [%s] update failed, error info: %s", $entity->getName(), $e->getMessage()));
            }
        }
    }

    private function updatePron(PronEntity $entity)
    {
        if (str_word_count($entity->getName(), 0) == 1) {
            $voiceName = $this->c['pronsWordVoiceDownloader']->download($entity->getName());
        } else {
            $entity->setMeans('NONE');
            $entity->setPronunciation('NONE');
            $voiceName = $this->c['pronsPhraseVoiceDownloader']->download($entity->getName());
        }
        $entity->setVoice($voiceName);
        $this->em->merge($entity);
        $this->em->flush();
        $this->output->writeln(sprintf("pron [%s] is updated.", $entity->getName()));
    }

    private function downloadPronsVoice($name, $force)
    {
        $params = array();
        if ($force) {
            $sql = "SELECT * FROM pronunciation WHERE 1 = 1";
        } else {
            $sql = "SELECT * FROM pronunciation WHERE (voice IS NULL or voice = '')";
        }
        if ($name != null) {
            $sql .= ' AND name = :name';
            $params['name'] = $name;
        }
		$sql .= " AND voice not like '%/%'";
        $handle = $this->db->executeQuery($sql, $params);
        while ($row = $handle->fetch()) {
            $entity = new PronEntity();
            $entity->fromArray($row);
            try {
                $this->updatePron($entity, $force);
            } catch (\Exception $e) {
                $this->output->writeln(sprintf("pron [%s] update failed, error info: %s", $entity->getName(), $e->getMessage()));
            }
        }
    }
}
