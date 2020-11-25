<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\ModelRepositories;

use App\ModelRepositories\Base\ModelRepository;
use App\Models\DatabaseNotification;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DatabaseNotificationRepository
 * @package App\ModelRepositories
 * @property DatabaseNotification $model
 */
class DatabaseNotificationRepository extends ModelRepository
{
    public function modelClass()
    {
        return DatabaseNotification::class;
    }

    public function getByIdBelongedToNotifiable($id, Model $notifiable)
    {
        return $this->first(
            $this->query()
                ->where('id', $id)
                ->where('notifiable_type', get_class($notifiable))
                ->where('notifiable_id', $notifiable->getKey())
        );
    }

    protected function searchOn($query, array $search)
    {
        if (!empty($search['notifiable_type'])) {
            $query->where('notifiable_type', $search['notifiable_type']);
        }
        if (!empty($search['notifiable_id'])) {
            $query->where('notifiable_id', $search['notifiable_id']);
        }
        if (!empty($search['types'])) {
            $query->whereIn('type', $search['types']);
        }
        return parent::searchOn($query, $search);
    }

    public function markAsRead()
    {
        return $this->catch(function () {
            $this->model->markAsRead();
            return $this->model;
        });
    }
}
