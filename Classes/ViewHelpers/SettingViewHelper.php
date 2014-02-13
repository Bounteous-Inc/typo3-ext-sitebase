<?php
namespace Tx\Sitebase\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Ingo Renner (ingo@typo3.org)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception as ViewHelperException;


/**
 * ViewHelper to retrieve a value from the site settings
 *
 * # Example: Basic example
 * <code>
 * <site:setting key="{format.date.short}" />
 * </code>
 * <output>
 * This will retrieve the format defined for a short date representation
 * </output>
 *
 * @package TYPO3
 * @subpackage tx_sitebase
 */
class SettingViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Outputs the value for a given key in the site configuration section
	 *
	 * @param string $key
	 * @return string site setting
	 * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception when a given $key path does not exist
	 */
	public function render($key) {
		$value = '';

		$configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$siteSettings  = $configuration['site.'];

		$pathSegments = GeneralUtility::trimExplode('.', $key);
		$lastSegment  = array_pop($pathSegments);

		foreach ($pathSegments as $segment) {
			if (!array_key_exists($segment . '.', $siteSettings)) {
				throw new ViewHelperException('Key path "' . htmlspecialchars($key) . '" does not exist in site settings', 1376007385);
			}
			$siteSettings = $siteSettings[$segment . '.'];
		}

		if (array_key_exists($lastSegment, $siteSettings)) {
			$value = $siteSettings[$lastSegment];
		} else {
			throw new ViewHelperException('Key path "' . htmlspecialchars($key) . '" does not exist in site settings', 1376007385);
		}

		return $value;
	}

}

?>