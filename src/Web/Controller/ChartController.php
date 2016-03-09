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
class ChartController extends BaseController
{
    public function words()
    {
        return $this->render('web/chart/words.html.twig');
    }

    public function phrases()
    {
        return $this->render('web/chart/phrases.html.twig');
    }

    public function ajax()
    {
        $type = $this->request->request->get('type', 'words');
        $interval = $this->request->request->get('interval', 'day');

        $cols = array(
            ['type' => 'string', 'label' => 'Date'],
            ['type' => 'number', 'label' => 'Easy'],
            ['type' => 'string', 'role' => 'tooltip', 'p' => ['html' => true]],
            ['type' => 'number', 'label' => 'Medium'],
            ['type' => 'string', 'role' => 'tooltip', 'p' => ['html' => true]],
            ['type' => 'number', 'label' => 'Difficult'],
            ['type' => 'string', 'role' => 'tooltip', 'p' => ['html' => true]],
        );
        $rows = [];
        $tooltipFormat = '<div class="chart-tooltip">'
            . '<span>Date:</span> %s<br/>'
            . '<span>Status:</span> %s<br/>'
            . '<span>Total:</span> %d<br/>'
            . '<span>Success:</span>%d <br/>'
            . '<span>Fail:</span> %d'
            . '</div>';

        $learningHistoryRepository = $this->em->getRepository('Eng:LearningHistoryEntity');
        $results = $learningHistoryRepository->getHistory($type, $interval);
        if ($results) {
            $resultsDays = array();
            $statusTrans = array(WordsEntity::EASY => 'Easy', WordsEntity::MEDIUM => 'Medium', WordsEntity::DIFFICULT => 'Difficult');
            foreach ($results as $row) {
                $dateString = $row['date']->format('Y-m-d');
                $resultsDays[$dateString][$row['status']]['value'] = $row['success'] + $row['fail'];
                $resultsDays[$dateString][$row['status']]['tooltip'] = sprintf($tooltipFormat, $dateString, $statusTrans[$row['status']], $row['success'] + $row['fail'], $row['success'], $row['fail']);
            }
            foreach ($resultsDays as $date => $row) {
                if (!isset($row[WordsEntity::EASY])) {
                    $row[WordsEntity::EASY]['value'] = 0;
                    $row[WordsEntity::EASY]['tooltip'] = sprintf($tooltipFormat, $date, $statusTrans[WordsEntity::EASY], 0, 0, 0);
                }
                if (!isset($row[WordsEntity::MEDIUM])) {
                    $row[WordsEntity::MEDIUM]['value'] = 0;
                    $row[WordsEntity::MEDIUM]['tooltip'] = sprintf($tooltipFormat, $date, $statusTrans[WordsEntity::MEDIUM], 0, 0, 0);
                }
                if (!isset($row[WordsEntity::DIFFICULT])) {
                    $row[WordsEntity::DIFFICULT]['value'] = 0;
                    $row[WordsEntity::DIFFICULT]['tooltip'] = sprintf($tooltipFormat, $date, $statusTrans[WordsEntity::DIFFICULT], 0, 0, 0);
                }
                $rows[] = [
                    'c' => [
                        ['v' => $date],
                        ['v' => $row[WordsEntity::EASY]['value']],
                        ['v' => $row[WordsEntity::EASY]['tooltip']],
                        ['v' => $row[WordsEntity::MEDIUM]['value']],
                        ['v' => $row[WordsEntity::MEDIUM]['tooltip']],
                        ['v' => $row[WordsEntity::DIFFICULT]['value']],
                        ['v' => $row[WordsEntity::DIFFICULT]['tooltip']],
                    ]
                ];
            }
        }

        $json = ['cols' => $cols, 'rows' => $rows];
        return new JsonResponse($json);

    }
}
