<?php
namespace IchHabRecht\ContentDefender\Tests\Functional\Hooks;

require_once __DIR__ . '/../AbstractFunctionalTestCase.php';

use IchHabRecht\ContentDefender\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Core\DataHandling\DataHandler;

class DatamapDataHandlerHookTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     */
    public function newRecordWithDisallowedCTypeIsNotSaved()
    {
        $datamap['tt_content']['NEW123'] = [
            'pid' => 2,
            'CType' => 'bullets',
            'colPos' => 0,
            'header' => 'Bullet List',
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start($datamap, []);
        $dataHandler->process_datamap();

        $this->assertEmpty($dataHandler->substNEWwithIDs);
    }

    /**
     * @test
     */
    public function newRecordWithDisallowedCTypeAfterRecordIsNotSaved()
    {
        $datamap['tt_content']['NEW123'] = [
            'pid' => -2,
            'CType' => 'bullets',
            'colPos' => 0,
            'header' => 'Bullet List',
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start($datamap, []);
        $dataHandler->process_datamap();

        $this->assertEmpty($dataHandler->substNEWwithIDs);
    }

    /**
     * @test
     */
    public function newRecordsWithMaxitemsCountAreNotSaved()
    {
        $datamap['tt_content'] = [
            'NEW123' => [
                'pid' => 3,
                'CType' => 'textmedia',
                'colPos' => 0,
                'header' => 'Text & Media 1',
            ],
            'NEW456' => [
                'pid' => 3,
                'CType' => 'textmedia',
                'colPos' => 0,
                'header' => 'Text & Media 2',
            ],
            'NEW789' => [
                'pid' => 3,
                'CType' => 'textmedia',
                'colPos' => 0,
                'header' => 'Text & Media 3',
            ],
            'NEW147' => [
                'pid' => 3,
                'CType' => 'textmedia',
                'colPos' => 0,
                'header' => 'Text & Media 4',
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start($datamap, []);
        $dataHandler->process_datamap();

        $count = $this->getDatabaseConnection()->getSelectCount('*', 'tt_content',  'pid=3 AND colPos=0');

        $this->assertSame(3, $count);
    }
}
