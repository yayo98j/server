<?php

declare(strict_types=1);
/**
 * @copyright 2021 Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
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
namespace OC\Core\Controller;

use OC\Contacts\ContactsMenu\Manager;
use OCA\Core\ResponseDefinitions;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\Contacts\ContactsMenu\IEntry;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\Share\IShare;

/**
 * @psalm-import-type CoreContactsAction from ResponseDefinitions
 */
class HoverCardController extends \OCP\AppFramework\OCSController {
	private Manager $manager;
	private IUserSession $userSession;

	public function __construct(IRequest $request, IUserSession $userSession, Manager $manager) {
		parent::__construct('core', $request);
		$this->userSession = $userSession;
		$this->manager = $manager;
	}

	/**
	 * @NoAdminRequired
	 *
	 * Get the user details for a hovercard
	 *
	 * @param string $userId ID of the user
	 * @return DataResponse<Http::STATUS_OK, array{userId: string, displayName: string, actions: CoreContactsAction[]}, array{}>|DataResponse<Http::STATUS_NOT_FOUND, \stdClass::class, array{}>
	 *
	 * 200: User details returned
	 * 404: User not found
	 */
	public function getUser(string $userId): DataResponse {
		$contact = $this->manager->findOne($this->userSession->getUser(), IShare::TYPE_USER, $userId);

		if (!$contact) {
			return new DataResponse(\stdClass::class, Http::STATUS_NOT_FOUND);
		}

		$data = $this->entryToArray($contact);

		$actions = $data['actions'];
		if ($data['topAction']) {
			array_unshift($actions, $data['topAction']);
		}

		return new DataResponse([
			'userId' => $userId,
			'displayName' => $contact->getFullName(),
			'actions' => $actions,
		]);
	}

	protected function entryToArray(IEntry $entry): array {
		return json_decode(json_encode($entry), true);
	}
}
