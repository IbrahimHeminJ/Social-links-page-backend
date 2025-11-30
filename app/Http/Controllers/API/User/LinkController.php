<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\LinkButtonStoreRequest;
use App\Http\Resources\User\UserButtonResource;
use App\Models\ButtonLink;
use App\Models\DomainBlock;
use App\Models\UserButton;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $links = UserButton::query()
        ->where('user_id',$request->user()->id)
        ->with('buttonLink','user')
        ->orderBy('order','asc')
        ->get();
        return $this->success(
            'Links fetched successfully',
            UserButtonResource::collection($links)
        );
    }
    public function show($id)
    {
        $link = UserButton::findOrFail($id);
        if(!$link){
            return $this->error('Link not found', 404);
        }
        return $this->success(
            'Link fetched successfully',
            new UserButtonResource($link)
        );
    }
    public function store(LinkButtonStoreRequest $request)
    {
        $user = $request->user();
        
        // Check if user already has 20 links
        $linkCount = UserButton::where('user_id', $user->id)->count();
        if ($linkCount >= 20) {
            return $this->error('You have reached the maximum limit of 20 links. Please delete a link before adding a new one.', 422);
        }
        
        // Check if domain is blocked
        $linkUrl = $request->validated()['link'];
        $domain = $this->extractDomain($linkUrl);
        if ($this->isDomainBlocked($domain)) {
            return $this->error('This domain is blocked and cannot be used for links.', 422);
        }
        
        // Get the last order number for this user and increment it
        $lastOrder = UserButton::where('user_id', $user->id)->max('order') ?? 0;
        $newOrder = $lastOrder + 1;
        
        $linkButton = ButtonLink::create($request->validated());
        UserButton::create([
            'user_id' => $user->id,
            'button_id' => $linkButton->id,
            'order' => $newOrder,
        ]);
        return $this->success(
            'Link created successfully',
            null,
            201
        );
    }
    public function update(LinkButtonStoreRequest $request, $id)
    {
        $link = UserButton::with('buttonLink')->findOrFail($id);
        
        // Check if domain is blocked (if link is being updated)
        if (isset($request->validated()['link'])) {
            $linkUrl = $request->validated()['link'];
            $domain = $this->extractDomain($linkUrl);
            if ($this->isDomainBlocked($domain)) {
                return $this->error('This domain is blocked and cannot be used for links.', 422);
            }
        }
        
        // Update the button link if link data is provided
        if (isset($request->validated()['link'])) {
            $link->buttonLink->update($request->validated());
        }
        
        return $this->success(
            'Link updated successfully',
            null,
            200
        );
    }

    public function destroy($id)
    {
        $link = UserButton::findOrFail($id);
        $link->delete();
        return $this->success(
            'Link deleted successfully',
            null,
            204
        );
    }

    public function reorder(Request $request)
    {
        $user = $request->user();
        
        // Validate the request
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:user_buttons,id',
        ]);
        
        $orderIds = $request->input('order');
        
        // Verify all links belong to the authenticated user
        $userLinkIds = UserButton::where('user_id', $user->id)
            ->whereIn('id', $orderIds)
            ->pluck('id')
            ->toArray();
        
        if (count($userLinkIds) !== count($orderIds)) {
            return $this->error('Some links do not belong to you or do not exist.', 403);
        }
        
        // Update the order based on the array position
        foreach ($orderIds as $index => $linkId) {
            UserButton::where('id', $linkId)
                ->where('user_id', $user->id)
                ->update(['order' => $index + 1]);
        }
        
        // Return the updated links in the new order
        $links = UserButton::where('user_id', $user->id)
            ->whereIn('id', $orderIds)
            ->with('buttonLink', 'user')
            ->get()
            ->sortBy(function ($link) use ($orderIds) {
                return array_search($link->id, $orderIds);
            })
            ->values();
        
        return $this->success(
            'Links reordered successfully',
            UserButtonResource::collection($links)
        );
    }

    /**
     * Extract domain from URL
     */
    private function extractDomain($url)
    {
        // Add protocol if missing
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'http://' . $url;
        }
        
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        
        // Remove www. prefix if present
        $host = preg_replace('/^www\./', '', $host);
        
        return strtolower($host);
    }

    /**
     * Check if domain is blocked
     */
    private function isDomainBlocked($domain)
    {
        return DomainBlock::where('domain', $domain)->exists();
    }
}
