<?php
namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{
    private function successResponse($data, $code) {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code) {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200) {
//        $transformer = $collection->first()->transformer;
//        $collection = $this->transformData($collection, $transformer);
        return $this->successResponse(['data' => $collection, 'code' => $code], $code);
    }

    protected function showOne(Model $model, $code = 200) {
//        $transformer = $model->transformer;
//        $model = $this->transformData($model, $transformer);
        return $this->successResponse(['data' => $model, 'code' =>  $code], $code);
    }

    protected function countAll(Collection $collection, $code = 200) {
        return $this->successResponse(['Count of data' => count($collection)], $code);
    }

    protected function showMessage($message, $code = 200) {
        return $this->successResponse($message, $code);
    }

    protected function transformData($data, $transformer) {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }
}