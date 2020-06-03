<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * このユーザが所有する投稿（Micropostモデルとの関係を定義）
     */
     public function microposts()
     {
         return $this->hasMany(Micropost::class);
     }
    
    /**
    * このユーザがフォロー中のユーザ（Userモデルとの関係を定義）
    */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
     
    /**
    * このユーザをフォロー中のユーザ（Userモデルとの関係を定義）
    */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
     
    /**
    * このユーザがお気に入りの投稿（Micropostモデルとの関係を定義）
    */
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    /**
    * このユーザに関係するモデルの件数をロードする。
    * 投稿件数、フォロー数、フォロワー数、お気に入り投稿数
    */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    /**
    * $userIdで指定されたユーザをフォローする
    * 
    * @param init $userId
    * @return bool
    */ 
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    /**
    * $userIdで指定されたユーザをアンフォローする
    *
    * @param int $userId
    * @return bool
    */
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    /**
    * 指定された $userId のユーザをこのユーザがフォロー中であるか調べる。
    * フォロー中なら true を返す。
    *
    * @param int $userId
    * @return bool
    */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userId のものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /**
    * このユーザとフォロー中ユーザの投稿に絞り込む
    */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds [] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    
    
    /**
    * $micropostIdで指定された投稿をお気に入りに追加する
    * 
    * @param init $micropostId
    * @return bool
    */ 
    public function favorite($micropostId)
    {
        // すでにお気に入りに追加しているかの確認
        $exist = $this->is_favorite($micropostId);
        
        if ($exist) {
            // すでに追加していれば何もしない
            return false;
        } else {
            // なければ追加する
            $this->favorites()->attach($micropostId);
            return true;
        }
    
    }
    
    /**
    * $micropostIdで指定された投稿をお気に入りから削除する
    *
    * @param int $micropostId
    * @return bool
    */
    public function unfavorite($micropostId)
    {
        // すでにお気に入りし追加しているかの確認
        $exist = $this->is_favorite($micropostId);
        
        if ($exist) {
            // すでに追加していれば削除する
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // なければ何もしない
            return false;
        }
    }
    
    
    /**
    * 指定された $micropostId の投稿をこのユーザがお気に入りに追加しているか調べる。
    * 追加していたら true を返す。
    *
    * @param int $micropostId
    * @return bool
    */
    public function is_favorite($micropostId)
    {
        // お気に入り投稿の中に $micropostId のものが存在するか
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
    
}
