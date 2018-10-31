<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\Utility;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\View\TemplateView;

class VariableReplacer
{

    /**
     * @param string $template
     * @param Configuration $configuration
     * @return string
     */
    public static function replace(string $template, Configuration $configuration): string
    {
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        fwrite($temp, $template);

        $standaloneView = GeneralUtility::makeInstance(TemplateView::class);
        $standaloneView->assign('configuration', $configuration);
        $standaloneView->getTemplatePaths()->setTemplatePathAndFilename($path);

        $result = $standaloneView->render();

        fclose($temp);

        return $result;
    }
}