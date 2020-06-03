<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
    * お気に入り投稿に追加するアクション
    *
    * @param $id 投稿ID
    * @return \Illuminate\Http\Response
    */
    public function store($id)
    {
        // 認証済みユーザ（閲覧者）が、idの投稿をお気に入りに追加する
        \Auth::user()->favorite($id);
        // 前のURLへリダイレクト
        return back();
    }
    
    /**
    * お気に入り投稿を削除するアクション
    *
    * @param $id 投稿ID
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        // 認証済みユーザ（閲覧者）が、idの投稿をお気に入りから削除する
        \Auth::user()->unfavorite($id);
        // 前のURLへリダイレクト
        return back();
    }
}
