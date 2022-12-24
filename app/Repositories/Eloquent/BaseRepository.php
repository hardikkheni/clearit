<?php

namespace App\Repositories\Eloquent;

use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BaseRepository implements EloquentRepositoryInterface
{
    /**      
     * @var Model      
     */
    protected $model;

    const GetAdjustedDatetimeFunction = 'getAdjustedDatetime';

    /**      
     * BaseRepository constructor.      
     *      
     * @param Model $model      
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModelLabel()
    {
        return defined('static::MODEL_LABEL') ? constant('static::MODEL_LABEL') : 'Data';
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param $id
     * @return Model
     */
    public function findById($id)
    {
        return $this->model->find((int) $id);
    }

    public function findOrFail($id)
    {
        $row = $this->model->find((int) $id);
        if (!$row) throw new ModelNotFoundException($this->getModelLabel() . " $id not found!.");
        return $row;
    }

    /**
     * 
     * @param array  $where 
     * @param array  $sort
     * 
     * @return  object
     */
    public function findAndOrSort(array $where = [], array $sorts = [])
    {
        $query = $this->model->query();
        if ($where) {
            $query->where($where);
        }
        if (count($sorts) > 0) {
            foreach ($sorts as $sort) {
                $query->orderBy(...$sort);
            }
        }
        return $query;
    }

    /**
     * 
     * @param array  $where 
     * @param array  $sort
     * @param int  $offset
     * @param int  $limit
     * 
     * @return  object
     */
    public function find(array $where = [], array $sorts = [], int $offset = 0, int $limit = null)
    {
        $query = $this->findAndOrSort($where, $sorts);
        if ($offset && $limit) {
            $query->offset($offset)->limit($limit);
        }
        return $query->get();
    }

    /**
     * 
     * @param array  $where 
     * @param array  $sort
     * @param int  $offset
     * 
     * @return  object
     */
    public function findOne(array $where = [], array $sorts = [], int $offset = 0)
    {
        $query = $this->findAndOrSort($where, $sorts);
        if ($offset) {
            $query->offset($offset)->limit(1);
        }
        return $query->first();
    }

    /**
     * 
     * @param array  $where 
     * @param array  $data
     * 
     * // TODO: Replace ```model->where($where)->update($data);``` with
     *          ```update($where, $data)``` in all Repositories
     * @return  object
     */
    public function update($where, $data)
    {
        return $this->model->where($where)->update($data);
    }

    /**
     * 
     * @param array  $where 
     * @param array  $data
     * 
     * @return  object
     */
    public function upsert($where, $data)
    {
        return $this->model->updateOrCreate($where, $data);
    }

    public function call(string $name, array $params = [])
    {
        $pdo = DB::getPdo();
        $query = sprintf("CALL `%s`(%s);", $name, implode(", ", array_fill(0, count($params), '?')));
        $stmt = $pdo->prepare($query, [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL]);
        $stmt->execute($params);
        $results = [];
        do {
            $results[] = $stmt->fetchAll(\PDO::FETCH_OBJ);
        } while ($stmt->nextRowset());
        if (count($results) > 0) {
            unset($results[count($results) - 1]);
        }
        return $results;
    }
}
