<?php

namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class UserComposer
{
    public $user;

    public function __construct()
    {
        $this->user = User::where('id', auth()->user()->id)
            ->with([
                'roles' => function ($query): void {
                    $query->select('id', 'name');
                },
            ])->first(['id']);
    }

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('user', $this->user);
    }
}
