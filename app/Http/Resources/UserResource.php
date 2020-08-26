<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray( $request ) {
        // return parent::toArray( $request );

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'username'          => $this->username,
            'email'             => $this->email,
            'create_date'       => [
                'created_date_human' => $this->created_at->diffForHumans(),
                'created_at'         => $this->created_at,
            ],
            'about'             => $this->about,
            'location'          => $this->location,
            'tagline'           => $this->tagline,
            'formatted_address' => $this->formatted_address,
        ];
    }
}