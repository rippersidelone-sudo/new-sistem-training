<?php
//app/Helpers/RoleHelper.php

namespace App\Helpers;

class RoleHelper
{
    // Role constants
    const HQ_ADMIN = 'HQ Admin';
    const COORDINATOR = 'Training Coordinator';
    const TRAINER = 'Trainer';
    const BRANCH_COORDINATOR = 'Branch Coordinator';
    const PARTICIPANT = 'Participant';

    /**
     * Check if user has specific role
     */
    public static function hasRole($user, string $roleName): bool
    {
        return $user && $user->role && $user->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public static function hasAnyRole($user, array $roles): bool
    {
        return $user && $user->role && in_array($user->role->name, $roles);
    }

    /**
     * Check if user is HQ Admin
     */
    public static function isHQAdmin($user): bool
    {
        return self::hasRole($user, self::HQ_ADMIN);
    }

    /**
     * Check if user is Coordinator
     */
    public static function isCoordinator($user): bool
    {
        return self::hasRole($user, self::COORDINATOR);
    }

    /**
     * Check if user is Trainer
     */
    public static function isTrainer($user): bool
    {
        return self::hasRole($user, self::TRAINER);
    }

    /**
     * Check if user is Branch Coordinator
     */
    public static function isBranchCoordinator($user): bool
    {
        return self::hasRole($user, self::BRANCH_COORDINATOR);
    }

    /**
     * Check if user is Participant
     */
    public static function isParticipant($user): bool
    {
        return self::hasRole($user, self::PARTICIPANT);
    }

    /**
     * Check if user has admin privileges (HQ Admin or Coordinator)
     */
    public static function isAdmin($user): bool
    {
        return self::hasAnyRole($user, [self::HQ_ADMIN, self::COORDINATOR]);
    }

    /**
     * Get user's role name
     */
    public static function getRoleName($user): ?string
    {
        return $user && $user->role ? $user->role->name : null;
    }
}
