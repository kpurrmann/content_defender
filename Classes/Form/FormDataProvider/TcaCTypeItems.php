<?php
namespace IchHabRecht\ContentDefender\Form\FormDataProvider;

use IchHabRecht\ContentDefender\BackendLayout\BackendLayoutConfiguration;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaCTypeItems implements FormDataProviderInterface
{
    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if ('tt_content' !== $result['tableName']) {
            return $result;
        }

        $pageId = !empty($result['effectivePid']) ? (int)$result['effectivePid'] : (int)$result['databaseRow']['pid'];
        $backendLayoutConfiguration = BackendLayoutConfiguration::createFromPageId($pageId);

        $colPos = (int)$result['databaseRow']['colPos'];
        $columnConfiguration = $backendLayoutConfiguration->getConfigurationByColPos($colPos);
        if (empty($columnConfiguration) || (empty($columnConfiguration['allowed.']) && empty($columnConfiguration['disallowed.']))) {
            return $result;
        }

        if (!empty($columnConfiguration['allowed.'])) {
            foreach ($columnConfiguration['allowed.'] as $field => $value) {
                if (empty($result['processedTca']['columns'][$field]['config']['items'])) {
                    continue;
                }

                $allowedValues = GeneralUtility::trimExplode(',', $value);
                $result['processedTca']['columns'][$field]['config']['items'] = array_filter(
                    $result['processedTca']['columns'][$field]['config']['items'],
                    function ($item) use ($allowedValues) {
                        return in_array($item[1], $allowedValues);
                    }
                );
            }
        }
        if (!empty($columnConfiguration['disallowed.'])) {
            foreach ($columnConfiguration['disallowed.'] as $field => $value) {
                if (empty($result['processedTca']['columns'][$field]['config']['items'])) {
                    continue;
                }

                $disAllowedValues = GeneralUtility::trimExplode(',', $value);
                $result['processedTca']['columns'][$field]['config']['items'] = array_filter(
                    $result['processedTca']['columns'][$field]['config']['items'],
                    function ($item) use ($disAllowedValues) {
                        return !in_array($item[1], $disAllowedValues);
                    }
                );
            }
        }

        return $result;
    }
}
