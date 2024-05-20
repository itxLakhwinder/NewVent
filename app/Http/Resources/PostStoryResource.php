<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Traits\GroupTrait;

class PostStoryResource extends JsonResource
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
			'post_count' => $this->topics_count,
			'status' => $this->getUserStatus($this->users),
			'posts' => $this->getPost($this->topics),
			'stories' => $this->getPost($this->topics),
		];

		return $data;
	}

}
