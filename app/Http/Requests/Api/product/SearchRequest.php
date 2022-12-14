<?php

namespace App\Http\Requests\Api\product;

use App\Http\Controllers\Api\Traits\Api_Response;
use App\Http\Resources\Product\IndexResource;
use App\Models\Product;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchRequest extends FormRequest
{
    use Api_Response;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function run()
    {
        try {
            $words = $this->toSearch;
            $products = Product::where('name', 'like', "%$words%")->paginate(config('constants.CUSTOMER_PAGINATION'));
            if (!$products)
                return $this->apiResponse(null, 404, __('messages.product.found'));
            return IndexResource::collection($products);
        } catch (Exception $ex) {
            return $this->apiResponse(null, 500, $ex->getMessage());
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'toSearch' => 'required|max:255|string'
        ];
    }

    public function messages()
    {
        return [
            'toSearch.required' => __('messages.product.toSearch.required'),
            'toSearch.max' => __('messages.product.toSearch.max'),
            'toSearch.string' => __('messages.product.toSearch.string'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->apiResponse(null, 422, $validator->errors()));
    }
}
