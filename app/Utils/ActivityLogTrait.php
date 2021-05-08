<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Utils;

use App\ModelRepositories\ActivityLogRepository;
use App\Models\ActivityLog;
use App\Models\Base\IActivityLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait ActivityLogTrait
{
    /**
     * @param string $action
     * @param Model|int|null $actedBy
     * @param array $payload
     */
    protected function logAction(string $action, $actedBy = null, $payload = [])
    {
        if (!ConfigHelper::get('activity_log_enabled')) {
            return;
        }

        if (is_null($actedBy)) {
            $actedBy = request()->user();
        }
        if ($actedBy) {
            try {
                (new ActivityLogRepository())->createWithAction($action, $actedBy, $payload);
            }
            catch (Throwable $exception) {
            }
        }
    }

    /**
     * @param string $action
     * @param string $modelClass
     * @param Model|int|null $actedBy
     * @param array $payload
     */
    protected function logActionModel(string $action, string $modelClass, $actedBy = null, $payload = [])
    {
        $this->logAction($action, $actedBy, array_merge([
            'model' => $modelClass,
        ], $payload));
    }

    /**
     * @param string $modelClass
     * @param array $params
     * @param Model|int|null $actedBy
     */
    protected function logActionModelList(string $modelClass, array $params, $actedBy = null)
    {
        $this->logActionModel(ActivityLog::ACTION_MODEL_LIST, $modelClass, $actedBy, [
            'params' => $params,
        ]);
    }

    /**
     * @param string $modelClass
     * @param array $params
     * @param Model|int|null $actedBy
     */
    protected function logActionModelExport(string $modelClass, array $params, $actedBy = null)
    {
        $this->logActionModel(ActivityLog::ACTION_MODEL_EXPORT, $modelClass, $actedBy, [
            'params' => $params,
        ]);
    }

    /**
     * @param string $modelClass
     * @param array $params
     * @param Model|int|null $actedBy
     */
    protected function logActionModelImport(string $modelClass, array $params, $actedBy = null)
    {
        $this->logActionModel(ActivityLog::ACTION_MODEL_IMPORT, $modelClass, $actedBy, [
            'params' => $params,
        ]);
    }

    /**
     * @param string $modelClass
     * @param IActivityLog|Model $createdModel
     * @param Model|int|null $actedBy
     */
    protected function logActionModelCreate(string $modelClass, $createdModel, $actedBy = null)
    {
        $this->logActionModel(ActivityLog::ACTION_MODEL_CREATE, $modelClass, $actedBy, [
            'created' => $this->logModelActivity($createdModel),
        ]);
    }

    /**
     * @param string $modelClass
     * @param IActivityLog|Model $oldModel
     * @param IActivityLog|Model $editedModel
     * @param Model|int|null $actedBy
     */
    protected function logActionModelEdit(string $modelClass, $oldModel, $editedModel, $actedBy = null)
    {
        $this->logActionModel(ActivityLog::ACTION_MODEL_EDIT, $modelClass, $actedBy, [
            'old' => $this->logModelActivity($oldModel),
            'edited' => $this->logModelActivity($editedModel),
        ]);
    }

    /**
     * @param string $modelClass
     * @param IActivityLog[]|Collection|Model[]|array $models
     * @param Model|int|null $actedBy
     */
    protected function logActionModelDelete(string $modelClass, $models, $actedBy = null)
    {
        $this->logActionModel(ActivityLog::ACTION_MODEL_DELETE, $modelClass, $actedBy, [
            'deleted' => (function () use ($models) {
                $deleted = [];
                foreach ($models as $model) {
                    $deleted[] = $this->logModelActivity($model);
                }
                return $deleted;
            })(),
        ]);
    }

    /**
     * @param IActivityLog|Model|array $model
     * @return array
     */
    protected function logModelActivity($model)
    {
        return $model instanceof IActivityLog ? $model->toActivityLogArray()
            : ($model instanceof Model ? $model->toArray() : $model);
    }
}