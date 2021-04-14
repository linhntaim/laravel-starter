<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils\HandledFiles\Filer;

use App\Exceptions\AppException;
use App\Utils\HandledFiles\File;
use App\Utils\HandledFiles\Helper;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use App\Vendors\Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

/**
 * Class ChunkedFiler
 * @package App\Utils\HandledFiles\Filer
 * @method PrivateStorage getOriginStorage()
 * @method ChunkedFiler fromExisted($file, $toDirectory = null, $keepOriginalName = true)
 */
class ChunkedFiler extends Filer
{
    const CHUNK_FILE_NAME = 'chunk';
    const CHUNK_FOLDER_NAME = 'chunks';

    protected $chunksId;
    protected $chunksRelativeDirectory;
    protected $chunksTotal;
    protected $chunksExtension;
    protected $chunksJoined;

    protected $chunkIndex;

    /**
     * @var ChunkedFiler
     */
    protected $joinedFiler;

    /**
     * @param string $chunksId
     * @param int $chunksTotal
     * @param UploadedFile|File|string $chunkFile
     * @param int $chunkIndex
     * @return ChunkedFiler
     * @throws
     */
    public function fromChunk($chunksId, $chunksTotal, $chunkFile, $chunkIndex = 0)
    {
        $this->chunksId = $chunksId;
        $this->chunksRelativeDirectory = static::generateChunksRelativeDirectory($chunksId);
        $this->chunksTotal = $chunksTotal;
        $this->chunksJoined = false;

        $this->chunkIndex = $chunkIndex;
        $this->fromExisted($chunkFile, false, false);
        if ($this->isFirstChunk()) {
            $this->chunksExtension = $this->getOriginStorage()->getExtension();
        } else {
            $firstChunkFileBaseName = $this->getOriginStorage()->first(function ($file) {
                return Str::startsWith(Helper::changeToPath($file), Helper::concatPath(
                    $this->chunksRelativeDirectory,
                    $this->getChunkFileNameByIndex()
                ));
            }, $this->chunksRelativeDirectory);
            if (empty($firstChunkFileBaseName)) {
                $this->removeChunkDirectory();
                throw new AppException('Chunks were failed');
            }
            $this->chunksExtension = pathinfo($firstChunkFileBaseName, PATHINFO_EXTENSION);
        }
        $this->moveTo($this->chunksRelativeDirectory, [
            'name' => $this->getChunkFileNameByIndex($this->chunkIndex),
            'extension' => $this->chunksExtension,
        ]);
        return $this;
    }

    public function fromChunksId($chunksId)
    {
        $this->chunksId = $chunksId;
        $this->chunksRelativeDirectory = static::generateChunksRelativeDirectory($chunksId);
        if (!$this->getOriginStorage()->exists($this->chunksRelativeDirectory)) {
            throw new AppException(sprintf('Chunks ID [%s] were not existed', $this->chunksRelativeDirectory));
        }
        $chunkFiles = $this->getOriginStorage()->find(function ($file) {
            return Str::startsWith(Helper::changeToPath($file), Helper::concatPath(
                $this->chunksRelativeDirectory,
                $this->getChunkFileNameByIndex('')
            ));
        }, $this->chunksRelativeDirectory);
        $this->chunksTotal = $chunkFiles->count();
        if ($this->chunksTotal <= 0) {
            throw new AppException('Chunks were not existed');
        }
        $this->chunksJoined = false;
        $this->chunksExtension = pathinfo($chunkFiles->first(), PATHINFO_EXTENSION);
        return $this;
    }

