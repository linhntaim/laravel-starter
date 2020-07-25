<?php

namespace App\Utils\HandledFiles\Filer;

use App\Utils\HandledFiles\File;
use App\Utils\HandledFiles\Helper;
use App\Utils\HandledFiles\Storage\PrivateStorage;
use App\Utils\StringHelper;
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
     * @var Filer
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
        $this->chunksTotal = $chunksTotal;
        $this->chunksRelativeDirectory = static::generateChunksRelativeDirectory($chunksId);
        $this->chunksJoined = false;

        $this->chunkIndex = $chunkIndex;
        $this->fromExisted($chunkFile, $this->chunksRelativeDirectory);
        $this->chunksExtension = $this->isFirstChunk() ?
            $this->getOriginStorage()->getExtension()
            : (new ChunkedFiler())->fromExisted(Helper::concatPath(
                $this->getOriginStorage()->getRootPath(),
                $this->getChunkFileRelativePathByIndex()
            ))->getOriginStorage()->getExtension();
        return $this->moveTo($this->chunksRelativeDirectory, $this->getChunkFileBaseNameByIndex($this->chunkIndex));
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
        return sprintf('%s.%s', static::CHUNK_FILE_NAME, $chunkIndex);
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

    protected function startJoining()
    {
        $this->joinedFiler = (new Filer())
            ->fromExisted(Helper::concatPath(
                $this->getOriginStorage()->getRootPath(),
                $this->getChunkFileRelativePathByIndex()
            ))
            ->copyTo(null, $this->getChunkFileBaseName());
        return $this;
    }

    protected function tryJoining()
    {
        if ($this->chunksTotal > 1) {
            $this->joinedFiler->fEnableBinaryHandling()
                ->fStartAppending();
            foreach (range(1, $this->chunksTotal - 1) as $chunkIndex) {
                $chunkedFiler = (new Filer())
                    ->fromExisted(Helper::concatPath(
                        $this->getOriginStorage()->getRootPath(),
                        $this->getChunkFileRelativePathByIndex()
                    ))
                    ->fEnableBinaryHandling()
                    ->fStartReading();
                $this->joinedFiler->fWrite($chunkedFiler->fRead());
                $chunkedFiler->fEndReading();
            }
            $this->joinedFiler->fEndWriting();
        }
        return $this;
    }

    protected function completeJoining()
    {
        return $this->publishJoinedFile()->removeChunkDirectory();
    }

    protected function publishJoinedFile()
    {
        $this->joinedFiler->moveToPublic(false, false);
        return $this;
    }

    protected function removeChunkDirectory()
    {
        $this->getOriginStorage()->deleteRelativeDirectory();
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
            $this->startJoining()->tryJoining()->completeJoining();
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
        $chunksId = StringHelper::uuid();
        $chunksRelativeDirectory = static::generateChunksRelativeDirectory($chunksId);
        if ((new PrivateStorage())->exists($chunksRelativeDirectory)) { // prevent duplicate
            return static::generateChunksId();
        }
        return $chunksId;
    }
}