<?php


namespace App\Http\Utils;


use Illuminate\Support\Facades\DB;

class DBHelper
{


    public static function getOffersByKeyword($requester_id, $keyword)
    {
        $sql = "
        SELECT
	t_offer.*, t_user.name as creator_name , t_user.avatar_filename as creator_avatar
FROM
	`t_offer`
	LEFT JOIN `t_user` ON t_offer.creator_id = t_user.id
	WHERE t_offer.creator_id <> $requester_id AND `t_offer`.title LIKE '%$keyword%'";


        return DB::select($sql);
    }

}

