<?php

namespace OC\Core\Command\Preview;

use OC\Preview\Storage\Root;
use OC\SystemConfig;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\Files\IAppData;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IConfig;
use OCP\IPreview;
use Symfony\Component\Console\Command\Command;
use OCP\IDBConnection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Regenerate extends Command {
	private IDBConnection $connection;
	private IAppData $appData;
	private IPreview $preview;
	private IRootFolder $rootFolder;
	private IConfig $config;

	public function __construct(IDBConnection $connection, IPreview $preview, IRootFolder $rootFolder, IConfig $config) {
		parent::__construct();
		$this->connection = $connection;
		$this->appData = new Root(
			\OC::$server->get(IRootFolder::class),
			\OC::$server->get(SystemConfig::class)
		);
		$this->preview = $preview;
		$this->rootFolder = $rootFolder;
		$this->config = $config;
	}

	protected function configure() {
		$this
			->setName('preview:regenerate');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$lastFileId = $this->config->getAppValue('preview', 'last_file_id', '<ENTER FILE ID HERE>');
		$limit = 100;

		for ($i = 0; $i < 100; $i++) {
			$start = microtime(true);
			if ($lastFileId !== 'none') {
				$result = $this->connection->executeQuery('SELECT fileid FROM *PREFIX*filecache WHERE path NOT LIKE "appdata%" AND fileid > ? AND mimetype in (SELECT id from *PREFIX*mimetypes WHERE mimetype LIKE "image%") ORDER BY fileid ASC LIMIT ?', [(int)$lastFileId, $limit], [IQueryBuilder::PARAM_INT, IQueryBuilder::PARAM_INT]);
			} else {
				$result = $this->connection->executeQuery('SELECT fileid FROM *PREFIX*filecache WHERE path NOT LIKE "appdata%" AND mimetype in (SELECT id from *PREFIX*mimetypes WHERE mimetype LIKE "image%") ORDER BY fileid ASC LIMIT ?', [$limit], [IQueryBuilder::PARAM_INT]);
			}
			$end = microtime(true);
			$output->writeln('Found list of files to delete in ' . ($end - $start) * 1000 . ' ms');
			$processed = false;
			while ($row = $result->fetch()) {
				$processed = true;
				$fileId = $row['fileid'];
				$lastFileId = $fileId;
				try {
					$start = microtime(true);
					$folder = $this->appData->getFolder($fileId);
					$folder->delete();
					$end = microtime(true);
					$output->writeln('Deleting file ' . $fileId . ' in ' . ($end - $start) * 1000 . ' ms');
				} catch (NotFoundException $e) {
					// nothing to do here, no preview
					$output->writeln('No preview found for file ' . $fileId);
					continue;
				} catch (\Exception $e) {
					$output->writeln('Exception: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
				}
			}
			$result->closeCursor();
			if (!$processed) {
				$output->writeln('Finished');
				break;
			}
		}
		$this->config->setAppValue('preview', 'last_file_id', (string)$lastFileId);

		return 0;
	}
}
