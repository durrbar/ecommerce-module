<?php

namespace Modules\Ecommerce\Http\Resources;

use Illuminate\Http\Request;
use Modules\Core\Http\Resources\Resource;
use Modules\User\Http\Resources\UserResource;

class ConversationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->when($this->needToInclude($request, 'conversation.user'), function () {
                return $this->whenLoaded('user', fn () => new UserResource($this->user));
            }),
            'to_user_id' => $this->to_user_id,
            'to_user' => $this->when($this->needToInclude($request, 'conversation.to_user'), function () {
                return $this->whenLoaded('to_user', fn () => new UserResource($this->to_user));
            }),
            'shop_id' => $this->shop_id,
            'shop' => $this->when($this->needToInclude($request, 'conversation.shop'), function () {
                return $this->whenLoaded('shop', fn () => new ShopResource($this->shop));
            }),
            'messages' => $this->when($this->needToInclude($request, 'conversation.messages'), function () {
                return $this->whenLoaded('messages', fn () => new MessageResourceCollection($this->messages));
            }),
            'latest_message' => new MessageResource($this->latest_message),
            'unseen' => $this->unseen,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
