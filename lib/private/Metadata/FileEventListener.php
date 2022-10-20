<?php

declare(strict_types=1);
/**
 * @copyright Copyright 2022 Carl Schwan <carl@carlschwan.eu>
 * @license AGPL-3.0-or-later
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Metadata;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Cache\CacheEntryRemovedEvent;
use OCP\Files\Events\Node\NodeWrittenEvent;
use OCP\Files\File;

/**
 * @template-implements IEventListener<CacheEntryRemovedEvent>
 * @template-implements IEventListener<NodeWrittenEvent>
 */
class FileEventListener implements IEventListener {
	private IMetadataManager $manager;

	public function __construct(
		IMetadataManager $manager
	) {
		$this->manager = $manager;
	}

	private function isCorrectPath(string $path): bool {
		// TODO make this more dynamic, we have the same issue in other places
		return !str_starts_with($path, 'appdata_') && !str_starts_with($path, 'files_versions/');
	}

	/**
	 * @param NodeWrittenEvent|CacheEntryRemovedEvent $event
	 */
	public function handle(Event $event): void {
		if ($event instanceof CacheEntryRemovedEvent) {
			if ($event->getStorage()->is_dir($event->getPath())) {
				return;
			}

			if ($this->isCorrectPath($event->getPath())) {
				$this->manager->clearMetadata($event->getFileId());
			}
		}

		if ($event instanceof NodeWrittenEvent) {
			$node = $event->getNode();
			if ($node->getSize(false) <= 0) {
				return;
			}

			if (!$this->isCorrectPath($node->getPath())) {
				return;
			}

			if ($node instanceof File) {
				$this->manager->generateMetadata($node, false);
			}
		}
	}
}
