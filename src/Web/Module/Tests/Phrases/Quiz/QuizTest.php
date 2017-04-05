<?php

namespace Eng\Web\Module\Tests\Phrases\Quiz;

use Eng\Web\Module\Phrases\Quiz\Quiz;
use Eng\Core\Repository\Entity\PhrasesEntity;
use Eng\Core\Repository\Entity\PhrasesMarkerEntity;
use Eng\Core\Exception\EngRuntimeException;

class QuizTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }
    
    public function testDoKnowUpdate()
    {
        $phrasesMarkerEntity = new PhrasesMarkerEntity();
        $phrasesEntity = new PhrasesEntity();
        $phrasesEntity->setStatus(1);
        $phrasesMarkerEntity->setStatus(0);
        
        $quizModule = $this->getMockBuilder(Quiz::class)
                ->setConstructorArgs([$phrasesMarkerEntity])
                ->setMethods(['getNextStatusWhenSuccess'])->getMock();
        try {
            $quizModule->doKnowUpdate($phrasesEntity);
        } catch (EngRuntimeException $e) {
            // We expect it throw exception here.
             $this->assertTrue(true);
             return true;
        }
         $this->assertTrue(false);
    }
    
    public function testDoKnowUpdateSetValue()
    {
        $phrasesMarkerEntity = new PhrasesMarkerEntity();
        $phrasesMarkerEntity->setStatus(PhrasesEntity::MEDIUM);
        $success = 0;
        $phrasesMarkerEntity->setSuccess($success);
        
        $quizModule = $this->getMockBuilder(Quiz::class)
        ->setConstructorArgs([$phrasesMarkerEntity])
        ->setMethods(['getNextStatusWhenSuccess'])->getMock();
        
        $quizModule->expects($this->any())
             ->method('getNextStatusWhenSuccess')
             ->will($this->returnValue(PhrasesEntity::DIFFICULT));
        
        $phrasesEntity = new PhrasesEntity();
        $phrasesEntity->setStatus(PhrasesEntity::MEDIUM);
        
        $quizModule->doKnowUpdate($phrasesEntity);
        $this->assertEquals(PhrasesEntity::DIFFICULT, $phrasesEntity->getStatus());
        $this->assertEquals(0, $phrasesEntity->getSuccess());
        $this->assertEquals(0, $phrasesEntity->getFailure());
    }
    
    public function testDoKnowUpdateSetValueLessThan()
    {
        $phrasesMarkerEntity = new PhrasesMarkerEntity();
        $phrasesMarkerEntity->setStatus(PhrasesEntity::MEDIUM);
        $success = 0;
        $phrasesMarkerEntity->setSuccess($success);
        
        $quizModule = $this->getMockBuilder('Eng\Web\Module\Phrases\Quiz\Quiz')
        ->setConstructorArgs([$phrasesMarkerEntity])
        ->setMethods(['getNextStatusWhenSuccess'])->getMock();
        
        $quizModule->expects($this->any())
             ->method('getNextStatusWhenSuccess')
             ->will($this->returnValue(PhrasesEntity::MEDIUM));
        
        $phrasesEntity = new PhrasesEntity();
        $phrasesEntity->setStatus(PhrasesEntity::MEDIUM);
        $quizModule->doKnowUpdate($phrasesEntity);
        $this->assertEquals($success + 1, $phrasesEntity->getSuccess());
    }
    
    public function testDoKnowUpdateCountUp()
    {
        $phrasesMarkerEntity = new PhrasesMarkerEntity();
        $phrasesMarkerEntity->setSuccess(4);
        
        $quizModule = $this->getMockBuilder('Eng\Web\Module\Phrases\Quiz\Quiz')
        ->setConstructorArgs([$phrasesMarkerEntity])
        ->setMethods(null)->getMock();

        $phrasesEntity = new PhrasesEntity();
        $phrasesEntity->setSuccess(0);
        $quizModule->doKnowUpdate($phrasesEntity);
        $this->assertEquals(1, $phrasesEntity->getSuccess());
    }
}
