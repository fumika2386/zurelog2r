<?php

namespace App\Http\Controllers;

class MyPageController extends Controller
{
    public function show(\Illuminate\Http\Request $request)
    {
        $user = auth()->user()
            ->load([
                'tags' => fn($q) => $q->orderBy('sort_order'),
            ])
            ->loadCount(['posts','followers','followings']);

            $posts = $user->posts()
        ->withCount([
            'reactions as r_total'   => fn($q)=>$q,
            'reactions as r_s0'      => fn($q)=>$q->where('stamp',0),
            'reactions as r_s1'      => fn($q)=>$q->where('stamp',1),
            'reactions as r_s2'      => fn($q)=>$q->where('stamp',2),
            'reactions as r_s3'      => fn($q)=>$q->where('stamp',3),
            'reactions as r_s4'      => fn($q)=>$q->where('stamp',4),
        ])
        ->latest()->paginate(10)->withQueryString();

        return view('mypage.show', compact('user','posts'));

    }

}
