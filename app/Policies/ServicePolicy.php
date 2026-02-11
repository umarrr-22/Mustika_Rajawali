<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    public function view(User $user, Service $service): Response
    {
        return $this->checkAccess($user, $service);
    }

    public function update(User $user, Service $service): Response
    {
        return $this->checkAccess($user, $service);
    }

    protected function checkAccess(User $user, Service $service): Response
    {
        $hasAccess = $user->role->name === 'admin' || 
                    $service->technician_id === $user->id ||
                    $service->courier_id === $user->id ||
                    $service->refil_id === $user->id;

        return $hasAccess
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses ke resource ini.');
    }
}