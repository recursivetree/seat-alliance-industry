<?php
namespace RecursiveTree\Seat\AllianceIndustry\Policies;

class UserPolicy
{
    public static function checkUser($user,$user_id){
        return $user->id === $user_id || $user->can("global.superuser") || $user->can("allianceindustry.admin");
    }
}