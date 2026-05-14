<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'priority'    => $this->priority,
            'completed'   => $this->completed,
            'due_date'    => $this->due_date?->toDateString(),
            'board_column'=> $this->board_column,
            'color'       => $this->color,
            'tags'        => $this->tags,
            'labels'      => $this->labels,
            'creator'     => new UserResource($this->whenLoaded('creator')),
            'assignee'    => new UserResource($this->whenLoaded('assignee')),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
