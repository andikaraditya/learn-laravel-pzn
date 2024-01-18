<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddreessCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact)
    {
        $contact = Contact::where("user_id", $user->id)->where("id", $idContact)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress)
    {
        $address = Address::where("contact_id", $contact->id)->where("id", $idAddress)->first();

        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function create(int $idContact, AddreessCreateRequest $request)
    {
        $user = Auth::user();
        
        $contact = $this->getContact($user, $idContact);

        $data = $request->validated();

        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress)
    {
        $user = Auth::user();

        $contact = $this->getContact($user, $idContact);

        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request)
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data)
        ->save();

        return new AddressResource($address);
    }
}
