<?php namespace Album\Infrastructure\Persistence\Album;

use Album\Domain\Album\Album;
use Album\Domain\Album\AlbumNotFoundException;
use Album\Domain\Album\AlbumRepository;
use Album\Infrastructure\Persistence\DMLPersistence;
use Album\Models\AlbumModel;

class SQLAlbumRepository implements AlbumRepository
{
	use DMLPersistence;

	/** @var AlbumModel */
	protected $model;

	public function __construct(AlbumModel $model)
	{
		$this->model = $model;
	}

	public function findPaginatedData(string $keyword = ''): ?array
	{
		if ($keyword)
		{
			$this->model
				 ->builder()
				 ->groupStart()
					 ->like('artist', $keyword)
					 ->orLike('title', $keyword)
				 ->groupEnd();
		}

		return $this->model->paginate(config('Album')->paginationPerPage);
	}

	public function findAlbumOfId(int $id): Album
	{
		$album = $this->model->find($id);
		if (! $album instanceof Album)
		{
			throw new AlbumNotFoundException();
		}

		return $album;
	}

	public function deleteOfId(int $id) : bool
	{
		$delete = $this->model->delete($id);
		if ($delete->connID->affected_rows === 0)
		{
			throw new AlbumNotFoundException();
		}

		return true;
	}
}