    public function fromChunksIdCompleted($chunksId)
    {
        $this->makeOriginalStorage();
        $this->chunksId = $chunksId;
        $this->chunksRelativeDirectory = static::generateChunksRelativeDirectory($chunksId);
        $originStorage = $this->getOriginStorage();
        if (!$originStorage->exists($this->chunksRelativeDirectory)) {
            throw new AppException(sprintf('Chunks ID [%s] were not existed', $this->chunksRelativeDirectory));
        }
        $chunkFile = $originStorage->first(function ($file) {
            return Str::startsWith(Helper::changeToPath($file), Helper::concatPath(
                    $this->chunksRelativeDirectory,
                    static::CHUNK_FILE_NAME . '.'
                )) || Helper::changeToPath($file) == Helper::concatPath(
                    $this->chunksRelativeDirectory,
                    static::CHUNK_FILE_NAME
                );
        }, $this->chunksRelativeDirectory);
        if (empty($chunkFile)) {
            throw new AppException('Chunk was not existed');
        }
        $originStorage->setRelativePath(Helper::changeToPath($chunkFile));
        $this->moveTo(false, false);
        $this->removeChunkDirectory();
        return $this;
    }

    public function getChunksId()
    {
        return $this->chunksId;
    }

    public function joined()
    {
        return $this->chunksJoined;
    }

    public function isFirstChunk()
    {
        return 0 == $this->chunkIndex;
    }

    protected function getChunkFileNameByIndex($chunkIndex = 0)
    {
        return sprintf('%s_%s', static::CHUNK_FILE_NAME, $chunkIndex);
    }

    protected function getChunkFileBaseNameByIndex($chunkIndex = 0)
    {
        return Helper::nameWithExtension($this->getChunkFileNameByIndex($chunkIndex), $this->chunksExtension);
    }

    protected function getChunkFileBaseName()
    {
        return Helper::nameWithExtension(static::CHUNK_FILE_NAME, $this->chunksExtension);
    }

    protected function getChunkFileRelativePathByIndex($chunkIndex = 0)
    {
        return Helper::concatPath($this->chunksRelativeDirectory, $this->getChunkFileBaseNameByIndex($chunkIndex));
    }

    protected function getChunkFileRelativePath()
    {
        return Helper::concatPath($this->chunksRelativeDirectory, $this->getChunkFileBaseName());
    }

    protected function startJoining()
    {
        $this->joinedFiler = (new ChunkedFiler())
            ->fromExisted(Helper::concatPath(
                $this->getOriginStorage()->getRootPath(),
                $this->getChunkFileRelativePathByIndex()
            ))
            ->copyTo($this->chunksRelativeDirectory, static::CHUNK_FILE_NAME);
        return $this;
    }

    protected function tryJoining()
    {
        if ($this->chunksTotal > 1) {
            $this->joinedFiler->fEnableBinaryHandling()
                ->fStartAppending();
            foreach (range(1, $this->chunksTotal - 1) as $chunkIndex) {
                $chunkedFiler = (new ChunkedFiler())
                    ->fromExisted(Helper::concatPath(
                        $this->getOriginStorage()->getRootPath(),
                        $this->getChunkFileRelativePathByIndex($chunkIndex)
                    ))
                    ->fEnableBinaryHandling()
                    ->fStartReading();
                $this->joinedFiler->fWrite($chunkedFiler->fRead());
            }
            $this->joinedFiler->fEndWriting();
        }
        return $this;
    }

    protected function removeChunkDirectory()
    {
        $this->getOriginStorage()->deleteRelativeDirectory($this->chunksRelativeDirectory);
        return $this;
    }

    protected function canJoin()
    {
        for ($i = $this->chunksTotal - 1; $i >= 0; --$i) {
            if ($this->getOriginStorage()->exists($this->getChunkFileRelativePathByIndex($i))) continue;
            return false;
        }
        return true;
    }

    public function join()
    {
        if ($this->canJoin()) {
            $this->startJoining()->tryJoining();
            $this->chunksJoined = true;
        }

        return $this;
    }

    public static function generateChunksRelativeDirectory($chunksId)
    {
        return Helper::concatPath(static::CHUNK_FOLDER_NAME, $chunksId);
    }

    public static function generateChunksId()
    {
        $chunksId = Str::uuid();
        $chunksRelativeDirectory = static::generateChunksRelativeDirectory($chunksId);
        $privateStorage = new PrivateStorage();
        if ($privateStorage->exists($chunksRelativeDirectory)) { // prevent duplicate
            return static::generateChunksId();
        }
        $privateStorage->makeDirectory($chunksRelativeDirectory);
        return $chunksId;
    }
}