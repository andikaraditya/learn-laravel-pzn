<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = Auth::user();

        $data["user_id"] = $user->id;

        $contact = new Contact($data);

        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id)
    {
        $user = Auth::user();

        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request)
    {
        $user = Auth::user();

        $data = $request->validated();

        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->fill($data);
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(200);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();

        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->delete();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
