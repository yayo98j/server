<?php
/**
 * @copyright 2016 Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 * @author Kate Döen <kate.doeen@nextcloud.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;

/**
 * Class FileDisplayResponse
 *
 * @since 11.0.0
 * @template S of int
 * @template C of string
 * @template H of array<string, mixed>
 * @template-extends Response<S, array<string, mixed>>
 */
class FileDisplayResponse extends Response implements ICallbackResponse {
	/** @var \OCP\Files\File|\OCP\Files\SimpleFS\ISimpleFile */
	private $file;

	/**
	 * FileDisplayResponse constructor.
	 *
	 * @param \OCP\Files\File|\OCP\Files\SimpleFS\ISimpleFile $file
	 * @param S $statusCode
	 * @param H $headers
	 * @param ?C $contentType
	 * @since 11.0.0
	 */
	public function __construct($file, $statusCode = Http::STATUS_OK,
								$headers = [], $contentType = null) {
		parent::__construct($statusCode, array_merge(['Content-Disposition' => 'inline; filename="' . rawurldecode($file->getName()) . '"'], $contentType !== null ? ["Content-Type" => $contentType] : [], $headers));

		$this->file = $file;

		$this->setETag($file->getEtag());
		$lastModified = new \DateTime();
		$lastModified->setTimestamp($file->getMTime());
		$this->setLastModified($lastModified);
	}

	/**
	 * @param IOutput $output
	 * @since 11.0.0
	 */
	public function callback(IOutput $output) {
		if ($output->getHttpResponseCode() !== Http::STATUS_NOT_MODIFIED) {
			$output->setHeader('Content-Length: ' . $this->file->getSize());
			$output->setOutput($this->file->getContent());
		}
	}
}
