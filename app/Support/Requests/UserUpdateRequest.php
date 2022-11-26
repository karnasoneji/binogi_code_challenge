<?php

namespace App\Support\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *   schema="UserUpdateRequest",
 *   description="User Update Request Body",
 *   @OA\Property(
 *      property="name",
 *      type="string",
 *      example="Jane Doe",
 *      description="User name",
 *      minLength=1,
 *      maxLength=191,
 *   ),
 *   @OA\Property(
 *      property="email",
 *      type="string",
 *      minLength=1,
 *      maxLength=191,
 *      description="User email",
 *      example="JaneDoe@email.com",
 *   ),
 *   @OA\Property(
 *      property="password",
 *      type="string",
 *      minLength=1,
 *      maxLength=191,
 *      description="User Password",
 *      example="correct horse battery staple",
 *   ),
 *   @OA\Property(
 *      property="nickname",
 *      type="string",
 *      minLength=1,
 *      maxLength=29,
 *      description="Nick Name",
 *      example="lalu",
 *   ),
 * )
 *
 * Get the validation rules that apply to the request.
 *
 * @return array
 */
class UserUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /**
         * When invalid parameters are passed such as special characters, the request body automatically become empty.
         *
         * So technically, it does pass the validation, but that is not correct.
         *
         * Thus, to make sure that at-leaat one of the parameters is present in the request body, we have used "required_without_all"
         *
         */

        return [
            'name'     => 'required_without_all:password,email,nickname|string|max:191|min:1',
            'password' => 'required_without_all:name,email,nickname|string|min:8|max:191',
            'email'    => [
                'email',
                Rule::unique('users')->ignore(request()->route('user')->id),
                'required_without_all:name,password,nickname',
            ],
            'nickname'    => [
                'nullable',
                'string',
                Rule::unique('users')->ignore(request()->route('user')->id),
                'max:29'
            ],
        ];
    }
}
