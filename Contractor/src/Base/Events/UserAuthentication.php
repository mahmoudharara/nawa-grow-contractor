<?php

 namespace Contractor\Base\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;

class UserAuthentication
{
    use SerializesModels;

    /**
     * this attribute indicates if the transaction
     * was login or logout
     *
     * @author WeSSaM
     * @var
     */
    public $logTransaction;
    /**
     * authenticated user
     *
     * @author WeSSaM
     *
     */
    public $authUser;

    /**
     * Create a new event instance.
     *
     * @param $logTransaction
     * @param null $authUser
     */
    public function __construct($logTransaction, $authUser = null)
    {
//        if (is_null($authUser)) {
//            $this->authUser = auth_user();              // if developer passed no user, get the auth_user()
//        } else {
//            $this->authUser = $authUser;
//        }
        $this->logTransaction = $logTransaction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
