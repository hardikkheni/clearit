<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface EloquentRepositoryInterface
 * @package App\Repositories
 */
interface EloquentRepositoryInterface
{
   /**
    * @param array $attributes
    * @return Model
    */
   public function create(array $attributes);

   /**
    * @param $id
    * @return Model
    */
   public function findById($id);

   /**
    * 
    * @param array  $where 
    * @param array  $sort
    * @param int  $offset
    * @param int  $limit
    * 
    * @return  object
    */
   public function find(array $where = [], array $sorts = [], int $offset = 0, int $limit = null);

   /**
    * 
    * @param array  $where 
    * @param array  $sort
    * @param int  $offset
    * 
    * @return  object
    */
   public function findOne(array $where = [], array $sorts = [], int $offset = 0);

   /**
    * 
    * @param array  $where 
    * @param array  $sort
    * 
    * @return  object
    */
   public function findAndOrSort(array $where = [], array $sorts = []);

   /**
    * 
    * @param array  $where 
    * @param array  $data
    * 
    * @return  object
    */
   public function update($where, $data);

   /**
    * 
    * @param array  $where 
    * @param array  $data
    * 
    * @return  object
    */
   public function upsert($where, $data);
}
