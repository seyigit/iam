<?php

namespace NextDeveloper\IAM\Services\AbstractServices;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use NextDeveloper\IAM\Database\Models\IamBackend;
use NextDeveloper\IAM\Database\Filters\IamBackendQueryFilter;

use NextDeveloper\IAM\Events\IamBackend\IamBackendCreatedEvent;
use NextDeveloper\IAM\Events\IamBackend\IamBackendCreatingEvent;
use NextDeveloper\IAM\Events\IamBackend\IamBackendUpdatedEvent;
use NextDeveloper\IAM\Events\IamBackend\IamBackendUpdatingEvent;
use NextDeveloper\IAM\Events\IamBackend\IamBackendDeletedEvent;
use NextDeveloper\IAM\Events\IamBackend\IamBackendDeletingEvent;

/**
* This class is responsible from managing the data for IamBackend
*
* Class IamBackendService.
*
* @package NextDeveloper\IAM\Database\Models
*/
class AbstractIamBackendService {
    public static function get(IamBackendQueryFilter $filter = null, array $params = []) : Collection|LengthAwarePaginator {
        $enablePaginate = array_key_exists('paginate', $params);

        /**
        * Here we are adding null request since if filter is null, this means that this function is called from
        * non http application. This is actually not I think its a correct way to handle this problem but it's a workaround.
        *
        * Please let me know if you have any other idea about this; baris.bulut@nextdeveloper.com
        */
        if($filter == null)
            $filter = new IamBackendQueryFilter(new Request());

        $perPage = config('commons.pagination.per_page');

        if($perPage == null)
            $perPage = 20;

        if(array_key_exists('per_page', $params)) {
            $perPage = intval($params['per_page']);

            if($perPage == 0)
                $perPage = 20;
        }

        if(array_key_exists('orderBy', $params)) {
            $filter->orderBy($params['orderBy']);
        }

        $model = IamBackend::filter($filter);

        if($model && $enablePaginate)
            return $model->paginate($perPage);
        else
            return $model->get();

        if(!$model && $enablePaginate)
            return IamBackend::paginate($perPage);
        else
            return IamBackend::get();
    }

    public static function getAll() {
        return IamBackend::all();
    }

    /**
    * This method returns the model by looking at reference id
    *
    * @param $ref
    * @return mixed
    */
    public static function getByRef($ref) : ?IamBackend {
        return IamBackend::findByRef($ref);
    }

    /**
    * This method returns the model by lookint at its id
    *
    * @param $id
    * @return IamBackend|null
    */
    public static function getById($id) : ?IamBackend {
        return IamBackend::where('id', $id)->first();
    }

    /**
    * This method created the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function create(array $data) {
        event( new IamBackendCreatingEvent() );

        try {
            $model = IamBackend::create($data);
        } catch(\Exception $e) {
            throw $e;
        }

        event( new IamBackendCreatedEvent($model) );

        return $model;
    }

    /**
    * This method updated the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function update($id, array $data) {
        $model = IamBackend::where('uuid', $id)->first();

        event( new IamBackendsUpdateingEvent($model) );

        try {
           $model = $model->update($data);
        } catch(\Exception $e) {
           throw $e;
        }

        event( new IamBackendsUpdatedEvent($model) );

        return $model;
    }

    /**
    * This method updated the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function delete($id, array $data) {
        $model = IamBackend::where('uuid', $id)->first();

        event( new IamBackendsDeletingEvent() );

        try {
            $model = $model->delete();
        } catch(\Exception $e) {
            throw $e;
        }

        event( new IamBackendsDeletedEvent($model) );

        return $model;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}