<?php

namespace App\Http\Controllers;

use App\Models\User;


class UserController extends BaseController
{
	public function index() {
      $users = User::all();
      return json_encode($users);
	}
}