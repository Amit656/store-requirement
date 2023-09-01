<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RandomUserController extends Controller
{
    public function getRandomUsers(Request $request)
{
    // Validate the 'limit' parameter (you can customize the validation rules).
    $validator = Validator::make($request->all(), [
        'limit' => 'integer|min:1|max:50', // Adjust the validation rules as needed.
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $limit = $request->input('limit', 10); // Default to 10 if 'limit' not provided.

    // Make API requests and process data with the specified 'limit'.
    $responses = [];
        for ($i = 0; $i < $limit; $i++) {
            $response = Http::get('https://randomuser.me/api/');
            $responses[] = json_decode($response->body(), true);
        }

        // Extract and sort user data by last name.
        $users = [];
        foreach ($responses as $response) {
            $user = $response['results'][0];
            $fullName = $user['name']['first'] . ' ' . $user['name']['last'];
            $phone = $user['phone'];
            $email = $user['email'];
            $country = $user['location']['country'];
            $users[] = compact('fullName', 'phone', 'email', 'country');
        }

        // Sort users by last name in reverse alphabetical order.
        usort($users, function ($a, $b) {
            return strcmp($b['fullName'], $a['fullName']);
        });

        // Convert the sorted data to XML.
        $xmlData = new \SimpleXMLElement('<users></users>');
        foreach ($users as $userData) {
            $user = $xmlData->addChild('user');
            $user->addChild('fullName', $userData['fullName']);
            $user->addChild('phone', $userData['phone']);
            $user->addChild('email', $userData['email']);
            $user->addChild('country', $userData['country']);
        }

        // Return the XML response.
        return response($xmlData->asXML(), 200)->header('Content-Type', 'application/xml');

    // Process and return the response as before.
}

}
