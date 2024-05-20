<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\GroupTrait;

class GroupResource extends JsonResource
{
	use GroupTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   	public function toArray($request)
	{
		$data = [
			'id' => $this->id,
			'name' => $this->name,
			'image' => $this->image,
			'users_count' => $this->users_count,
			'posts_count' => $this->topics_count,
			'status' => $this->getUserStatus($this->users),
			'mute_status' =>$this->getMuteStatus($this->users),
			
		];

		return $data;
	}

}
